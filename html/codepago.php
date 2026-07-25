<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/vendor/autoload.php';

use Openpay\Data\Openpay;
use Openpay\Data\OpenpayApiTransactionError;
use Openpay\Data\OpenpayApiRequestError;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'dbcon.php';

if (isset($_POST['update'])) {

    if (!isset($_POST['identificador']) || empty($_POST['identificador'])) {
        die('Identificador no recibido');
    }

    $identificador = $_POST['identificador'];

    $stmt = $con->prepare("
        SELECT nombre, apellidop, apellidom, email, telefono, total
        FROM pedidos
        WHERE identificador = ?
        LIMIT 1
    ");

    if (!$stmt) {
        die($con->error);
    }

    $stmt->bind_param("s", $identificador);
    $stmt->execute();

    $stmt->bind_result(
        $nombre,
        $apellidop,
        $apellidom,
        $email,
        $telefono,
        $total
    );

    if (!$stmt->fetch()) {
        die('Pedido no encontrado');
    }

    $stmt->close();

    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    $openpay = Openpay::getInstance(
        $_ENV['OPENPAY_MERCHANT_ID'],
        $_ENV['OPENPAY_PRIVATE_KEY'],
        'MX',
        $clientIp
    );

    if (($_ENV['OPENPAY_SANDBOX'] ?? 'true') === 'true') {
        Openpay::setProductionMode(false);
    } else {
        Openpay::setProductionMode(true);
    }

    $customer = [
        'name'         => $pedido['nombre'] ?? $nombre,
        'last_name'    => trim($apellidop . ' ' . $apellidom),
        'phone_number' => $telefono,
        'email'        => $email,
    ];

    $method = $_POST['payment_method'];

    $montoFinal = number_format((float)$total, 2, '.', '');

    try {
        if ($method === 'card') {

            $chargeData = array(
                'method'            => 'card',
                'source_id'         => $_POST["token_id"],
                'amount'            => $montoFinal,
                'description'       => 'Pedido #' . $identificador,
                'order_id'          => $identificador . '_' . time(),
                'device_session_id' => $_POST["deviceIdHiddenFieldName"],
                'customer'          => $customer,
            );
        } else {

            $chargeData = array(
                'method'      => 'bank_account',
                'amount'      => $montoFinal,
                'description' => 'Pedido #' . $identificador,
                'order_id'    => $identificador . '_' . time(),
                'customer'    => $customer
            );
        }
        $charge = $openpay->charges->create($chargeData);

        if ($method === 'bank_account') {

            $vigencia = $charge->due_date;
            $bank = $charge->payment_method->bank;
            $clabe = $charge->payment_method->clabe;
            $convenio = $charge->payment_method->agreement;
            $referencia = $charge->payment_method->name;
            $url_pdf = $charge->payment_method->url_spei;

            $fechaObj = new DateTime($vigencia);
            $meses = ['January'=>'enero','February'=>'febrero','March'=>'marzo','April'=>'abril','May'=>'mayo','June'=>'junio','July'=>'julio','August'=>'agosto','September'=>'septiembre','October'=>'octubre','November'=>'noviembre','December'=>'diciembre'];
            $mesEsp = $meses[$fechaObj->format('F')] ?? $fechaObj->format('F');
            $vigenciaAmigable = $fechaObj->format('d') . ' de ' . $mesEsp . ' de ' . $fechaObj->format('Y') . ', ' . $fechaObj->format('H:i');

            $update_stmt = $con->prepare("UPDATE pedidos SET 
                status_pago = 'Pendiente SPEI', 
                openpay_id = ?, 
                pdf_url = ?, 
                clabe = ?,
                vigencia = ?,
                banco = ?,
                convenio = ?,
                referencia = ? 
                WHERE identificador = ?");

            $chargeId = (string) $charge->id;
            $update_stmt->bind_param("ssssssss", $chargeId, $url_pdf, $clabe, $vigenciaAmigable, $bank, $convenio, $referencia, $identificador);
            $update_stmt->execute();

            sendSPEIEmail($identificador, $email, $bank, $clabe, $convenio, $referencia, $url_pdf, $montoFinal, $vigenciaAmigable);
            header("Location: orden.php?id=" . $identificador);
            exit();

        } else {

            if ($charge->status == 'completed') {

                $chargeId = (string) $charge->id;
                $update_stmt = $con->prepare("UPDATE pedidos SET status_pago = 'Pagado', openpay_id = ? WHERE identificador = ?");
                $update_stmt->bind_param("ss", $chargeId, $identificador);
                $update_stmt->execute();

                header("Location: orden.php?id=" . $identificador);
                exit();

            } else if ($charge->status == 'charge_pending') {

                header("Location: " . $charge->payment_method->url);
                exit();

            } else {

                $_SESSION['alert'] = [
                    'title'   => 'PAGO NO COMPLETADO',
                    'message' => 'El pago no pudo ser procesado. Status: ' . $charge->status,
                    'icon'    => 'warning'
                ];
                header("Location: pago.php?id=$identificador");
                exit();
            }
        }
    } catch (OpenpayApiTransactionError $e) {
        handleOpenpayError($e, $identificador);
    } catch (OpenpayApiRequestError $e) {
        handleOpenpayError($e, $identificador);
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'title'   => 'ERROR DEL SISTEMA',
            'message' => 'Contacta a soporte: ' . $e->getMessage(),
            'icon'    => 'error'
        ];
        header("Location: pago.php?id=$identificador");
        exit();
    }

    exit();
}

function handleOpenpayError($e, $identificador)
{
    $errorCode = $e->getErrorCode();

    switch ($errorCode) {
        case 3001:
            $message = 'La tarjeta fue rechazada';
            break;
        case 3002:
            $message = 'La tarjeta ha expirado';
            break;
        case 3003:
            $message = 'Fondos insuficientes';
            break;
        case 3004:
            $message = 'La tarjeta fue rechazada';
            break;
        case 3005:
            $message = 'La tarjeta fue rechazada';
            break;
        case 2005:
            $message = 'La fecha de expiración es incorrecta';
            break;
        case 15001:
            $message = 'La autenticación de la tarjeta falló. Por favor, intenta con otro método de pago o contacta a tu banco.';
            break;
        default:
            $message = 'Error (' . $errorCode . '): ' . $e->getMessage();
            break;
    }

    $_SESSION['alert'] = [
        'title'   => 'PAGO NO APROBADO',
        'message' => $message,
        'icon'    => 'error'
    ];

    header("Location: pago.php?id=$identificador");
    exit();
}

function sendSPEIEmail($identificador, $email, $bank, $clabe, $convenio, $referencia, $url_pdf, $total, $vigenciaAmigable)
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->Port = (int)$_ENV['SMTP_PORT'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USER'];
    $mail->Password = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];

    $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
    $mail->addAddress($email);
    $mail->Subject = 'Realiza tu pago por SPEI - Pedido #' . $identificador;
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);

    $body = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="margin:0; padding:0; background:#ffffff; font-family:Arial, sans-serif;">
        <div style="background-color: #f3f3f3; max-width: 600px; margin: 0px auto; text-align: center; line-height: 100px;">
            <h2 style="padding: 10px 0;">FASTPACK INDUSTRIAL</h2>
        </div>
        <div style="max-width:600px; background:#ffffff; margin:0px auto 10px; padding:15px;">
            <h1 style="font-size:25px; margin:30px 0; text-align:left;">REALIZA TU PAGO POR SPEI</h1>
            <p>Estas a un paso de finalizar tu pedido, realiza tu pago por SPEI antes del <strong>' . $vigenciaAmigable . '</strong> con los siguientes datos:</p>
            <div style="background: #2c3b5c; color:#fff; padding:15px; border-radius:3px; margin:30px 0;">
                <p><strong>Beneficiario:</strong> FASTPACK INDUSTRIAL S.A. DE C.V.</p>
                <p><strong>Concepto:</strong> Pedido #' . $identificador . '</p>
                <p><strong>Total a pagar:</strong> $' . number_format((float)$total, 2) . '</p>
                <p><strong>Banco:</strong> ' . htmlspecialchars($bank, ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Referencia:</strong> ' . htmlspecialchars(implode(' ', str_split($referencia, 4)), ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>CLABE (Con otros bancos):</strong> ' . htmlspecialchars(implode(' ', str_split($clabe, 4)), ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Convenio CIE (Con BBVA):</strong> ' . htmlspecialchars(implode(' ', str_split($convenio, 3)), ENT_QUOTES, 'UTF-8') . '</p>
            </div>
            <p style="text-align:center;"><strong>FASTPACK INDUSTRIAL</strong></p>
            <p style="font-size:8px; color:#555;">Este es un email enviado automaticamente desde el sistema de Fastpack Industrial.</p>
        </div>
    </body>
    </html>';

    $mail->Body = $body;
    try {
        $mail->send();
    } catch (Exception $e) {
        error_log('Error correo SPEI: ' . $e->getMessage());
    }
}
