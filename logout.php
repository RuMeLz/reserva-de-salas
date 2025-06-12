<?php
session_start();

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Se é desejado matar a sessão, também delete o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão
session_destroy();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logout - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <meta http-equiv="refresh" content="3;url=index.php">
	<link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-building"></i> Sistema de Reservas
        </a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-box-arrow-right"></i> Logout Realizado
                    </h4>
                </div>
                <div class="card-body text-center p-5">
                    <i class="bi bi-check-circle display-1 text-success mb-4"></i>
                    <h5 class="mb-3">Você foi desconectado com sucesso!</h5>
                    <p class="text-muted mb-4">
                        Obrigado por usar o Sistema de Reservas. 
                        Você será redirecionado automaticamente em alguns segundos.
                    </p>
                    
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <small class="text-muted">Redirecionando...</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-block">
                        <a href="index.php" class="btn btn-primary">
                            <i class="bi bi-house"></i> Ir para Início
                        </a>
                        <a href="login.php" class="btn btn-outline-secondary">
                            <i class="bi bi-box-arrow-in-right"></i> Fazer Login Novamente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>