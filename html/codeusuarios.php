<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM usuarios WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['alert'] = [
            'message' => 'Usuario eliminado exitosamente',
            'title' => 'USUARIO ELIMINADO',
            'icon' => 'success'
        ];
        header("Location: usuarios.php");
        exit(0);
    } else {
        $_SESSION['alert'] = [
            'message' => 'Notifica a soporte',
            'title' => 'ERROR AL ELIMINAR',
            'icon' => 'error'
        ];
        header("Location: usuarios.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {

    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidopaterno = mysqli_real_escape_string($con, $_POST['apellidopaterno']);
    $apellidomaterno = mysqli_real_escape_string($con, $_POST['apellidomaterno']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password']; // NO escapar todavía
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);

    // Base del update
    $query = "
        UPDATE usuarios SET
            nombre = '$nombre',
            apellidopaterno = '$apellidopaterno',
            apellidomaterno = '$apellidomaterno',
            username = '$username',
            rol = '$rol',
            estatus = '$estatus'
    ";

    // 👉 Solo si el password NO está vacío
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = '$hashed_password'";
    }

    $query .= " WHERE id = '$id'";

    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['alert'] = [
            'message' => 'Usuario editado exitosamente',
            'title' => 'USUARIO EDITADO',
            'icon' => 'success'
        ];
        header("Location: usuarios.php");
        exit;
    } else {
        $_SESSION['alert'] = [
            'message' => 'Notifica a soporte',
            'title' => 'ERROR AL EDITAR',
            'icon' => 'error'
        ];
        header("Location: usuarios.php");
        exit;
    }
}


if (isset($_POST['save'])) {

    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidopaterno = mysqli_real_escape_string($con, $_POST['apellidopaterno']);
    $apellidomaterno = mysqli_real_escape_string($con, $_POST['apellidomaterno']);
    $email = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $estatus = "1";
    $medio = '';

    // Verificar el rol y asignar el nombre correspondiente
    if ($rol == 1) {
        $rol_nombre = "Administrador";
    } elseif ($rol == 2) {
        $rol_nombre = "Colaborador";
    } elseif ($rol == 3) {
        $rol_nombre = "Cliente";
    } else {
        $rol_nombre = "Otro";
    }

    $check_email_query = "SELECT * FROM usuarios WHERE username='$email' LIMIT 1";
    $result = mysqli_query($con, $check_email_query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['alert'] = [
            'title' => 'ERROR',
            'message' => 'Este correo ya está registrado',
            'icon' => 'error'
        ];
        header("Location: usuarios.php");
        exit(0);
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO usuarios SET nombre='$nombre', apellidopaterno='$apellidopaterno', apellidomaterno='$apellidomaterno', username='$email', password='$hashed_password', rol='$rol', estatus='$estatus', medio='$medio'";

        $query_run = mysqli_query($con, $query);
        if ($query_run) {

            // Configuración SMTP por variables de entorno
            $host = getenv('SMTP_HOST') ?: 'mailpit';
            $port = (int) (getenv('SMTP_PORT') ?: 1025);
            $smtp_user = getenv('SMTP_USER') ?: '';
            $smtp_password = getenv('SMTP_PASSWORD') ?: '';
            $security = getenv('SMTP_ENCRYPTION') ?: '';
            $from_email = getenv('SMTP_FROM_EMAIL') ?: 'no-reply@local.test';
            $from_name = getenv('SMTP_FROM_NAME') ?: 'Mi Empresa';


            // Crear instancia PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configurar SMTP
                $mail->isSMTP();
                $mail->Host = $host;
                $mail->Port = $port;
                $mail->SMTPAuth = $smtp_user !== '';
                if ($smtp_user !== '') {
                    $mail->Username = $smtp_user;
                    $mail->Password = $smtp_password;
                }
                if ($security !== '') {
                    $mail->SMTPSecure = $security;
                }

                // Configurar correo
                $mail->setFrom($from_email, $from_name);
                $mail->addAddress($email);
                $mail->Subject = 'NUEVO USUARIO CREADO';
                $mail->CharSet = 'UTF-8';
                $mail->isHTML(true);

                // Cuerpo del mensaje
                $cuerpo = '
                    <html>
                    <head>
                        <meta charset="UTF-8">
                    </head>
                    <body style="font-family: Arial, sans-serif; text-align: justify; background-color: #e7e7e7;">
                        <div style="max-width: 500px; margin: 0 auto;">
                            <div style="background-color: #1e375c; padding: 20px; color: white; text-align: center;">
                                <h2>Bienvenido a Mi Empresa</h2>
                            </div>
                            <div style="padding: 30px; background-color: white;">
                                <p>Estimado/a <strong>' . htmlspecialchars($nombre) . '</strong></p>
                                <p>Tu cuenta ha sido creada exitosamente.</p>
                                <p>Por seguridad no compartas tus credenciales con nadie.</p>

                                <div style="padding: 20px; background-color: #efefef; border-radius: 3px; margin: 30px 0;">
                                    <p><strong>Detalles de tu cuenta:</strong></p>
                                    <p><strong>Nombre:</strong> ' . htmlspecialchars($nombre . ' ' . $apellidopaterno . ' ' . $apellidomaterno) . '</p>
                                    <p><strong>Correo:</strong> ' . htmlspecialchars($email) . '</p>
                                    <p><strong>Contraseña:</strong> ' . htmlspecialchars($password) . '</p>
                                    <p><strong>Rol:</strong> ' . htmlspecialchars($rol_nombre) . '</p>
                                </div>

                                <p style="text-align: center; margin-top: 50px; color: #666;">Atentamente,<br><strong>Equipo administrativo</strong></p>
                            </div>
                            <div style="background-color: #af3335; color: white; padding: 15px; text-align: center; font-size: 12px;">
                                <p>Este correo es enviado de manera automática. No responder a este mensaje.</p>
                            </div>
                        </div>
                    </body>
                    </html>';

                $mail->Body = $cuerpo;
                $correoEnviado = $mail->send();

                $_SESSION['alert'] = [
                    'title' => 'ÉXITO',
                    'message' => 'Usuario creado y correo enviado correctamente',
                    'icon' => 'success'
                ];
            } catch (Exception $e) {
                $_SESSION['alert'] = [
                    'title' => 'ADVERTENCIA',
                    'message' => 'Usuario creado pero no se pudo enviar el correo: ' . $mail->ErrorInfo,
                    'icon' => 'warning'
                ];
            }

            header("Location: usuarios.php");
            exit(0);
        } else {
            $_SESSION['alert'] = [
                'title' => 'ERROR',
                'message' => 'Notifica a soporte',
                'icon' => 'error'
            ];
            header("Location: usuarios.php");
            exit(0);
        }
    }
}