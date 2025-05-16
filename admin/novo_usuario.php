<?php
session_start();
require '../conexao.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografa a senha
    $tipo = $_POST['tipo']; // 'usuario' ou 'admin'
    $ativo = 1; // Usuário será ativo por padrão

    // Verifica se já existe um usuário com esse e-mail
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);

    if ($stmt->fetch()) {
        $mensagem = "Já existe um usuário com este e-mail.";
    } else {
        // Insere o novo usuário
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, ativo) VALUES (:nome, :email, :senha, :tipo, :ativo)");
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha,
            ':tipo' => $tipo,
            ':ativo' => $ativo
        ]);
        $mensagem = "Usuário cadastrado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Novo Usuário</title>
</head>
<body>
    <h2>Cadastrar Novo Usuário</h2>

    <?php if (isset($mensagem)) echo "<p><strong>$mensagem</strong></p>"; ?>

    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <label>Tipo de usuário:</label><br>
        <select name="tipo">
            <option value="usuario">Usuário comum</option>
            <option value="admin">Administrador</option>
        </select><br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <p><a href="painel.php">Voltar ao Painel Administrativo</a></p>
</body>
</html>