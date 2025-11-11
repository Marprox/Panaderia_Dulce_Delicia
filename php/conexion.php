<?php
$servername = "localhost";
$username = "root";
$password = "";  // XAMPP por defecto no tiene contraseña
$database = "panaderia";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8mb4");
?>