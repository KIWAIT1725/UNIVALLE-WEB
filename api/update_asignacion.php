<?php
date_default_timezone_set('America/Mexico_City');
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(array('success' => false, 'message' => 'Acceso denegado'));
    exit();
}

header('Content-Type: application/json');
require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'];
    $maestro_id = $input['maestro_id'];
    $asignatura_id = $input['asignatura_id'];
    $periodo = $input['periodo'];
    $año = $input['año'];
    $horario = isset($input['horario']) ? $input['horario'] : null;
    $aula = isset($input['aula']) ? $input['aula'] : null;

    try {
        $stmt = $conn->prepare("UPDATE asignaciones SET maestro_id = ?, asignatura_id = ?, periodo = ?, año = ?, horario = ?, aula = ? WHERE id = ?");
        $stmt->bind_param("iisissi", $maestro_id, $asignatura_id, $periodo, $año, $horario, $aula, $id);
        
        if ($stmt->execute()) {
            echo json_encode(array('success' => true, 'message' => 'Asignación actualizada exitosamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al actualizar: ' . $stmt->error));
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(array('success' => false, 'message' => 'Error: ' . $e->getMessage()));
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Método no permitido'));
}

$conn->close();
?>
