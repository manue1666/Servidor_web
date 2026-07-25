<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'vendor/autoload.php';
require 'dbcon.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: tienda-en-linea.php');
    exit;
}

$stmt = $con->prepare("
    SELECT *
    FROM pedidos
    WHERE identificador = ?
    LIMIT 1
");

if (!$stmt) {
    die($con->error);
}

$stmt->bind_param('s', $_GET['id']);
$stmt->execute();
$resultado = $stmt->store_result();

if ($resultado === false || $stmt->num_rows === 0) {
    header('Location: tienda-en-linea.php');
    exit;
}

$pedido = [];
$meta = $stmt->result_metadata();
$fields = [];

while ($field = $meta->fetch_field()) {
    $fields[] = &$pedido[$field->name];
}

call_user_func_array([$stmt, 'bind_result'], $fields);
$stmt->fetch();

$ventas = [];
$stmtVentas = $con->prepare("
    SELECT titulo, sku, cantidad, precio, descuento
    FROM ventas
    WHERE identificador = ?
");

if ($stmtVentas) {
    $stmtVentas->bind_param('s', $pedido['identificador']);
    $stmtVentas->execute();
    $stmtVentas->bind_result($titulo, $sku, $cantidad, $precio, $descuento);

    while ($stmtVentas->fetch()) {
        $ventas[] = [
            'titulo'    => $titulo,
            'sku'       => $sku,
            'cantidad'  => $cantidad,
            'precio'    => $precio,
            'descuento' => $descuento
        ];
    }
}

$statusPago = strtolower($pedido['status_pago'] ?? '');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
    <title>Mi Pedido | Fastpack Industrial</title>
    <style>
        body { background-color: #ecf0f3; }
        .spei-box { background: #2c3b5c; color: #fff; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .spei-box p { margin-bottom: 8px; }
        .spei-box strong { color: #ffd700; }
        .success-box { background: #d4edda; border: 2px solid #28a745; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .pending-box { background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
    </style>
</head>

<body>
    <?php include('menu.php'); ?>

    <div class="container-fluid bg-light">
        <div class="row mt-5 justify-content-center">

            <div class="col-11 col-md-8 mt-5 mb-5 p-5">

                <?php if ($statusPago === 'pagado'): ?>

                    <div class="success-box">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 60px;"></i>
                        <h2 class="mt-3">PAGO RECIBIDO</h2>
                        <p class="mb-0">Tu pago ha sido procesado exitosamente.</p>
                        <p><small>Tu pedido estara en camino pronto.</small></p>
                    </div>

                <?php elseif ($statusPago === 'pendiente spei'): ?>

                    <h2><i class="bi bi-bank"></i> INSTRUCCIONES DE PAGO SPEI</h2>
                    <p>Realiza una transferencia bancaria con los siguientes datos antes del <strong><?= htmlspecialchars($pedido['vigencia'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>:</p>

                    <div class="spei-box">
                        <p><strong>Banco:</strong> <?= htmlspecialchars($pedido['banco'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>CLABE:</strong> <?= htmlspecialchars(implode(' ', str_split($pedido['clabe'] ?? '', 4)), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Convenio CIE:</strong> <?= htmlspecialchars(implode(' ', str_split($pedido['convenio'] ?? '', 3)), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Referencia:</strong> <?= htmlspecialchars(implode(' ', str_split($pedido['referencia'] ?? '', 4)), ENT_QUOTES, 'UTF-8') ?></p>
                        <hr style="border-color: rgba(255,255,255,0.3);">
                        <p><strong>Total a pagar:</strong> $<?= number_format((float)$pedido['total'], 2) ?></p>
                        <p><strong>Pedido:</strong> <?= htmlspecialchars($pedido['identificador'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>

                    <?php if (!empty($pedido['pdf_url'])): ?>
                        <p><a href="<?= htmlspecialchars($pedido['pdf_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-pdf"></i> Descargar comprobante SPEI
                        </a></p>
                    <?php endif; ?>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i>
                        Una vez realizado el pago, sera verificado automaticamente. Si tienes dudas contactanos.
                    </div>

                <?php else: ?>

                    <div class="pending-box">
                        <i class="bi bi-clock-history text-warning" style="font-size: 60px;"></i>
                        <h2 class="mt-3">PEDIDO EN PROCESO</h2>
                        <p class="mb-0">Tu pedido esta siendo procesado.</p>
                        <p><small>Status: <?= htmlspecialchars($pedido['status_pago'] ?? 'Sin definir', ENT_QUOTES, 'UTF-8') ?></small></p>
                    </div>

                <?php endif; ?>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Datos del pedido</h5>
                        <p><b>ID:</b> <span class="small"><?= htmlspecialchars($pedido['identificador'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                        <p><b>Fecha:</b> <?= htmlspecialchars($pedido['fecha'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mb-0"><b>Recibe:</b></p>
                        <p class="small mb-0"><?= htmlspecialchars($pedido['nombre'], ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($pedido['apellidop'], ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($pedido['apellidom'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="small mb-0"><?= htmlspecialchars($pedido['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="small"><?= htmlspecialchars($pedido['telefono'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Envio a</h5>
                        <p class="small"><?= htmlspecialchars($pedido['calle'], ENT_QUOTES, 'UTF-8'); ?> #<?= htmlspecialchars($pedido['exterior'], ENT_QUOTES, 'UTF-8'); ?>, <?= htmlspecialchars($pedido['colonia'], ENT_QUOTES, 'UTF-8'); ?>, <?= htmlspecialchars($pedido['ciudad'], ENT_QUOTES, 'UTF-8'); ?>, <?= htmlspecialchars($pedido['estado'], ENT_QUOTES, 'UTF-8'); ?>. CP <?= htmlspecialchars($pedido['postal'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>

                <h5>Tus productos</h5>
                <?php foreach ($ventas as $item): ?>
                    <div class="row">
                        <div class="col-9">
                            <p class="mb-1">
                                <strong><?= (int)$item['cantidad'] ?> x <?= htmlspecialchars($item['titulo']) ?></strong>
                            </p>
                            <p class="mb-0 small text-muted">SKU: <?= htmlspecialchars($item['sku']) ?></p>
                        </div>
                        <div class="col-3 text-end">
                            <?php
                            $monto = $item['cantidad'] * $item['precio'];
                            $disc = $item['cantidad'] * $item['descuento'];
                            ?>
                            <p class="mb-0 small">$<?= number_format($monto, 2) ?></p>
                            <?php if ($disc > 0): ?>
                                <p class="mb-0 small text-success">-$<?= number_format($disc, 2) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>

                <div class="text-end">
                    <p><b>Subtotal:</b> $<?= number_format((float)$pedido['subtotal'], 2); ?></p>
                    <?php if (($pedido['cuponMonto'] ?? 0) > 0): ?>
                        <p class="text-success"><b>Cupon:</b> -$<?= number_format((float)$pedido['cuponMonto'], 2); ?></p>
                    <?php endif; ?>
                    <p><b>Envio:</b> <?= ($pedido['envioMonto'] ?? 0) > 0 ? '$' . number_format((float)$pedido['envioMonto'], 2) : 'GRATIS'; ?></p>
                    <p style="font-weight: 500;"><b>Total:</b> $<?= number_format((float)$pedido['total'], 2); ?></p>
                </div>

                <div class="text-center mt-4">
                    <a href="tienda-en-linea.php" class="btn btn-primary">Seguir comprando</a>
                </div>
            </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="js/menu.js"></script>
</body>

</html>
