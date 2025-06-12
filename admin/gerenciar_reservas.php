<?php
session_start();
require '../conexao.php';

// Verifica se é admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Aceitar ou rejeitar reserva
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $acao = $_GET['acao'];
    $id_reserva = $_GET['id'];

    if ($acao === 'aceitar') {
        $novo_status = 2; // aceita
        $mensagem = "Reserva aceita com sucesso!";
        $tipo_mensagem = "success";
    } elseif ($acao === 'rejeitar') {
        $novo_status = 3; // rejeitada
        $mensagem = "Reserva rejeitada com sucesso!";
        $tipo_mensagem = "warning";
    }

    $stmt = $pdo->prepare("UPDATE reservas SET status_reserva = ? WHERE id_reserva = ?");
    $stmt->execute([$novo_status, $id_reserva]);
}

// Buscar reservas pendentes
$stmt = $pdo->query("
    SELECT r.id_reserva, r.data, r.turno, r.nome_turno, r.observacoes,
           s.nome AS nome_sala,
           u.nome AS nome_usuario
    FROM reservas r
    JOIN salas s ON r.id_sala = s.id_sala
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE r.status_reserva = 1
    ORDER BY r.data ASC, r.turno ASC
");
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Reservas</title>
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
                    <a class="nav-link active" href="gerenciar_reservas.php">Reservas</a>
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
            <h1 class="mb-4">Gerenciar Reservas</h1>

            <?php if (isset($mensagem)): ?>
                <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                    <?= $mensagem ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Reservas Pendentes</h5>
                    <span class="badge bg-primary"><?= count($reservas) ?> reserva(s)</span>
                </div>
                <div class="card-body">
                    <?php if (count($reservas) === 0): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Não há reservas pendentes no momento.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Usuário</th>
                                        <th>Sala</th>
                                        <th>Data</th>
                                        <th>Turno</th>
                                        <th>Observações</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservas as $reserva): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($reserva['nome_usuario']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($reserva['nome_sala']) ?></span>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($reserva['data'])) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($reserva['nome_turno']) ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($reserva['observacoes'])): ?>
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= htmlspecialchars($reserva['observacoes']) ?>">
                                                    Ver obs.
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Sem observações</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?acao=aceitar&id=<?= $reserva['id_reserva'] ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   onclick="return confirm('Aceitar esta reserva?')">
                                                    <i class="bi bi-check-lg"></i> Aceitar
                                                </a>
                                                <a href="?acao=rejeitar&id=<?= $reserva['id_reserva'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Rejeitar esta reserva?')">
                                                    <i class="bi bi-x-lg"></i> Rejeitar
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
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Inicializar tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
<script src="../script.js"></script>
</body>
</html>