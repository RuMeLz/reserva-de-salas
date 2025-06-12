<?php
session_start();
include("conexao.php");

// Verifica se está logado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

// Buscar salas com tipo
$stmt = $pdo->query("
    SELECT s.id_sala, s.nome, s.status, t.nome AS tipo_sala
    FROM salas s
    JOIN tipos_sala t ON s.tipo_sala_id = t.id_tipo
");
$salas = $stmt->fetchAll();

// Buscar reservas existentes
$stmt = $pdo->query("
    SELECT r.id_reserva, r.data, r.turno, r.nome_turno, r.status_reserva, r.observacoes,
           s.nome AS nome_sala, s.status,
           t.nome AS tipo_sala,
           u.nome AS nome_usuario
    FROM reservas r
    JOIN salas s ON r.id_sala = s.id_sala
    JOIN tipos_sala t ON s.tipo_sala_id = t.id_tipo
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE r.cancelada = 0
    ORDER BY r.data DESC, r.turno
");
$reservas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
	<link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="bi bi-building"></i> Sistema de Reservas
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>
                <?php if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin/painel.php">
                        <i class="bi bi-gear"></i> Painel Admin
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text me-3">
                <i class="bi bi-person-circle"></i> Olá, <?php echo $_SESSION['nome']; ?>!
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">
                <i class="bi bi-calendar-check text-primary"></i> 
                Bem-vindo ao Dashboard
            </h1>
            <p class="lead text-muted">Gerencie suas reservas de salas</p>
        </div>
    </div>

    <!-- Formulário de Reserva -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Nova Reserva
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="reserva.php">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data" class="form-label">Data</label>
                                <input type="date" class="form-control" id="data" name="data" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="turno" class="form-label">Turno</label>
                                <select class="form-select" id="turno" name="turno" required>
                                    <option value="">Selecione o turno</option>
                                    <option value="1">Manhã</option>
                                    <option value="2">Tarde</option>
                                    <option value="3">Noite</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="id_sala" class="form-label">Sala</label>
                            <select class="form-select" id="id_sala" name="id_sala" required>
                                <option value="">Selecione uma sala</option>
                                <?php foreach ($salas as $sala): ?>
                                    <option value="<?php echo $sala['id_sala']; ?>">
                                        <?php echo $sala['nome']; ?> - <?php echo $sala['tipo_sala']; ?> (<?php echo $sala['status']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Observações adicionais (opcional)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-calendar-plus"></i> Reservar Sala
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Reservas -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Reservas Existentes
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (count($reservas) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="bi bi-calendar3"></i> Data</th>
                                        <th><i class="bi bi-clock"></i> Turno</th>
                                        <th><i class="bi bi-door-open"></i> Sala</th>
                                        <th><i class="bi bi-tag"></i> Tipo</th>
                                        <th><i class="bi bi-check-circle"></i> Status</th>
                                        <th><i class="bi bi-person"></i> Usuário</th>
                                        <th><i class="bi bi-chat-text"></i> Observações</th>
                                        <th><i class="bi bi-gear"></i> Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservas as $r): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($r['data'])); ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo $r['nome_turno']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $r['nome_sala']; ?></td>
                                        <td><?php echo $r['tipo_sala']; ?></td>
                                        <td>
                                            <?php if ($r['status_reserva'] == 2): ?>
                                                <span class="badge bg-success">Aprovada</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $r['nome_usuario']; ?></td>
                                        <td><?php echo $r['observacoes'] ?: '-'; ?></td>
                                        <td>
                                            <form method="POST" action="cancelar_reserva.php" style="display: inline;">
                                                <input type="hidden" name="id_reserva" value="<?php echo $r['id_reserva']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Tem certeza que deseja cancelar esta reserva?');">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">Nenhuma reserva encontrada</h5>
                            <p class="text-muted">Faça sua primeira reserva usando o formulário acima.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>