<?php
session_start();
require '../conexao.php';

// Permitir acesso apenas para admins
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Verifica o tipo de relatório a gerar
if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];

    if ($tipo === 'reservas') {
        // Geração de CSV de reservas
        $stmt = $pdo->query("
            SELECT r.id_reserva, r.data, r.nome_turno, r.status_reserva, r.observacoes,
                   s.nome AS sala, u.nome AS usuario
            FROM reservas r
            JOIN salas s ON r.id_sala = s.id_sala
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            ORDER BY r.data DESC, r.id_reserva DESC
        ");
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_reservas_' . date('Y-m-d_H-i-s') . '.csv"');

        $saida = fopen('php://output', 'w');
        // BOM para UTF-8
        fwrite($saida, "\xEF\xBB\xBF");
        
        fputcsv($saida, ['ID', 'Data', 'Turno', 'Status', 'Observações', 'Sala', 'Usuário'], ';');

        foreach ($dados as $linha) {
            fputcsv($saida, [
                $linha['id_reserva'],
                date('d/m/Y', strtotime($linha['data'])),
                $linha['nome_turno'],
                match ($linha['status_reserva']) {
                    1 => 'Pendente',
                    2 => 'Aceita',
                    3 => 'Rejeitada',
                    default => 'Desconhecido'
                },
                $linha['observacoes'] ?: 'Sem observações',
                $linha['sala'],
                $linha['usuario']
            ], ';');
        }

        fclose($saida);
        exit;

    } elseif ($tipo === 'usuarios') {
        // Geração de CSV de usuários
        $stmt = $pdo->query("SELECT id_usuario, nome, email, tipo, ativo, data_criacao FROM usuarios ORDER BY nome");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_usuarios_' . date('Y-m-d_H-i-s') . '.csv"');

        $saida = fopen('php://output', 'w');
        // BOM para UTF-8
        fwrite($saida, "\xEF\xBB\xBF");
        
        fputcsv($saida, ['ID', 'Nome', 'Email', 'Tipo', 'Status', 'Data Criação'], ';');

        foreach ($usuarios as $usuario) {
            fputcsv($saida, [
                $usuario['id_usuario'],
                $usuario['nome'],
                $usuario['email'],
                $usuario['tipo'],
                $usuario['ativo'] ? 'Ativo' : 'Inativo',
                isset($usuario['data_criacao']) ? date('d/m/Y H:i', strtotime($usuario['data_criacao'])) : 'N/A'
            ], ';');
        }

        fclose($saida);
        exit;
        
    } elseif ($tipo === 'salas') {
        // Geração de CSV de salas
        $stmt = $pdo->query("SELECT id_sala, nome, tipo_sala_id, permite_reserva_direta, status FROM salas ORDER BY nome");
        $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_salas_' . date('Y-m-d_H-i-s') . '.csv"');

        $saida = fopen('php://output', 'w');
        // BOM para UTF-8
        fwrite($saida, "\xEF\xBB\xBF");
        
        fputcsv($saida, ['ID', 'Nome', 'tipo_sala_id', 'permite_reserva_direta', 'Status'], ';');

        foreach ($salas as $sala) {
            fputcsv($saida, [
                $sala['id_sala'],
                $sala['nome'],
                $sala['tipo_sala_id'],
                $sala['permite_reserva_direta'],
                $sala['status']
            ], ';');
        }

        fclose($saida);
        exit;
    }
}

// Buscar estatísticas para o dashboard
$stmt_total_reservas = $pdo->query("SELECT COUNT(*) as total FROM reservas");
$total_reservas = $stmt_total_reservas->fetch()['total'];

$stmt_reservas_pendentes = $pdo->query("SELECT COUNT(*) as total FROM reservas WHERE status_reserva = 1");
$reservas_pendentes = $stmt_reservas_pendentes->fetch()['total'];

$stmt_total_usuarios = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $stmt_total_usuarios->fetch()['total'];

$stmt_total_salas = $pdo->query("SELECT COUNT(*) as total FROM salas");
$total_salas = $stmt_total_salas->fetch()['total'];

// Estatísticas por mês (últimos 6 meses)
$stmt_estatisticas = $pdo->query("
    SELECT 
        DATE_FORMAT(data, '%Y-%m') as mes,
        COUNT(*) as total_reservas,
        SUM(CASE WHEN status_reserva = 2 THEN 1 ELSE 0 END) as aceitas,
        SUM(CASE WHEN status_reserva = 3 THEN 1 ELSE 0 END) as rejeitadas
    FROM reservas 
    WHERE data >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(data, '%Y-%m')
    ORDER BY mes DESC
");
$estatisticas_mensais = $stmt_estatisticas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerar Relatórios</title>
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
                    <a class="nav-link" href="gerenciar_usuarios.php">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="criar_sala.php">Salas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="gerar_relatorio.php">Relatórios</a>
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
            <h1 class="mb-4">Relatórios e Estatísticas</h1>

            <!-- Estatísticas gerais -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-center bg-primary text-white">
                        <div class="card-body">
                            <i class="bi bi-calendar-check fs-1"></i>
                            <h4 class="card-title"><?= $total_reservas ?></h4>
                            <p class="card-text">Total de Reservas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center bg-warning text-dark">
                        <div class="card-body">
                            <i class="bi bi-clock fs-1"></i>
                            <h4 class="card-title"><?= $reservas_pendentes ?></h4>
                            <p class="card-text">Reservas Pendentes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center bg-success text-white">
                        <div class="card-body">
                            <i class="bi bi-people fs-1"></i>
                            <h4 class="card-title"><?= $total_usuarios ?></h4>
                            <p class="card-text">Usuários Cadastrados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center bg-info text-white">
                        <div class="card-body">
                            <i class="bi bi-door-open fs-1"></i>
                            <h4 class="card-title"><?= $total_salas ?></h4>
                            <p class="card-text">Salas Disponíveis</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relatórios CSV -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Relatórios em CSV
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Baixe relatórios completos em formato CSV para análise em planilhas.</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-check text-primary fs-1"></i>
                                    <h6 class="card-title mt-2">Relatório de Reservas</h6>
                                    <p class="card-text small">Lista completa de todas as reservas com status e detalhes.</p>
                                    <a href="?tipo=reservas" class="btn btn-primary">
                                        <i class="bi bi-download"></i> Baixar CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="bi bi-people text-success fs-1"></i>
                                    <h6 class="card-title mt-2">Relatório de Usuários</h6>
                                    <p class="card-text small">Lista de usuários cadastrados com informações básicas.</p>
                                    <a href="?tipo=usuarios" class="btn btn-success">
                                        <i class="bi bi-download"></i> Baixar CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="bi bi-door-open text-info fs-1"></i>
                                    <h6 class="card-title mt-2">Relatório de Salas</h6>
                                    <p class="card-text small">Lista de salas com capacidade e equipamentos disponíveis.</p>
                                    <a href="?tipo=salas" class="btn btn-info">
                                        <i class="bi bi-download"></i> Baixar CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relatórios PDF -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-pdf"></i> Relatórios em PDF
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Gere relatórios formatados em PDF para impressão e apresentação.</p>
                    <div class="btn-group" role="group">
                        <a href="gerar_pdf.php?tipo=reservas" class="btn btn-outline-danger">
                            <i class="bi bi-file-pdf"></i> PDF de Reservas
                        </a>
                        <a href="gerar_pdf.php?tipo=usuarios" class="btn btn-outline-danger">
                            <i class="bi bi-file-pdf"></i> PDF de Usuários
                        </a>
                        <a href="gerar_pdf.php?tipo=salas" class="btn btn-outline-danger">
                            <i class="bi bi-file-pdf"></i> PDF de Salas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estatísticas mensais -->
            <?php if (!empty($estatisticas_mensais)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up"></i> Estatísticas dos Últimos 6 Meses
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Mês/Ano</th>
                                    <th>Total de Reservas</th>
                                    <th>Aceitas</th>
                                    <th>Rejeitadas</th>
                                    <th>Taxa de Aprovação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estatisticas_mensais as $stat): ?>
                                <tr>
                                    <td>
                                        <strong><?= date('m/Y', strtotime($stat['mes'] . '-01')) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?= $stat['total_reservas'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?= $stat['aceitas'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger"><?= $stat['rejeitadas'] ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $taxa = $stat['total_reservas'] > 0 ? round(($stat['aceitas'] / $stat['total_reservas']) * 100, 1) : 0;
                                        $cor_badge = $taxa >= 80 ? 'success' : ($taxa >= 60 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?= $cor_badge ?>"><?= $taxa ?>%</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Botões de navegação -->
            <div class="card">
                <div class="card-footer">
                    <a href="painel.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar ao Painel
                    </a>
                    <a href="gerenciar_reservas.php" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-check"></i> Gerenciar Reservas
                    </a>
                    <a href="gerenciar_usuarios.php" class="btn btn-outline-success">
                        <i class="bi bi-people"></i> Gerenciar Usuários
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