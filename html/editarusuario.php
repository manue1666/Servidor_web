<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';

// Proteger acceso
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$alert = isset($_SESSION['alert']) ? $_SESSION['alert'] : null;

if (!empty($alert)) {
    $title = isset($alert['title']) ? json_encode($alert['title']) : '"Notificación"';
    $message = isset($alert['message']) ? json_encode($alert['message']) : '""';
    $icon = isset($alert['icon']) ? json_encode($alert['icon']) : '"info"';

    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: $title,
                    " . (!empty($alert['message']) ? "text: $message," : "") . "
                    icon: $icon,
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    unset($_SESSION['alert']);
}

// Obtener el ID del usuario a editar
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "SELECT * FROM usuarios WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['alert'] = [
            'title' => 'ERROR',
            'message' => 'Usuario no encontrado',
            'icon' => 'error'
        ];
        header('Location: usuarios.php');
        exit();
    }
} else {
    header('Location: usuarios.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario | Mi Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
</head>
<body>
    <?php include 'sidenav.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h4 class="m-0">Editar Usuario</h4>
                    </div>
                    <div class="card-body">
                        <form action="codeusuarios.php" method="POST">
                            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellidopaterno" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" name="apellidopaterno" value="<?= htmlspecialchars($usuario['apellidopaterno']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellidomaterno" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" name="apellidomaterno" value="<?= htmlspecialchars($usuario['apellidomaterno']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Correo</label>
                                <input type="email" class="form-control" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
                                <input type="password" class="form-control" name="password" minlength="8">
                            </div>

                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>
                                <select class="form-select" name="rol" required>
                                    <option value="1" <?= $usuario['rol'] == 1 ? 'selected' : '' ?>>Administrador</option>
                                    <option value="2" <?= $usuario['rol'] == 2 ? 'selected' : '' ?>>Colaborador</option>
                                    <option value="3" <?= $usuario['rol'] == 3 ? 'selected' : '' ?>>Cliente</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="estatus" class="form-label">Estatus</label>
                                <select class="form-select" name="estatus" required>
                                    <option value="1" <?= $usuario['estatus'] == 1 ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= $usuario['estatus'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" name="update">Guardar Cambios</button>
                                <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
