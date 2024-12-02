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

// Recupera o endereço do usuário
$sql_endereco = "SELECT endereco FROM usuarios WHERE id = :usuario_id";
$stmt = $pdo->prepare($sql_endereco);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$endereco_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Recupera os itens do carrinho do usuário
$sql = "SELECT c.id AS carrinho_id, p.id AS produto_id, p.nome, p.preco, c.quantidade 
        FROM carrinho c 
        JOIN produtos p ON c.produto_id = p.id 
        WHERE c.usuario_id = :usuario_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$itensCarrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica se o carrinho está vazio
if (empty($itensCarrinho)) {
    header("Location: carrinho.php"); // Se o carrinho estiver vazio, redireciona para o carrinho
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

        // Adiciona os itens no histórico de compras
        foreach ($itensCarrinho as $item) {
            // Verifica se o produto existe na tabela produtos
            $sql_check_produto = "SELECT COUNT(*) FROM produtos WHERE id = :produto_id";
            $stmt = $pdo->prepare($sql_check_produto);
            $stmt->bindParam(':produto_id', $item['produto_id'], PDO::PARAM_INT);
            $stmt->execute();
            $produtoExistente = $stmt->fetchColumn();

            if ($produtoExistente > 0) {
                // Insere os itens no histórico de compras
                $sql_itens = "INSERT INTO itens_compras (compra_id, produto_id, quantidade, preco_unitario) 
                              VALUES (:compra_id, :produto_id, :quantidade, :preco)";
                $stmt = $pdo->prepare($sql_itens);
                $stmt->bindParam(':compra_id', $compra_id, PDO::PARAM_INT);
                $stmt->bindParam(':produto_id', $item['produto_id'], PDO::PARAM_INT); // Usando produto_id correto
                $stmt->bindParam(':quantidade', $item['quantidade'], PDO::PARAM_INT);
                $stmt->bindParam(':preco', $item['preco'], PDO::PARAM_STR);
                $stmt->execute();
            } else {
                // O produto não existe, você pode registrar um erro ou pular a inserção
                echo "Erro: Produto com ID " . $item['produto_id'] . " não encontrado no banco de dados.";
            }
        }

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
    <link rel="stylesheet" href="assets/style.css">
    <title>Checkout - YetzStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Poppins';
        }
        .container {
            max-width: 1200px;
        }
        h2, h3 {
            color: #333;
            margin-bottom: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .form-control {
            border-radius: 8px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .total-section {
            border-top: 2px solid #ddd;
            padding-top: 20px;
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .btn-success {
                width: 100%;
            }
            .d-flex {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../nav-footer/navbar.php'; ?>

    <div class="container mt-5 bg-white p-4 shadow-sm rounded">
        <h2 class="text-center">Revise seus Itens e Finalize a Compra</h2>

        <!-- Exibe os itens do carrinho -->
        <div class="table-responsive">
            <table class="table">
                <thead>
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
                </tbody>
            </table>
        </div>

        <h3 class="mt-4">Dados de Entrega</h3>
        <form method="POST">
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço de Entrega</label>
                <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo htmlspecialchars($endereco_usuario['endereco']); ?>" required>
            </div>

            <h3 class="mt-4">Método de Pagamento</h3>
            <div class="mb-3">
                <select name="pagamento" class="form-control" required>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Boleto Bancário">Boleto Bancário</option>
                    <option value="Pix">Pix</option>
                </select>
            </div>

            <div class="d-flex justify-content-between align-items-center total-section">
                <h4>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h4>
                <button type="submit" class="btn btn-success">Confirmar Compra</button>
            </div>
        </form>
    </div>
    <?php include '../nav-footer/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

