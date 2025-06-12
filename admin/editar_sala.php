<?php
session_start();
require '../conexao.php';

// Proteção: só admins
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM salas");
$salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Salas</title>
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
            <p>Use o menu acima para navegar pelas funcionalidades do sistema.</p>
        </div>
    </div>
</div>

    <h2>Lista de Salas</h2>
    <table border="1">
        <tr>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Status</th>
			<th>Reserva Direta</th>
            <th>Ação</th>
        </tr>
        <?php foreach ($salas as $sala): ?>
        <tr>
            <td><?= $sala['nome'] ?></td>
            <td><?= $sala['tipo_sala_id'] ?></td>
            <td><?= $sala['status'] ?></td>
			<td><?= $sala['permite_reserva_direta'] ?></td>
            <td><a href="editar_form_sala.php?id=<?= $sala['id_sala'] ?>">Editar</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="painel.php">Voltar ao Painel</a></p>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</html>
