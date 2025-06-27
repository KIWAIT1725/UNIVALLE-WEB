<?php
// Configurar timezone
date_default_timezone_set('America/Mexico_City');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require '../config/database.php';

try {
    $query = "SELECT a.id, a.periodo, a.año, a.horario, a.aula,
                     CONCAT(m.nombre, ' ', m.apellido) as maestro_nombre,
                     s.nombre as asignatura_nombre
              FROM asignaciones a
              JOIN maestros m ON a.maestro_id = m.id
              JOIN asignaturas s ON a.asignatura_id = s.id
              ORDER BY a.año DESC, a.periodo";
    
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $asignaciones = array();
    while ($row = $result->fetch_assoc()) {
        $asignaciones[] = $row;
    }

    echo json_encode($asignaciones);
} catch (Exception $e) {
    echo json_encode(array(
        'error' => $e->getMessage(),
        'data' => array()
    ));
}

$conn->close();
?>
