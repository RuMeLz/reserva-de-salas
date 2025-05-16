<?php
// Inicia a sessão para verificar se o usuário já está logado
session_start();

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo ao Sistema de Reservas</title>
</head>
<body>
    <h1>Sistema de Reserva de Salas</h1>
    <p><a href="login.php">Login</a></p>
</body>
</html>
