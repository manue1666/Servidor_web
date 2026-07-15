<?php
// Configuración de conexión a MySQL
$host = 'mysql';
$user = 'root';
$password = 'root123';
$database = 'mibd';

// Crear conexión
$con = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

// Establecer charset a UTF-8
$con->set_charset("utf8mb4");
?>
