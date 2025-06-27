<?php
// Configurar timezone al inicio
date_default_timezone_set('America/Mexico_City');

header('Content-Type: application/json');
require '../config/database.php';

try {
    $stats = array();
    
    // Contar estudiantes
    $result = $conn->query("SELECT COUNT(*) as count FROM estudiantes");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['estudiantes'] = $row['count'];
    } else {
        $stats['estudiantes'] = 0;
    }
    
    // Contar maestros
    $result = $conn->query("SELECT COUNT(*) as count FROM maestros");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['maestros'] = $row['count'];
    } else {
        $stats['maestros'] = 0;
    }
    
    // Contar asignaturas
    $result = $conn->query("SELECT COUNT(*) as count FROM asignaturas");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['asignaturas'] = $row['count'];
    } else {
        $stats['asignaturas'] = 0;
    }
    
    // Contar asignaciones
    $result = $conn->query("SELECT COUNT(*) as count FROM asignaciones");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['asignaciones'] = $row['count'];
    } else {
        $stats['asignaciones'] = 0;
    }
    
    echo json_encode($stats);
} catch (Exception $e) {
    echo json_encode(array(
        'error' => $e->getMessage(),
        'estudiantes' => 0,
        'maestros' => 0,
        'asignaturas' => 0,
        'asignaciones' => 0
    ));
}

$conn->close();
?>
