<?php
session_start();
require '../conexao.php';

// Proteção
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID da sala não informado.";
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM salas WHERE id_sala = ?");
$stmt->execute([$id]);
$sala = $stmt->fetch();

if (!$sala) {
    echo "Sala não encontrada.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dados do formulário
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo_sala_id'];
    $status = $_POST['status'];
    $permite_reserva_direta = $_POST["permite_reserva_direta"];

    $update = $pdo->prepare("UPDATE salas SET nome = ?, tipo_sala_id = ?, permite_reserva_direta = ?, status = ? WHERE id_sala = ?");
    $update->execute([$nome, $tipo, $permite_reserva_direta, $status, $id]);

    $mensagem = "Sala atualizada com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Sala</title>
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
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Editar Sala</h1>

            <?php if (isset($mensagem)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $mensagem ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações da Sala</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Sala</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($sala['nome']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="tipo_sala_id" class="form-label">Tipo de Sala</label>
                            <select class="form-select" id="tipo_sala_id" name="tipo_sala_id" required>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM tipos_sala");
                                $tipos = $stmt->fetchAll();
                                foreach ($tipos as $tipo) {
                                    $selected = ($tipo['id_tipo'] == $sala['tipo_sala_id']) ? 'selected' : '';
                                    echo "<option value='{$tipo['id_tipo']}' $selected>{$tipo['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="disponivel" <?= $sala['status'] === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                                <option value="manutencao" <?= $sala['status'] === 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
                                <option value="indisponivel" <?= $sala['status'] === 'indisponivel' ? 'selected' : '' ?>>Indisponível</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="permite_reserva_direta" class="form-label">Permite Reserva Direta</label>
                            <select class="form-select" id="permite_reserva_direta" name="permite_reserva_direta" required>
                                <option value="1" <?= $sala['permite_reserva_direta'] == 1 ? 'selected' : '' ?>>Sim</option>
                                <option value="0" <?= $sala['permite_reserva_direta'] == 0 ? 'selected' : '' ?>>Não</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <a href="criar_sala.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>