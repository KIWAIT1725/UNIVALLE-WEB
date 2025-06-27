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

// Simular calificaciones (en un sistema real habría una tabla de calificaciones)
$calificaciones = array(
    array(
        'codigo' => 'MAT101',
        'materia' => 'Cálculo Diferencial',
        'profesor' => 'Dr. Roberto Martínez Silva',
        'nota1' => 4.2,
        'nota2' => 3.8,
        'nota3' => 4.5,
        'promedio' => 4.17,
        'estado' => 'Aprobado'
    ),
    array(
        'codigo' => 'FIS101',
        'materia' => 'Física Mecánica',
        'profesor' => 'Dra. Ana María González Torres',
        'nota1' => 3.5,
        'nota2' => 4.0,
        'nota3' => 3.8,
        'promedio' => 3.77,
        'estado' => 'Aprobado'
    ),
    array(
        'codigo' => 'QUI101',
        'materia' => 'Química General',
        'profesor' => 'Mg. Luis Fernando Rodríguez Pérez',
        'nota1' => 4.8,
        'nota2' => 4.5,
        'nota3' => 4.7,
        'promedio' => 4.67,
        'estado' => 'Aprobado'
    )
);

echo json_encode($calificaciones);
$conn->close();
?>
