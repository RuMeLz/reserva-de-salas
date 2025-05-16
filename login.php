<!-- Formulário de login -->
<form method="POST">
  Email: <input type="email" name="email"><br>
  Senha: <input type="password" name="senha"><br>
  <button type="submit">Entrar</button>
</form>

<?php
include("conexao.php"); // Conexão com o banco
session_start(); // Inicia a sessão para salvar dados do login

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Procura o usuário pelo email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();
	
	// confere se o usuário está ativo no sistema
if ($usuario && $usuario['ativo'] == 0) {
    die("Este usuário está inativo.");
}
    // Verifica se o usuário e senha conferem de acordo com a senha do input
    if ($usuario && password_verify($senha, $usuario["senha_hash"])) {
        // Salva o ID do usuário na sessão para manter ele logado
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["tipo"] = $usuario["tipo"]; // Controle de acesso como admin ou comum

        header("Location: dashboard.php"); // Redireciona para o painel principal se passar pela verificação
    } else {
        echo "Email ou senha inválidos.";
    }
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
