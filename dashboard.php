<?php
session_start(); // Inicia a sessão
include("conexao.php"); // Conecta com o banco

// Se a sessão tiver valor nulo redireciona
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

echo "<h1>Bem-vindo!</h1>";

// Busca todas as salas disponíveis
$stmt = $pdo->query("SELECT * FROM salas");
$salas = $stmt->fetchAll(); // Retorna todas as salas

// Formulário para reservar sala
echo "<form method='POST' action='reserva.php'>";
echo "Data: <input type='date' name='data'><br>";
echo "Turno: <select name='turno'>
        <option value='1'>Manhã</option>
        <option value='2'>Tarde</option>
        <option value='3'>Noite</option></select><br>";

echo "<select name='id_sala'>";
foreach ($salas as $sala) {
    echo "<option value='{$sala['id']}'>
            {$sala['nome']} - ({$sala['status']})
          </option>";
}

echo "</select><br>";

echo "<button type='submit'>Reservar</button></form>";
?>
