<?php
require 'conexao.php'; // Conexão PDO com o banco

// Exibe o formulário de busca por email se ainda não há edição em andamento
if (!isset($_POST['etapa'])) {
?>
    <h2>Editar usuário</h2>
    <form method="POST">
        <label>Email do usuário que deseja editar:</label><br>
        <input type="email" name="email" required>
        <input type="hidden" name="etapa" value="buscar">
        <button type="submit">Buscar</button>
    </form>
<?php
} elseif ($_POST['etapa'] === 'buscar') {
    // Busca o usuário com base no email
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        echo "<p style='color:red'>Usuário não encontrado.</p>";
        echo "<a href='editar_usuario.php'>Voltar</a>";
        exit;
    }

    // Exibe o formulário de edição
?>
    <h2>Editando usuário: <?= htmlspecialchars($usuario['nome']) ?></h2>
    <form method="POST">
        <input type="hidden" name="etapa" value="editar">
        <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">

        Nome: <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required><br>
        Nova Senha: <input type="password" name="senha"><br>
        Tipo: 
        <select name="tipo">
            <option value="comum" <?= $usuario['tipo'] === 'comum' ? 'selected' : '' ?>>Comum</option>
            <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select><br>
        Ativo: 
        <select name="ativo">
            <option value="1" <?= $usuario['ativo'] == 1 ? 'selected' : '' ?>>Ativo</option>
            <option value="0" <?= $usuario['ativo'] == 0 ? 'selected' : '' ?>>Inativo</option>
        </select><br>
        <button type="submit">Salvar Alterações</button>
    </form>
<?php
} elseif ($_POST['etapa'] === 'editar') {
    // Recebe os dados do formulário de edição
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    $ativo = $_POST['ativo'];

    if (!empty($senha)) {
        // Atualiza também a senha, com hash
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, senha_hash = :senha_hash, tipo = :tipo, ativo = :ativo WHERE id_usuario = :id");
        $stmt->execute([
            ':nome' => $nome,
            ':senha_hash' => $senha_hash,
            ':tipo' => $tipo,
            ':ativo' => $ativo,
            ':id' => $id
        ]);
    } else {
        // Atualiza sem alterar a senha
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, tipo = :tipo, ativo = :ativo WHERE id_usuario = :id");
        $stmt->execute([
            ':nome' => $nome,
            ':tipo' => $tipo,
            ':ativo' => $ativo,
            ':id' => $id
        ]);
    }

    echo "<p>✅ Usuário atualizado com sucesso!</p>";
    echo "<p><a href='editar_usuario.php'>Editar outro usuário</a></p>";
}
?>
