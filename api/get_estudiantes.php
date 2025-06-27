<?php
// Configurar timezone
date_default_timezone_set('America/Mexico_City');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

try {
    $query = "SELECT id, codigo_estudiante, nombre, apellido, email, telefono, fecha_registro FROM estudiantes ORDER BY fecha_registro DESC";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $estudiantes = array();
    while ($row = $result->fetch_assoc()) {
        $estudiantes[] = $row;
    }

    echo json_encode($estudiantes);
} catch (Exception $e) {
    echo json_encode(array(
        'error' => $e->getMessage(),
        'data' => array()
    ));
}

$conn->close();
?>
