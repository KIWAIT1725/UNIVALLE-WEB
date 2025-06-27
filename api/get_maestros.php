<?php
// Configurar timezone
date_default_timezone_set('America/Mexico_City');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

try {
    $query = "SELECT id, codigo_maestro, nombre, apellido, email, especialidad, salario FROM maestros ORDER BY nombre";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $maestros = array();
    while ($row = $result->fetch_assoc()) {
        $maestros[] = $row;
    }

    echo json_encode($maestros);
} catch (Exception $e) {
    echo json_encode(array(
        'error' => $e->getMessage(),
        'data' => array()
    ));
}

$conn->close();
?>
