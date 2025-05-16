<?php
require 'conexao.php'; // inclui a conexão PDO com o banco de dados

// Exibe o formulário HTML se ainda não foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
?>
    <h2>Criar novo usuário</h2>
    <form method="POST">
        Nome: <input type="text" name="nome" required><br>
        Email: <input type="email" name="email" required><br>
        Senha: <input type="password" name="senha" required><br>
        Tipo: 
        <select name="tipo">
            <option value="comum">Comum</option>
            <option value="admin">Administrador</option>
        </select><br>
        Ativo: 
        <select name="ativo">
            <option value="1">Ativo</option>
            <option value="0">Inativo</option>
        </select><br>
        <button type="submit">Criar Usuário</button>
    </form>
<?php
} else {
    // Recebe os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    $ativo = $_POST['ativo'];

    // Cria a hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Prepara e executa a inserção no banco
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, tipo, ativo)
                           VALUES (:nome, :email, :senha_hash, :tipo, :ativo)");
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha_hash' => $senha_hash,
        ':tipo' => $tipo,
        ':ativo' => $ativo
    ]);

    echo "<p>✅ Usuário criado com sucesso!</p>";
    echo "<p><a href='criar_usuario.php'>Criar outro usuário</a></p>";
}
?>
