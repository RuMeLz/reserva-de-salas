<?php
session_start();
include("conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$sucesso = false;
$erro = "";

// Verifica se a requisição veio por POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_reserva"])) {
    $id_reserva = $_POST["id_reserva"];

    try {
        // Atualiza a reserva para marcar como cancelada
        $stmt = $pdo->prepare("UPDATE reservas SET cancelada = 1 WHERE id_reserva = :id");
        $stmt->execute([':id' => $id_reserva]);
        
        if ($stmt->rowCount() > 0) {
            $sucesso = true;
        } else {
            $erro = "Reserva não encontrada ou já foi cancelada.";
        }
    } catch (Exception $e) {
        $erro = "Erro ao cancelar a reserva. Tente novamente.";
    }
} else {
    $erro = "Requisição inválida.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cancelamento de Reserva - Sistema de Reservas</title>
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
                            <i class="bi bi-check-circle"></i> Reserva Cancelada
                        <?php else: ?>
                            <i class="bi bi-exclamation-triangle"></i> Erro no Cancelamento
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body text-center p-4">
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            Reserva cancelada com sucesso!
                        </div>
                        <i class="bi bi-calendar-x display-1 text-success mb-3"></i>
                        <p class="text-muted">Sua reserva foi cancelada e a sala está novamente disponível para outros usuários.</p>
                    <?php else: ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?php echo $erro; ?>
                        </div>
                        <i class="bi bi-exclamation-triangle display-1 text-danger mb-3"></i>
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