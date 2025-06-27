<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo "<script>alert('Acceso denegado'); window.history.back();</script>";
    exit();
}

require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maestro_id = $_POST['maestro_id'];
    $asignatura_id = $_POST['asignatura_id'];
    $periodo = $_POST['periodo'];
    $año = $_POST['año'];
    $horario = isset($_POST['horario']) ? $_POST['horario'] : null;
    $aula = isset($_POST['aula']) ? $_POST['aula'] : null;

    try {
        $stmt = $conn->prepare("INSERT INTO asignaciones (maestro_id, asignatura_id, periodo, año, horario, aula) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisiss", $maestro_id, $asignatura_id, $periodo, $año, $horario, $aula);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('Asignación creada exitosamente');
                window.parent.location.reload();
            </script>";
        } else {
            echo "<script>
                alert('Error al crear asignación: " . addslashes($stmt->error) . "');
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
