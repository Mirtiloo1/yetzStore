<?php
session_start();
require_once '../conexao.php';

// Função para calcular o total do carrinho
function calcularTotal() {
    $total = 0;
    if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $produto_id => $produto) {
            if (isset($produto['preco'], $produto['quantidade'])) {
                $total += $produto['preco'] * $produto['quantidade']; // Calcula com base na quantidade
            }
        }
    }
    return $total;
}

// Verificar se o carrinho existe na sessão
$carrinho = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
?>

<?php
// Inclui a navbar com o link de login/logout
include '../nav-footer/navbar.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carrinho - YetzStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* Estilo geral */
    body {
      background-color: #f8f9fa;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #343a40;
    }

    .table {
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Rodapé fixado na parte inferior */
    .cart-footer {
      background-color: #fff;
      padding: 20px;
      border-top: 2px solid #dee2e6;
      box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
      position: sticky;
      bottom: 0;
      z-index: 1000;
    }

    .cart-footer h3 {
      margin: 0;
      font-size: 1.5rem;
      color: #343a40;
    }

    .btn {
      font-size: 1rem;
    }

    /* Centraliza mensagens de carrinho vazio */
    .empty-cart {
      text-align: center;
      margin-top: 50px;
      color: #6c757d;
    }

    /* Ajuste responsivo para botões */
    @media (max-width: 576px) {
      .cart-footer .btn {
        width: 100%;
        margin-bottom: 10px;
      }

      .cart-footer h3 {
        text-align: center;
      }
    }
  </style>
</head>
<body>
<?php
// Inclui a navbar com o link de login/logout
include '../nav-footer/navbar.php';
?>
  <div class="container mt-5">
    <br><br><br><h2>Carrinho de Compras</h2>

    <?php if (count($carrinho) > 0): ?>
      <!-- Tabela responsiva -->
      <div class="table-responsive mb-5">
        <table class="table table-striped">
          <thead class="table-dark">
            <tr>
              <th>Produto</th>
              <th>Preço</th>
              <th>Quantidade</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $total = calcularTotal(); // Chama a função para calcular o total
            foreach ($carrinho as $produto_id => $produto):
              if (is_array($produto) && isset($produto['nome'], $produto['preco'], $produto['quantidade'])):
            ?>
              <tr>
                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                <td>
                  <form method="POST" action="../atualizar_quantidade.php" class="d-inline">
                    <input type="hidden" name="produto_id" value="<?php echo $produto_id; ?>" />
                    <button type="submit" name="acao" value="decrementar" class="btn btn-warning btn-sm">-</button>
                    <span class="px-3"><?php echo $produto['quantidade']; ?></span>
                    <button type="submit" name="acao" value="incrementar" class="btn btn-success btn-sm">+</button>
                  </form>
                </td>
                <td>
                  <form action="../remover_carrinho.php" method="POST">
                    <input type="hidden" name="produto_id" value="<?php echo $produto_id; ?>" />
                    <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                  </form>
                </td>
              </tr>
            <?php endif; endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-cart">
        <p>Seu carrinho está vazio. Adicione alguns produtos!</p>
        <a href="../index.php" class="btn btn-primary">Voltar para a loja</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Rodapé do carrinho -->
  <?php if (count($carrinho) > 0): ?>
    <div class="cart-footer container-fluid d-flex flex-wrap justify-content-between align-items-center">
      <h3>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>
      <div class="d-flex flex-wrap gap-3">
        <a href="../index.php" class="btn btn-primary">Continuar Comprando</a>
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
        <?php else: ?>
          <p class="text-danger m-0">Você precisa <a href="login.php">fazer login</a> para finalizar a compra.</p>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
  <?php include '../nav-footer/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</html>

