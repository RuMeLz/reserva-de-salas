<?php
session_start();

// Verifica se é um admin logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
</head>
<body>
    <h2>Painel Administrativo</h2>
    <p>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</p>

    <ul>
        <li><a href="novo_usuario.php">Cadastrar Novo Usuário</a></li>
        <li><a href="gerenciar_usuarios.php">Gerenciar Usuários</a></li>
        <li><a href="editar_sala.php">Editar Salas</a></li>
        <li><a href="../dashboard.php">Voltar para Dashboard</a></li>
        <li><a href="../logout.php">Sair</a></li>
    </ul>
</body>
</html>
