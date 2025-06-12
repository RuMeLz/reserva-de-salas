<?php
include("conexao.php");
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$sucesso = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION["id_usuario"];
    $id_sala = $_POST["id_sala"];
    $data = $_POST["data"];
    $turno = $_POST["turno"];
    $observacoes = $_POST["observacoes"] ?? null;

    // Verifica se a sala permite reserva direta
    $stmt = $pdo->prepare("SELECT permite_reserva_direta FROM salas WHERE id_sala = :id_sala");
    $stmt->execute([':id_sala' => $id_sala]);
    $sala = $stmt->fetch();

    if (!$sala) {
        $erro = "Sala não encontrada.";
    } else {
        $permite_direta = $sala['permite_reserva_direta'];

        // Verifica se já existe reserva no mesmo turno e data para a mesma sala
        $stmt = $pdo->prepare("SELECT * FROM reservas WHERE id_sala = :id_sala AND data = :data AND turno = :turno AND cancelada = 0");
        $stmt->execute([
            ':id_sala' => $id_sala,
            ':data' => $data,
            ':turno' => $turno
        ]);
        $reserva_existente = $stmt->fetch();

        if ($reserva_existente) {
            $erro = "A sala já está reservada para este dia e turno.";
        } else {
            // Determina status da reserva: 2 = aprovada, 1 = pendente
            $status = $permite_direta ? 2 : 1;

            // Define nome do turno
            $nome_turno = ($turno == 1) ? 'Manhã' : (($turno == 2) ? 'Tarde' : 'Noite');

            // Insere reserva
            $stmt = $pdo->prepare("INSERT INTO reservas 
                (id_usuario, id_sala, data, turno, nome_turno, status_reserva, observacoes)
                VALUES (:id_usuario, :id_sala, :data, :turno, :nome_turno, :status, :observacoes)");
            
            $stmt->execute([
                ':id_usuario' => $id_usuario,
                ':id_sala' => $id_sala,
                ':data' => $data,
                ':turno' => $turno,
                ':nome_turno' => $nome_turno,
                ':status' => $status,
                ':observacoes' => $observacoes
            ]);

            $sucesso = "Reserva realizada com sucesso! Status: " . ($status == 2 ? "Aprovada automaticamente" : "Pendente para aprovação");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado da Reserva - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
	<link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-building"></i> Sistema de Reservas
        </a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header text-center <?php echo $sucesso ? 'bg-success' : 'bg-danger'; ?> text-white">
                    <h4 class="mb-0">
                        <?php if ($sucesso): ?>
                            <i class="bi bi-check-circle"></i> Reserva Realizada
                        <?php else: ?>
                            <i class="bi bi-exclamation-triangle"></i> Erro na Reserva
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body text-center p-4">
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <?php echo $sucesso; ?>
                        </div>
                        <i class="bi bi-calendar-check display-1 text-success mb-3"></i>
                    <?php else: ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?php echo $erro; ?>
                        </div>
                        <i class="bi bi-calendar-x display-1 text-danger mb-3"></i>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>