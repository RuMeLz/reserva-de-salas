<?php
session_start();

// Verifica se é um admin logado
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="../style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark navbar-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Sistema de Reservas</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="painel.php">Painel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gerenciar_reservas.php">Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gerenciar_usuarios.php">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="criar_sala.php">Salas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gerar_relatorio.php">Relatórios</a>
                </li>
            </ul>
            <span class="navbar-text">
                <a href="../logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </span>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Bem-vindo ao Painel Administrativo</h1>
                <a href="../dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                </a>
            </div>
            <p>Use o menu acima para navegar pelas funcionalidades do sistema.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>