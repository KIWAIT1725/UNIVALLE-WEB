<?php
session_start();
require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_estudiante = $_POST['codigo_estudiante'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, codigo_estudiante, nombre, apellido, email FROM estudiantes WHERE codigo_estudiante = ? AND password = ?");
    $hashed_password = md5($password); // En producción usar password_hash() y password_verify()
    $stmt->bind_param("ss", $codigo_estudiante, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $_SESSION['user_type'] = 'student';
        $_SESSION['user_id'] = $student['id'];
        $_SESSION['user_name'] = $student['nombre'] . ' ' . $student['apellido'];
        $_SESSION['user_code'] = $student['codigo_estudiante'];
        
        header("Location: ../dashboard/student_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Codigo de estudiante o contraseña incorrectos'); window.location.href='../index.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../index.html");
    exit();
}
?>
