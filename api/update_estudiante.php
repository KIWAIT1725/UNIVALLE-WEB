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
    $codigo_estudiante = $input['codigo_estudiante'];
    $nombre = $input['nombre'];
    $apellido = $input['apellido'];
    $email = $input['email'];
    $telefono = isset($input['telefono']) ? $input['telefono'] : null;
    $fecha_nacimiento = isset($input['fecha_nacimiento']) ? $input['fecha_nacimiento'] : null;
    $direccion = isset($input['direccion']) ? $input['direccion'] : null;
    $password = isset($input['password']) ? $input['password'] : null;

    try {
        // Si se proporciona una nueva contraseña, incluirla en la actualización
        if (!empty($password)) {
            $hashed_password = md5($password);
            $stmt = $conn->prepare("UPDATE estudiantes SET codigo_estudiante = ?, nombre = ?, apellido = ?, email = ?, telefono = ?, fecha_nacimiento = ?, direccion = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssssssi", $codigo_estudiante, $nombre, $apellido, $email, $telefono, $fecha_nacimiento, $direccion, $hashed_password, $id);
        } else {
            // Actualizar sin cambiar la contraseña
            $stmt = $conn->prepare("UPDATE estudiantes SET codigo_estudiante = ?, nombre = ?, apellido = ?, email = ?, telefono = ?, fecha_nacimiento = ?, direccion = ? WHERE id = ?");
            $stmt->bind_param("sssssssi", $codigo_estudiante, $nombre, $apellido, $email, $telefono, $fecha_nacimiento, $direccion, $id);
        }
        
        if ($stmt->execute()) {
            echo json_encode(array('success' => true, 'message' => 'Estudiante actualizado exitosamente'));
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
