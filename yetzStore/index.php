<?php
session_start();
include 'conexao.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $produto_id = $_POST['produto_id'];
    $produto_nome = $_POST['produto_nome'];
    $produto_preco = $_POST['produto_preco'];


    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }


    if (isset($_SESSION['carrinho'][$produto_id])) {

        $_SESSION['carrinho'][$produto_id]['quantidade']++;
    } else {

        $_SESSION['carrinho'][$produto_id] = [
            'nome' => $produto_nome,
            'preco' => (float)$produto_preco,
            'quantidade' => 1
        ];
    }


    try {
        $session_id = session_id();
        $sql = "SELECT COUNT(*) FROM carrinho WHERE produto_id = :produto_id AND session_id = :session_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
        $stmt->execute();
        $produtoNoBanco = $stmt->fetchColumn();


        if ($produtoNoBanco > 0) {
            $sql = "UPDATE carrinho SET quantidade = quantidade + 1 WHERE produto_id = :produto_id AND session_id = :session_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
            $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
            $stmt->execute();
        } else {

            $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
            $sql = "INSERT INTO carrinho (usuario_id, produto_id, nome, preco, session_id, quantidade) 
                    VALUES (:usuario_id, :produto_id, :nome, :preco, :session_id, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $produto_nome, PDO::PARAM_STR);
            $stmt->bindParam(':preco', $produto_preco, PDO::PARAM_STR);
            $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
            $stmt->execute();
        }


        if ($_POST['add_to_cart'] === 'buy_now') {

            if (isset($_SESSION['usuario_id'])) {
                header("Location: /yetzstore/pages/checkout_unit.php?produto_id=$produto_id"); 
                exit();
            } else {
                header("Location: pages/login.php"); 
                exit();
            }
        }

    } catch (PDOException $e) {
        echo "Erro ao adicionar produto ao carrinho: " . $e->getMessage();
    }
}


$sql = "SELECT * FROM produtos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$produtos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>YetzStore - Página Principal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .banner {
      width: 100%;
      height: auto;
    }

    .card img {
      object-fit: cover;
      height: 200px;
    }

    footer {
      text-align: center;
      padding: 1rem 0;
    }

    .btn-primary {
      background: none;
      border: 1px solid black;
      color: black;
      transition: ease-in 0.1s;
    }

    .btn-primary:hover {
      background: #333333;
      border: 1px solid black;
      color: white;
    }

    .img-fluid {
      border-radius: 10px;
    }

    .text-container {
      padding: 20px;
      border-radius: 10px;
    }
  </style>
</head>
<body>

  <?php include 'nav-footer/navbar.php'; ?>

  <img src="assets/images/banner2.jpg" class="banner">

  <div class="container mt-4">
    <br><h1>Bem-vindo à YetzStore!</h1>
    <p>Aqui você encontra os melhores produtos com preços incríveis.</p><br>

    <div class="row g-4">
      <?php foreach ($produtos as $produto): ?>
        <div class="col-12 col-sm-6 col-md-4">
          <div class="card">
            <img src="<?php echo $produto['imagem']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($produto['nome']); ?></h5>
              <p class="card-text"><?php echo htmlspecialchars($produto['descricao']); ?></p>
              <p class="card-text">Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
              <form method="POST" action="index.php">
                <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                <input type="hidden" name="produto_nome" value="<?php echo htmlspecialchars($produto['nome']); ?>">
                <input type="hidden" name="produto_preco" value="<?php echo $produto['preco']; ?>">
                <button type="submit" name="add_to_cart" value="buy_now" class="btn btn-primary w-100 mb-2">Comprar Agora</button>
                <button type="submit" name="add_to_cart" value="add_to_cart" class="btn btn-secondary w-100">
                  <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div><br><br><hr>

<div class="container mt-5">
  <div class="row">
    <!-- Coluna com as imagens -->
    <div class="col-12 col-md-6 d-flex flex-column align-items-center">
      <div class="d-flex w-100 mb-3 gap-3 justify-content-center">
        <!-- Duas imagens lado a lado -->
        <img src="assets/images/sobre1.png" class="img-fluid" style="max-width: 50%; height: auto; border-radius: 10px;" alt="Imagem 1">
        <img src="assets/images/sobre2.jpeg" class="img-fluid" style="max-width: 50%; height: auto; border-radius: 10px;" alt="Imagem 2">
      </div>
      <!-- Imagem mais larga abaixo -->
      <img src="assets/images/sobre-larga2.jpg" class="img-fluid" style="max-width: 100%; height: auto; border-radius: 10px;" alt="Imagem Larga">
    </div>

    <!-- Coluna com o texto e botão -->
    <div class="col-12 col-md-6 d-flex flex-column align-items-start justify-content-center text-container mx-auto" style="max-width: 500px;">
      <h1>Sobre Nós</h1>
      <p>
        A YetzStore é especializada em tênis, oferecendo produtos modernos, sofisticados e de alta qualidade. 
        Nosso objetivo é garantir conforto e estilo em cada par, para todos os gostos e ocasiões. 
        Descubra uma experiência única de compra com a YetzStore.
      </p>
      <a href="pages/sobre.php" class="btn btn-primary">Saiba Mais</a>
    </div>
  </div>
</div>

  <br><br><br>
    <!-- Carrossel de Depoimentos -->
    <div id="testimonialsCarousel" class="carousel slide mt-5" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <blockquote class="blockquote text-center">
            <p class="mb-0">"A YetzStore tem os melhores tênis! Conforto e estilo em um só lugar."</p>
            <footer class="blockquote-footer">João Silva</footer>
          </blockquote>
        </div>
        <div class="carousel-item">
          <blockquote class="blockquote text-center">
            <p class="mb-0">"Nunca pensei que compraria tênis tão incríveis online. Estou apaixonado!"</p>
            <footer class="blockquote-footer">Maria Oliveira</footer>
          </blockquote>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Próximo</span>
      </button>
    </div>
  </div>
  <?php include 'nav-footer/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
