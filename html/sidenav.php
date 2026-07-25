<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteger acceso
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Mi Empresa</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link">
                        Bienvenido: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="carga-tienda-en-linea.php">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="compras-aprobadas.php">Pedidos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="usuarios.php">Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" style="color: #fff;">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
