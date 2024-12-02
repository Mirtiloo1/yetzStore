<?php
session_start();
include '../conexao.php'; // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redireciona para login se não estiver logado
    exit();
}

// Verifica se o parâmetro compra_id foi passado pela URL
if (!isset($_GET['compra_id'])) {
    header("Location: index.php"); // Redireciona para a página principal se não houver ID de compra
    exit();
}

// Obtém o ID da compra da URL
$compra_id = $_GET['compra_id'];
$usuario_id = $_SESSION['usuario_id'];

// Recupera os detalhes da compra
$sql_compra = "SELECT * FROM historico_compras WHERE id = :compra_id AND usuario_id = :usuario_id";
$stmt = $pdo->prepare($sql_compra);
$stmt->bindParam(':compra_id', $compra_id, PDO::PARAM_INT);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se a compra existe
if (!$compra) {
    header("Location: historico_compras.php"); // Se a compra não for encontrada, redireciona para o histórico
    exit();
}

// Recupera os itens da compra
$sql_itens = "SELECT p.nome, ic.quantidade, ic.preco_unitario 
              FROM itens_compras ic 
              JOIN produtos p ON ic.produto_id = p.id 
              WHERE ic.compra_id = :compra_id";
$stmt = $pdo->prepare($sql_itens);
$stmt->bindParam(':compra_id', $compra_id, PDO::PARAM_INT);
$stmt->execute();
$itensCompra = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcula o preço total
$total = 0;
foreach ($itensCompra as $item) {
    $total += $item['preco_unitario'] * $item['quantidade'];
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Histórico de Compras - YetzStore</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            padding-top: 20px;
        }
    
        h2, h4 {
            color: black;
        }
        .table thead th {
            background-color: #1C1C1C;
            color: white;
        }

    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../nav-footer/navbar.php'; ?>

    <div class="container mt-5"><br>
        <h2 class="text-center">Resumo da Compra</h2>
        <hr>

        <!-- Detalhes da compra -->
        <div class="mb-4">
            <h4 class="mb-3">Detalhes do Pedido</h4>
            <p><strong>Endereço de Entrega:</strong> <?php echo htmlspecialchars($compra['endereco']); ?></p>
            <p><strong>Método de Pagamento:</strong> <?php echo htmlspecialchars($compra['metodo_pagamento']); ?></p>
            <p><strong>Data da Compra:</strong> <?php echo date('d/m/Y H:i:s', strtotime($compra['data_compra'])); ?></p>
        </div>

        <!-- Itens da compra -->
        <h4 class="mb-3">Itens Comprados</h4>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itensCompra as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nome']); ?></td>
                            <td><?php echo $item['quantidade']; ?></td>
                            <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Preço total -->
        <h4 class="mt-4 text-end text-primary">
            Preço Total: R$ <?php echo number_format($total, 2, ',', '.'); ?>
        </h4>

        <!-- Botão voltar -->
        <div class="d-flex justify-content-end mt-4">
            <a href="../index.php" class="btn btn-primary">Voltar para a Página Inicial</a>
        </div>
    </div>
    <?php include '../nav-footer/footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

