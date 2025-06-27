<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Credenciales hardcodeadas
    $admin_usuario = "KIWAIT";
    $admin_password = "1725";

    if ($usuario === $admin_usuario && $password === $admin_password) {
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Administrador KIWAIT';
        $_SESSION['user_username'] = $admin_usuario;
        
        header("Location: ../dashboard/admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href='../index.html';</script>";
    }
} else {
    header("Location: ../index.html");
    exit();
}
?>
