<?php
// Inicia a sessão para poder destruí-la
session_start();

// Destroi todos os dados da sessão (logout completo)
session_destroy();

// Redireciona o usuário para a página inicial
header("Location: index.php");
exit;
?>
