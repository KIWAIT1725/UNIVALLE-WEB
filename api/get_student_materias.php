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

// Obtener materias del estudiante (simulado - en un sistema real habría una tabla de inscripciones)
$query = "SELECT a.codigo_asignatura, a.nombre, a.creditos, a.semestre,
                 CONCAT(m.nombre, ' ', m.apellido) as profesor,
                 asig.horario, asig.aula, asig.periodo, asig.año
          FROM asignaturas a
          JOIN asignaciones asig ON a.id = asig.asignatura_id
          JOIN maestros m ON asig.maestro_id = m.id
          WHERE asig.año = 2024 AND asig.periodo = '2024-1'
          ORDER BY a.semestre, a.nombre";

$result = $conn->query($query);

$materias = array();
while ($row = $result->fetch_assoc()) {
    $materias[] = $row;
}

echo json_encode($materias);
$conn->close();
?>
