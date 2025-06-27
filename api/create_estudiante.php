<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo "<script>alert('Acceso denegado'); window.history.back();</script>";
    exit();
}

require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_estudiante = $_POST['codigo_estudiante'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
    $fecha_nacimiento = isset($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;
    $password = md5(isset($_POST['password']) ? $_POST['password'] : '123456'); // Contraseña por defecto

    try {
        $stmt = $conn->prepare("INSERT INTO estudiantes (codigo_estudiante, nombre, apellido, email, telefono, fecha_nacimiento, direccion, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $codigo_estudiante, $nombre, $apellido, $email, $telefono, $fecha_nacimiento, $direccion, $password);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('Estudiante creado exitosamente');
                window.parent.location.reload();
            </script>";
        } else {
            echo "<script>
                alert('Error al crear estudiante: " . addslashes($stmt->error) . "');
                window.history.back();
            </script>";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo "<script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>
        alert('Método no permitido');
        window.history.back();
    </script>";
}

$conn->close();
?>
