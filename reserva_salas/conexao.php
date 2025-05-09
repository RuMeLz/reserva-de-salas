<?php
//informações necessárias para fazer a conexão
$host = "localhost";
$dbname = "reserva_salas"; // Nome do banco de dados
$usuario = "root";
$senha = "";

//bloco try/catch para tentativa de conexão e captura de erros
try {
    // Criação da conexão do banco de dados usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);

    // Tem que definir o modo de erro como "exception" porque o padrão é modo "silent"
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se tiver um erro o comando "die" para o script e retorna a Exception de erro
    die("Erro na conexão: " . $e->getMessage());
}
?>

