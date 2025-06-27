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
    $codigo_asignatura = $input['codigo_asignatura'];
    $nombre = $input['nombre'];
    $descripcion = isset($input['descripcion']) ? $input['descripcion'] : null;
    $creditos = $input['creditos'];
    $horas_semanales = $input['horas_semanales'];
    $semestre = $input['semestre'];

    try {
        $stmt = $conn->prepare("UPDATE asignaturas SET codigo_asignatura = ?, nombre = ?, descripcion = ?, creditos = ?, horas_semanales = ?, semestre = ? WHERE id = ?");
        $stmt->bind_param("sssiii", $codigo_asignatura, $nombre, $descripcion, $creditos, $horas_semanales, $semestre, $id);
        
        if ($stmt->execute()) {
            echo json_encode(array('success' => true, 'message' => 'Asignatura actualizada exitosamente'));
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
