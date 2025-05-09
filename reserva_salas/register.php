<!-- Formulário para cadastro de usuário -->
<form method="POST">
  Nome: <input type="text" name="nome"><br>
  Email: <input type="email" name="email"><br>
  Senha: <input type="password" name="senha"><br>
  <button type="submit">Cadastrar</button>
</form>

<?php
include("conexao.php"); // Inclui o arquivo de conexão

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cria as variáveis com os dados fornecidos do formulário
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); // Criptografa (em bcrypt) a senha no padrão do PHP atrávés do parâmetro passworddefault

    // Prepara o comando de inserção de dados com o método prepare do PDO (que pode substituir através do "execute" os valores seguidos de ":"
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
    $stmt = $pdo->prepare($sql);

    // Executa o comando de consulta do banco através do método execute do PDO
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senha
    ]);

    echo "Usuário registrado com sucesso!";
}
?>
