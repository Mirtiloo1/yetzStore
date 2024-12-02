<?php
session_start();
require_once 'conexao.php';

// Verifica se o produto e a ação foram passados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'], $_POST['acao'])) {
    $produto_id = $_POST['produto_id'];
    $acao = $_POST['acao'];
    $session_id = session_id();

    // Recupera a quantidade atual do produto
    $sql = "SELECT quantidade FROM carrinho WHERE produto_id = ? AND session_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$produto_id, $session_id]);
    $produto = $stmt->fetch();

    if ($produto) {
        $quantidade = $produto['quantidade'];

        // Modifica a quantidade
        if ($acao === 'incrementar') {
            $quantidade++;
        } elseif ($acao === 'decrementar' && $quantidade > 1) {
            $quantidade--;
        }

        // Atualiza a quantidade no banco de dados
        $sql = "UPDATE carrinho SET quantidade = ? WHERE produto_id = ? AND session_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantidade, $produto_id, $session_id]);

        // Atualiza a quantidade na sessão
        $_SESSION['carrinho'][$produto_id]['quantidade'] = $quantidade;
    }
}

header('Location: /yetzStore/pages/carrinho.php'); // Redireciona de volta para o carrinho
exit();
?>
