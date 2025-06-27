<?php
// Configurar timezone
date_default_timezone_set('America/Mexico_City');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

try {
    $query = "SELECT id, codigo_asignatura, nombre, creditos, horas_semanales, semestre FROM asignaturas ORDER BY semestre, nombre";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $asignaturas = array();
    while ($row = $result->fetch_assoc()) {
        $asignaturas[] = $row;
    }

    echo json_encode($asignaturas);
} catch (Exception $e) {
    echo json_encode(array(
        'error' => $e->getMessage(),
        'data' => array()
    ));
}

$conn->close();
?>
