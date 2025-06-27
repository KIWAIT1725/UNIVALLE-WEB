<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo "<script>alert('Acceso denegado'); window.history.back();</script>";
    exit();
}

require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_asignatura = $_POST['codigo_asignatura'];
    $nombre = $_POST['nombre'];
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
    $creditos = $_POST['creditos'];
    $horas_semanales = $_POST['horas_semanales'];
    $semestre = $_POST['semestre'];

    try {
        $stmt = $conn->prepare("INSERT INTO asignaturas (codigo_asignatura, nombre, descripcion, creditos, horas_semanales, semestre) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiii", $codigo_asignatura, $nombre, $descripcion, $creditos, $horas_semanales, $semestre);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('Asignatura creada exitosamente');
                window.parent.location.reload();
            </script>";
        } else {
            echo "<script>
                alert('Error al crear asignatura: " . addslashes($stmt->error) . "');
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
