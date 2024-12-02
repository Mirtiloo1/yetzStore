<?php
session_start(); // Inicia a sessão

// Destrói todas as variáveis de sessão
session_unset();

// Destroi a sessão
session_destroy();

// Redireciona para a página inicial ou de login após o logout
header("Location: /yetzStore/index.php");
exit();
