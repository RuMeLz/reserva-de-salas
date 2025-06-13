<?php
require '../conexao.php';
require '../dompdf/dompdf-3.1.0/dompdf/autoload.inc.php'; // ajuste o caminho se necessário

use Dompdf\Dompdf;

if (!isset($_GET['tipo'])) {
    die("Tipo de relatório não especificado.");
}

$tipo = $_GET['tipo'];
$html = '';

if ($tipo === 'reservas') {
    $stmt = $pdo->query("
        SELECT r.id_reserva, r.data, r.nome_turno, r.status_reserva, r.observacoes,
               s.nome AS sala, u.nome AS usuario
        FROM reservas r
        JOIN salas s ON r.id_sala = s.id_sala
        JOIN usuarios u ON r.id_usuario = u.id_usuario
    ");
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html .= "<h2>Relatório de Reservas</h2><table border='1' cellpadding='5'>
        <tr><th>ID</th><th>Data</th><th>Turno</th><th>Status</th><th>Observações</th><th>Sala</th><th>Usuário</th></tr>";

    foreach ($dados as $d) {
        $status = match ($d['status_reserva']) {
            1 => 'Pendente',
            2 => 'Aceita',
            3 => 'Rejeitada',
            default => 'Desconhecido'
        };
        $html .= "<tr>
            <td>{$d['id_reserva']}</td>
            <td>{$d['data']}</td>
            <td>{$d['nome_turno']}</td>
            <td>{$status}</td>
            <td>{$d['observacoes']}</td>
            <td>{$d['sala']}</td>
            <td>{$d['usuario']}</td>
        </tr>";
    }
    $html .= "</table>";

} elseif ($tipo === 'usuarios') {
    $stmt = $pdo->query("SELECT id_usuario, nome, email, senha_hash, tipo FROM usuarios");
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html .= "<h2>Relatório de Usuários</h2><table border='1' cellpadding='5'>
        <tr><th>ID</th><th>Nome</th><th>Email</th><th>Senha</th><th>Tipo</th></tr>";

    foreach ($dados as $d) {
        $html .= "<tr>
            <td>{$d['id_usuario']}</td>
            <td>{$d['nome']}</td>
            <td>{$d['email']}</td>
            <td>{$d['senha_hash']}</td>
            <td>{$d['tipo']}</td>
        </tr>";
    }
    $html .= "</table>";

} else {
    die("Tipo de relatório inválido.");
}

// Gerar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Opcional) Definir tamanho e orientação do papel
$dompdf->setPaper('A4', 'portrait');

// Renderizar e gerar download
$dompdf->render();
$dompdf->stream("relatorio_{$tipo}.pdf", ["Attachment" => true]);
exit;
