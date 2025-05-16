<?php
session_start();
require '../conexao.php';

// Apenas admins acessam
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ativa ou desativa usuário
if (isset($_GET['acao'], $_GET['id'])) {
    $id = $_GET['id'];
    $acao = $_GET['acao'] === 'ativar' ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE usuarios SET ativo = :ativo WHERE id = :id");
    $stmt->execute([':ativo' => $acao, ':id' => $id]);
}

// Busca todos os usuários (exceto o admin atual)
$stmt = $pdo->query("SELECT id, nome, email, tipo, ativo FROM usuarios WHERE id != " . $_SESSION['usuario_id']);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
</head>
<body>
    <h2>Usuários Cadastrados</h2>
    <table border="1">
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?= $usuario['nome'] ?></td>
            <td><?= $usuario['email'] ?></td>
            <td><?= $usuario['tipo'] ?></td>
            <td><?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?></td>
            <td>
                <?php if ($usuario['ativo']): ?>
                    <a href="?acao=desativar&id=<?= $usuario['id'] ?>">Desativar</a>
                <?php else: ?>
                    <a href="?acao=ativar&id=<?= $usuario['id'] ?>">Ativar</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="painel.php">Voltar ao Painel</a></p>
</body>
</html>
