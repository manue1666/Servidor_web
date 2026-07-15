<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si está logueado, ir a usuarios
if (isset($_SESSION['username'])) {
    header('Location: usuarios.php');
    exit();
}

// Si no está logueado, ir al login
header('Location: login.php');
exit();
?>
