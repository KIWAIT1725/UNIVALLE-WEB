<?php
header('Content-Type: application/json');
require '../config/database.php';

$query = "SELECT id, CONCAT(nombre, ' ', apellido) as nombre_completo FROM maestros ORDER BY nombre";
$result = $conn->query($query);

$maestros = array();
while ($row = $result->fetch_assoc()) {
    $maestros[] = $row;
}

echo json_encode($maestros);
$conn->close();
?>
