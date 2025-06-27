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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $table = $_GET['table'];
    $id = $_GET['id'];
    
    // Validar tabla permitida
    $allowed_tables = array('estudiantes', 'maestros', 'asignaturas', 'asignaciones');
    if (!in_array($table, $allowed_tables)) {
        echo json_encode(array('success' => false, 'message' => 'Tabla no permitida'));
        exit();
    }

    try {
        if ($table === 'asignaciones') {
            // Para asignaciones, necesitamos también los IDs de maestro y asignatura
            $query = "SELECT a.*, m.id as maestro_id, s.id as asignatura_id 
                     FROM asignaciones a
                     JOIN maestros m ON a.maestro_id = m.id
                     JOIN asignaturas s ON a.asignatura_id = s.id
                     WHERE a.id = ?";
        } else {
            $query = "SELECT * FROM $table WHERE id = ?";
        }
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();
            echo json_encode(array('success' => true, 'data' => $record));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Registro no encontrado'));
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
