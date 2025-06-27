<?php
session_start();
header('Content-Type: application/json');

// Verificar que sea estudiante
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    echo json_encode(array('success' => false, 'message' => 'Acceso denegado'));
    exit();
}

require '../config/database.php';

$student_id = $_SESSION['user_id'];

// Obtener horarios del estudiante
$query = "SELECT a.nombre as materia, a.codigo_asignatura,
                 CONCAT(m.nombre, ' ', m.apellido) as profesor,
                 asig.horario, asig.aula, asig.periodo, asig.año
          FROM asignaturas a
          JOIN asignaciones asig ON a.id = asig.asignatura_id
          JOIN maestros m ON asig.maestro_id = m.id
          WHERE asig.año = 2024 AND asig.periodo = '2024-1' AND asig.horario IS NOT NULL
          ORDER BY asig.horario";

$result = $conn->query($query);

$horarios = array();
while ($row = $result->fetch_assoc()) {
    $horarios[] = $row;
}

echo json_encode($horarios);
$conn->close();
?>
