<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo "<script>alert('Acceso denegado'); window.history.back();</script>";
    exit();
}

require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_maestro = $_POST['codigo_maestro'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
    $especialidad = $_POST['especialidad'];
    $fecha_contratacion = isset($_POST['fecha_contratacion']) ? $_POST['fecha_contratacion'] : null;
    $salario = isset($_POST['salario']) ? $_POST['salario'] : 0;

    try {
        $stmt = $conn->prepare("INSERT INTO maestros (codigo_maestro, nombre, apellido, email, telefono, especialidad, fecha_contratacion, salario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssd", $codigo_maestro, $nombre, $apellido, $email, $telefono, $especialidad, $fecha_contratacion, $salario);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('Maestro creado exitosamente');
                window.parent.location.reload();
            </script>";
        } else {
            echo "<script>
                alert('Error al crear maestro: " . addslashes($stmt->error) . "');
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
