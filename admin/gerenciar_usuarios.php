<?php
session_start();
require '../conexao.php';

// Apenas admins acessam
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ativa ou desativa usuário
if (isset($_GET['acao'], $_GET['id'])) {
    $id = $_GET['id'];
    $acao = $_GET['acao'] === 'ativar' ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE usuarios SET ativo = :ativo WHERE id_usuario = :id");
    $stmt->execute([':ativo' => $acao, ':id' => $id]);
    
    $mensagem = $acao ? "Usuário ativado com sucesso!" : "Usuário desativado com sucesso!";
    $tipo_mensagem = "success";
}

// Busca todos os usuários (exceto o admin atual)
$stmt = $pdo->query("SELECT id_usuario, nome, email, tipo, ativo FROM usuarios WHERE id_usuario != " . $_SESSION['id_usuario']);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
	<link href="../style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark navbar-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Sistema de Reservas</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="painel.php">Painel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gerenciar_reservas.php">Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="gerenciar_usuarios.php">Usuários</a>
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
            <h1 class="mb-4">Gerenciar Usuários</h1>

            <?php if (isset($mensagem)): ?>
                <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                    <?= $mensagem ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Usuários Cadastrados</h5>
                    <span class="badge bg-primary"><?= count($usuarios) ?> usuário(s)</span>
                </div>
                <div class="card-body">
                    <?php if (count($usuarios) === 0): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Não há usuários cadastrados no momento.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="bi bi-person"></i> Nome</th>
                                        <th><i class="bi bi-envelope"></i> Email</th>
                                        <th><i class="bi bi-shield"></i> Tipo</th>
                                        <th><i class="bi bi-activity"></i> Status</th>
                                        <th><i class="bi bi-gear"></i> Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($usuario['nome']) ?></strong>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($usuario['email']) ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario['tipo'] === 'admin'): ?>
                                                <span class="badge bg-danger">Administrador</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Comum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario['ativo']): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Ativo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> Inativo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if ($usuario['ativo']): ?>
                                                    <a href="?acao=desativar&id=<?= $usuario['id_usuario'] ?>" 
                                                       class="btn btn-sm btn-warning" 
                                                       onclick="return confirm('Desativar usuário <?= htmlspecialchars($usuario['nome']) ?>?')">
                                                        <i class="bi bi-pause-circle"></i> Desativar
                                                    </a>
                                                <?php else: ?>
                                                    <a href="?acao=ativar&id=<?= $usuario['id_usuario'] ?>" 
                                                       class="btn btn-sm btn-success" 
                                                       onclick="return confirm('Ativar usuário <?= htmlspecialchars($usuario['nome']) ?>?')">
                                                        <i class="bi bi-play-circle"></i> Ativar
                                                    </a>
                                                <?php endif; ?>
                                                <a href="editar_usuario.php?email=<?= urlencode($usuario['email']) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="painel.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar ao Painel
                    </a>
                    <a href="novo_usuario.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Usuário
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>