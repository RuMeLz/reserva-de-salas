<?php
// Inicia a sessão para verificar se o usuário já está logado
session_start();

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['id_usuario'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Reserva de Salas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
	<link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="bi bi-building"></i> Sistema de Reservas
        </a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <i class="bi bi-calendar-check display-1 text-primary mb-4"></i>
                    <h1 class="card-title mb-4">Sistema de Reserva de Salas</h1>
                    <p class="card-text text-muted mb-4">
                        Gerencie suas reservas de salas de forma simples e eficiente. 
                        Faça login para acessar o sistema.
                    </p>
                    <a href="login.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Fazer Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 py-4 bg-dark text-light">
    <div class="container text-center">
        <p class="mb-0">&copy; 2025 Sistema de Reserva de Salas. Todos os direitos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>