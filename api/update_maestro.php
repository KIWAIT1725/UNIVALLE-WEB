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
    $codigo_maestro = $input['codigo_maestro'];
    $nombre = $input['nombre'];
    $apellido = $input['apellido'];
    $email = $input['email'];
    $telefono = isset($input['telefono']) ? $input['telefono'] : null;
    $especialidad = $input['especialidad'];
    $fecha_contratacion = isset($input['fecha_contratacion']) ? $input['fecha_contratacion'] : null;
    $salario = isset($input['salario']) ? $input['salario'] : 0;

    try {
        $stmt = $conn->prepare("UPDATE maestros SET codigo_maestro = ?, nombre = ?, apellido = ?, email = ?, telefono = ?, especialidad = ?, fecha_contratacion = ?, salario = ? WHERE id = ?");
        $stmt->bind_param("sssssssdi", $codigo_maestro, $nombre, $apellido, $email, $telefono, $especialidad, $fecha_contratacion, $salario, $id);
        
        if ($stmt->execute()) {
            echo json_encode(array('success' => true, 'message' => 'Maestro actualizado exitosamente'));
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
