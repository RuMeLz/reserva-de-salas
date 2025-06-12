<?php
session_start();
require '../conexao.php';

// Apenas admins acessam
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$usuario = null;
$mensagem = '';
$tipo_mensagem = '';

// Se veio email pela URL (link do gerenciar_usuarios.php)
if (isset($_GET['email']) && !isset($_POST['etapa'])) {
    $_POST['email'] = $_GET['email'];
    $_POST['etapa'] = 'buscar';
}

// Exibe o formulário de busca por email se ainda não há edição em andamento
if (!isset($_POST['etapa'])) {
    // Apenas exibe o formulário - nada a processar
} elseif ($_POST['etapa'] === 'buscar') {
    // Busca o usuário com base no email
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        $mensagem = "Usuário não encontrado.";
        $tipo_mensagem = "danger";
    }
} elseif ($_POST['etapa'] === 'editar') {
    // Recebe os dados do formulário de edição
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    $ativo = $_POST['ativo'];

    try {
        if (!empty($senha)) {
            // Atualiza também a senha, com hash
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, senha_hash = :senha_hash, tipo = :tipo, ativo = :ativo WHERE id_usuario = :id");
            $stmt->execute([
                ':nome' => $nome,
                ':senha_hash' => $senha_hash,
                ':tipo' => $tipo,
                ':ativo' => $ativo,
                ':id' => $id
            ]);
        } else {
            // Atualiza sem alterar a senha
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, tipo = :tipo, ativo = :ativo WHERE id_usuario = :id");
            $stmt->execute([
                ':nome' => $nome,
                ':tipo' => $tipo,
                ':ativo' => $ativo,
                ':id' => $id
            ]);
        }

        $mensagem = "Usuário atualizado com sucesso!";
        $tipo_mensagem = "success";
    } catch (Exception $e) {
        $mensagem = "Erro ao atualizar usuário: " . $e->getMessage();
        $tipo_mensagem = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Usuário</title>
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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4">Editar Usuário</h1>

            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                    <?= $mensagem ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!isset($_POST['etapa']) || ($_POST['etapa'] === 'buscar' && !$usuario)): ?>
                <!-- Formulário de busca -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-search"></i> Buscar Usuário
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email do usuário:</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                            </div>
                            <input type="hidden" name="etapa" value="buscar">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </form>
                    </div>
                </div>

            <?php elseif ($usuario): ?>
                <!-- Formulário de edição -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pencil"></i> Editando: <?= htmlspecialchars($usuario['nome']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="etapa" value="editar">
                            <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nome" class="form-label">Nome:</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="senha" class="form-label">Nova Senha:</label>
                                    <input type="password" class="form-control" id="senha" name="senha">
                                    <div class="form-text">Deixe em branco para manter a senha atual</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tipo" class="form-label">Tipo:</label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <option value="comum" <?= $usuario['tipo'] === 'comum' ? 'selected' : '' ?>>Comum</option>
                                        <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ativo" class="form-label">Status:</label>
                                    <select class="form-select" id="ativo" name="ativo" required>
                                        <option value="1" <?= $usuario['ativo'] == 1 ? 'selected' : '' ?>>Ativo</option>
                                        <option value="0" <?= $usuario['ativo'] == 0 ? 'selected' : '' ?>>Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email:</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" readonly>
                                    <div class="form-text">O email não pode ser alterado</div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php endif; ?>

            <?php if (isset($_POST['etapa']) && $_POST['etapa'] === 'editar' && $tipo_mensagem === 'success'): ?>
                <!-- Ações após sucesso -->
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h5>O que deseja fazer agora?</h5>
                        <div class="btn-group" role="group">
                            <a href="editar_usuario.php" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle"></i> Editar outro usuário
                            </a>
                            <a href="gerenciar_usuarios.php" class="btn btn-outline-secondary">
                                <i class="bi bi-list"></i> Listar usuários
                            </a>
                            <a href="painel.php" class="btn btn-outline-dark">
                                <i class="bi bi-house"></i> Voltar ao painel
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Botões de navegação -->
            <div class="card mt-3">
                <div class="card-footer">
                    <a href="gerenciar_usuarios.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar para Usuários
                    </a>
                    <a href="painel.php" class="btn btn-outline-dark">
                        <i class="bi bi-house"></i> Painel Principal
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