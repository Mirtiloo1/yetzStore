<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'])) {
    $produto_id = $_POST['produto_id'];
    $session_id = session_id();
    
    // Remover o produto da sessão
    if (isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
    }

    // Remover o produto do banco de dados
    $sql = "DELETE FROM carrinho WHERE produto_id = ? AND session_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$produto_id, $session_id]);
}

header('Location: /yetzStore/pages/carrinho.php'); // Redireciona de volta para o carrinho
exit();
?>
