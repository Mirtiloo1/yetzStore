<?php
session_start();
include '../conexao.php'; // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redireciona para login se não estiver logado
    exit();
}

// Obtém o ID do usuário
$usuario_id = $_SESSION['usuario_id'];

// Verifica se o produto_id foi passado na URL
$produto_id = isset($_GET['produto_id']) ? (int)$_GET['produto_id'] : 0;

if ($produto_id == 0) {
    // Se não houver produto_id, redireciona de volta à loja ou para o carrinho
    header("Location: index.php");
    exit();
}

// Recupera o endereço do usuário
$sql_endereco = "SELECT endereco FROM usuarios WHERE id = :usuario_id";
$stmt = $pdo->prepare($sql_endereco);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$endereco_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Inicializa a variável $itensCarrinho como um array vazio caso não haja produtos
$itensCarrinho = [];

if ($produto_id != 0) {
    // Recupera o item do carrinho baseado no produto_id
    $sql = "SELECT c.id AS carrinho_id, p.id AS produto_id, p.nome, p.preco, c.quantidade 
            FROM carrinho c 
            JOIN produtos p ON c.produto_id = p.id 
            WHERE c.usuario_id = :usuario_id AND c.produto_id = :produto_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
    $stmt->execute();
    $itensCarrinho = $stmt->fetchAll(PDO::FETCH_ASSOC); // Armazena os resultados
}

// Verifica se o item foi encontrado
if (empty($itensCarrinho)) {
    // Se o produto não estiver no carrinho, redireciona
    header("Location: carrinho.php");
    exit();
}

// Processa a finalização da compra
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Valida se os campos obrigatórios foram preenchidos
    if (empty($_POST['endereco']) || empty($_POST['pagamento'])) {
        $erro = "Por favor, preencha todos os campos obrigatórios!";
    } else {
        // Dados de entrega e pagamento
        $endereco_entrega = $_POST['endereco'];
        $metodo_pagamento = $_POST['pagamento'];

        // Insere a compra no histórico de compras
        $sql_compra = "INSERT INTO historico_compras (usuario_id, endereco, metodo_pagamento) 
                       VALUES (:usuario_id, :endereco, :metodo_pagamento)";
        $stmt = $pdo->prepare($sql_compra);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':endereco', $endereco_entrega, PDO::PARAM_STR);
        $stmt->bindParam(':metodo_pagamento', $metodo_pagamento, PDO::PARAM_STR);
        $stmt->execute();
        $compra_id = $pdo->lastInsertId(); // Obtém o ID da compra inserida

        // Adiciona o item no histórico de compras
        $sql_itens = "INSERT INTO itens_compras (compra_id, produto_id, quantidade, preco_unitario) 
                      VALUES (:compra_id, :produto_id, :quantidade, :preco)";
        $stmt = $pdo->prepare($sql_itens);
        $stmt->bindParam(':compra_id', $compra_id, PDO::PARAM_INT);
        $stmt->bindParam(':produto_id', $itensCarrinho[0]['produto_id'], PDO::PARAM_INT); // Usando produto_id correto
        $stmt->bindParam(':quantidade', $itensCarrinho[0]['quantidade'], PDO::PARAM_INT);
        $stmt->bindParam(':preco', $itensCarrinho[0]['preco'], PDO::PARAM_STR);
        $stmt->execute();

        // Limpa o carrinho após a compra
        $sql_limp_carrinho = "DELETE FROM carrinho WHERE usuario_id = :usuario_id";
        $stmt = $pdo->prepare($sql_limp_carrinho);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redireciona para a página de histórico de compras
        header("Location: historico_compras.php?compra_id=" . $compra_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <title>Checkout - YetzStore</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto';
        }
        .checkout-container {
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 22px;
            padding: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .total-row {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../nav-footer/navbar.php'; ?>

    <div class="container mt-5">
        <div class="checkout-container"><br>
            <h2 class="mb-4 text-center">Revise seus Itens e Finalize a Compra</h2><br>

            <!-- Exibe os itens do carrinho -->
            <div class="table-responsive mb-4">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($itensCarrinho as $item): 
                            $total += $item['preco'] * $item['quantidade'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nome']); ?></td>
                            <td><?php echo $item['quantidade']; ?></td>
                            <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="3" class="text-end">Total:</td>
                            <td>R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="mb-3">Dados de Entrega</h3>
            <form method="POST">
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço de Entrega</label>
                    <input type="text" name="endereco" id="endereco" class="form-control" 
                           value="<?php echo htmlspecialchars($endereco_usuario['endereco']); ?>" required>
                </div>

                <h3 class="mb-3">Método de Pagamento</h3>
                <div class="mb-3">
                    <select name="pagamento" class="form-select" required>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Boleto Bancário">Boleto Bancário</option>
                        <option value="Pix">Pix</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <h4>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h4>
                    <button type="submit" class="btn btn-success">Confirmar Compra</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
