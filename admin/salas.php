<?php
session_start();
require '../conexao.php';

// Protege o acesso apenas para administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Criação de sala
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar'])) {
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO salas (nome, tipo, status) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $tipo, $status]);
    $mensagem = "Sala criada com sucesso.";
}

// Exclusão de sala
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $stmt = $pdo->prepare("DELETE FROM salas WHERE id = ?");
    $stmt->execute([$id]);
    $mensagem = "Sala excluída com sucesso.";
}

// Buscar salas existentes
$stmt = $pdo->query("SELECT * FROM salas");
$salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Salas</title>
</head>
<body>
    <h2>Gerenciar Salas</h2>

    <?php if (isset($mensagem)): ?>
        <p style="color:green;"><?= $mensagem ?></p>
    <?php endif; ?>

    <h3>Criar Nova Sala</h3>
    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required><br><br>

        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="comum">Comum</option>
            <option value="laboratorio">Laboratório</option>
            <option value="auditorio">Auditório</option>
        </select><br><br>

        <label>Status:</label>
        <select name="status" required>
            <option value="disponivel">Disponível</option>
            <option value="manutencao">Manutenção</option>
            <option value="indisponivel">Indisponível</option>
        </select><br><br>

        <button type="submit" name="criar">Criar Sala</button>
    </form>

    <h3>Salas Cadastradas</h3>
    <table border="1">
        <tr>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($salas as $sala): ?>
        <tr>
            <td><?= $sala['nome'] ?></td>
            <td><?= $sala['tipo'] ?></td>
            <td><?= $sala['status'] ?></td>
            <td>
                <a href="editar_form_sala.php?id=<?= $sala['id'] ?>">Editar</a> |
                <a href="salas.php?excluir=<?= $sala['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta sala?');">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="painel.php">Voltar ao Painel</a></p>
</body>
</html>
