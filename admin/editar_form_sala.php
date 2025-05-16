<?php
session_start();
require '../conexao.php';

// Proteção
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID da sala não informado.";
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM salas WHERE id = ?");
$stmt->execute([$id]);
$sala = $stmt->fetch();

if (!$sala) {
    echo "Sala não encontrada.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dados do formulário
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $status = $_POST['status'];

    $update = $pdo->prepare("UPDATE salas SET nome = ?, tipo = ?, status = ? WHERE id = ?");
    $update->execute([$nome, $tipo, $status, $id]);

    echo "Sala atualizada com sucesso. <a href='editar_sala.php'>Voltar</a>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Sala</title>
</head>
<body>
    <h2>Editar Sala</h2>
    <form method="post">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= $sala['nome'] ?>" required><br><br>

        <label>Tipo:</label>
        <select name="tipo">
            <option value="comum" <?= $sala['tipo'] === 'comum' ? 'selected' : '' ?>>Comum</option>
            <option value="laboratorio" <?= $sala['tipo'] === 'laboratorio' ? 'selected' : '' ?>>Laboratório</option>
            <option value="auditorio" <?= $sala['tipo'] === 'auditorio' ? 'selected' : '' ?>>Auditório</option>
        </select><br><br>

        <label>Status:</label>
        <select name="status">
            <option value="disponivel" <?= $sala['status'] === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
            <option value="manutencao" <?= $sala['status'] === 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
            <option value="indisponivel" <?= $sala['status'] === 'indisponivel' ? 'selected' : '' ?>>Indisponível</option>
        </select><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>

    <p><a href="editar_sala.php">Cancelar</a></p>
</body>
</html>
