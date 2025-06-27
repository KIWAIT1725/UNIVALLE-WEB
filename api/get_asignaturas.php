<?php
header('Content-Type: application/json');
require '../config/database.php';

$query = "SELECT id, codigo_asignatura, nombre, creditos, horas_semanales, semestre FROM asignaturas ORDER BY semestre, nombre";
$result = $conn->query($query);

$asignaturas = array();
while ($row = $result->fetch_assoc()) {
    $asignaturas[] = $row;
}

echo json_encode($asignaturas);
$conn->close();
?>
