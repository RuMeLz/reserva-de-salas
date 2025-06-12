<?php
session_start();
require '../conexao.php';

// Protege o acesso apenas para administradores
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Criação de sala
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar'])) {
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $status = $_POST['status'];
    $permite_reserva_direta = $_POST['permite_reserva_direta'];

    $stmt = $pdo->prepare("INSERT INTO salas (nome, tipo_sala_id, status, permite_reserva_direta) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $tipo, $status, $permite_reserva_direta]);
    $mensagem = "Sala criada com sucesso.";
}

// Exclusão de sala
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $stmt = $pdo->prepare("DELETE FROM salas WHERE id_sala = ?");
    $stmt->execute([$id]);
    $mensagem = "Sala excluída com sucesso.";
}

// Buscar salas existentes
$stmt = $pdo->query("
    SELECT s.*, t.nome AS nome_tipo_sala, t.id_tipo
    FROM salas s
    JOIN tipos_sala t ON s.tipo_sala_id = t.id_tipo
");

$salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Salas</title>
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
                    <a class="nav-link" href="painel.php">Painel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gerenciar_reservas.php">Reservas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gerenciar_usuarios.php">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="criar_sala.php">Salas</a>
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
            <h1 class="mb-4">Gerenciar Salas</h1>

            <?php if (isset($mensagem)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $mensagem ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formulário de Criação -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Criar Nova Sala</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome da Sala</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label">Tipo de Sala</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Selecione o tipo</option>
                                    <?php
                                    $tipos = $pdo->query("SELECT * FROM tipos_sala")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($tipos as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo'] ?>"><?= $tipo['nome'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="disponivel">Disponível</option>
                                    <option value="manutencao">Manutenção</option>
                                    <option value="indisponivel">Indisponível</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="permite_reserva_direta" class="form-label">Permite Reserva Direta</label>
                                <select class="form-select" id="permite_reserva_direta" name="permite_reserva_direta" required>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="criar" class="btn btn-primary">Criar Sala</button>
                    </form>
                </div>
            </div>

            <!-- Lista de Salas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Salas Cadastradas</h5>
                </div>
                <div class="card-body">
                    <?php if (count($salas) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>Status</th>
                                        <th>Reserva Direta</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($salas as $sala): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sala['nome']) ?></td>
                                        <td><?= htmlspecialchars($sala['nome_tipo_sala']) ?></td>
                                        <td>
                                            <?php
                                            $status_class = '';
                                            switch($sala['status']) {
                                                case 'disponivel':
                                                    $status_class = 'text-success';
                                                    break;
                                                case 'manutencao':
                                                    $status_class = 'text-warning';
                                                    break;
                                                case 'indisponivel':
                                                    $status_class = 'text-danger';
                                                    break;
                                            }
                                            ?>
                                            <span class="<?= $status_class ?>"><?= ucfirst($sala['status']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $sala['permite_reserva_direta'] ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $sala['permite_reserva_direta'] ? 'Sim' : 'Não' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="editar_form_sala.php?id=<?= $sala['id_sala'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                            <a href="criar_sala.php?excluir=<?= $sala['id_sala'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Tem certeza que deseja excluir esta sala?');">Excluir</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Nenhuma sala cadastrada no sistema.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>