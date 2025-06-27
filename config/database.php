<?php
// Configurar timezone para Ciudad de México
date_default_timezone_set('America/Mexico_City');

$host = 'localhost';
$user = 'root';
$password = ''; // si tienes contraseña, colócala aquí
$database = 'UNIVALLE';

$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar charset y timezone en MySQL también
$conn->set_charset("utf8");
$conn->query("SET time_zone = '-06:00'"); // Timezone de Ciudad de México

?>
