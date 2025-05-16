<?php
include("conexao.php"); // Conexão
session_start(); // Sessão ativa para pegar o ID do usuário

// Coleta os dados do formulário
$id_usuario = $_SESSION["usuario_id"];
$id_sala = $_POST["id_sala"];
$data = $_POST["data"];
$turno = $_POST["turno"];
$status = 1; // valor de status pendente

// Verifica se a sala permite reserva direta
$stmt = $pdo->prepare("SELECT permite_reserva_direta FROM salas WHERE id = :id");
$stmt->execute([':id' => $id_sala]);
$sala = $stmt->fetch();

// Se for sala comum, aprova direto
if ($sala['permite_reserva_direta'] == 1) {
    $status = 2; // Valor de status aprovado
}

// Verifica o status da sala no banco
$stmt = $pdo->prepare("SELECT status FROM salas WHERE id = :id");
$stmt->execute([':id' => $id_sala]);
$sala = $stmt->fetch();

// Se a sala estiver com status diferente de 'disponivel', bloqueia a reserva
if ($sala['status'] !== 'disponivel') {
    die("Esta sala está marcada como '{$sala['status']}' e não pode ser reservada.");
}

// Insere a reserva no banco
$sql = "INSERT INTO reservas (id_usuario, id_sala, data, turno, status_reserva)
        VALUES (:id_usuario, :id_sala, :data, :turno, :status)";
$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id_usuario' => $id_usuario,
    ':id_sala' => $id_sala,
    ':data' => $data,
    ':turno' => $turno,
    ':status' => $status
]);

echo "Reserva registrada!";
?>
