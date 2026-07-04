<?php
// Configuración de conexión a MySQL
$host = 'mysql';
$user = 'root';
$password = 'root123';
$database = 'mibd';

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

echo "<h1>¡Entorno Docker XAMPP funcionando!</h1>";
echo "<p>Conectado a MySQL correctamente</p>";

// Obtener usuarios de la base de datos
$sql = "SELECT id, nombre, email FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Usuarios en la BD:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["nombre"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No hay usuarios registrados";
}

// Información del sistema
echo "<hr>";
echo "<h2>Información:</h2>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";
echo "<p>Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

$conn->close();
?>