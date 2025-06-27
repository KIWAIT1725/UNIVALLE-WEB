<?php
session_start();
header('Content-Type: application/json');

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(array('success' => false, 'message' => 'Acceso denegado'));
    exit();
}

require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    $table = $input['table'];
    $id = $input['id'];
    
    // Validar tabla permitida
    $allowed_tables = array('estudiantes', 'maestros', 'asignaturas', 'asignaciones');
    if (!in_array($table, $allowed_tables)) {
        echo json_encode(array('success' => false, 'message' => 'Tabla no permitida'));
        exit();
    }

    try {
        $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(array('success' => true, 'message' => 'Registro eliminado exitosamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al eliminar: ' . $stmt->error));
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
