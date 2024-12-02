<?php
// Inicia a sessão
session_start();

// Verifica se o usuário é administrador
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo json_encode(["error" => "Acesso negado!"]);
    exit();
}

// Conexão com o banco de dados
include 'conexao.php';

// Verifica se o ID do produto foi passado via POST
if (isset($_POST['id'])) {
    $produto_id = $_POST['id'];

    // Exclui o produto
    $sql = "DELETE FROM produtos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Produto excluído com sucesso!"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir o produto."]);
    }
} else {
    echo json_encode(["error" => "ID do produto não fornecido."]);
}
?>
