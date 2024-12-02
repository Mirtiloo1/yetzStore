<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/yetzStore/conexao.php');


// Definir a variável $paginaAtual
$paginaAtual = basename($_SERVER['PHP_SELF']); // Obtém o nome do arquivo atual

// Recupera a quantidade de itens no carrinho
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$session_id = session_id();

// Consulta para contar os itens no carrinho com base no session_id
if ($usuario_id) {
    // Caso o usuário esteja logado, conta com o usuario_id e session_id
    $sql_count = "SELECT COUNT(*) AS total_items FROM carrinho WHERE session_id = :session_id AND usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql_count);
    $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
} else {
    // Caso o usuário não esteja logado, conta apenas com o session_id
    $sql_count = "SELECT COUNT(*) AS total_items FROM carrinho WHERE session_id = :session_id AND usuario_id IS NULL";
    $stmt = $pdo->prepare($sql_count);
    $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
}

$stmt->execute();
$result = $stmt->fetch();

$carrinhoQuantidade = $result['total_items'] ?: 0;

// Verifica se o usuário logado é um administrador
$isAdmin = false;
if (isset($_SESSION['usuario_id'])) {
    $sql = "SELECT is_admin FROM usuarios WHERE id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();
    $isAdmin = ($user['is_admin'] == 1); // Se is_admin for 1, é administrador
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YetzStore</title>
    <link rel="stylesheet" href="/yetzStore/assets/style.css"> <!-- Aqui é onde você inclui o CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap Icons -->
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
  <div class="container">
    <a class="navbar-brand" href="/yetzStore/index.php">YetzStore</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="/yetzStore/index.php">Home</a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="/yetzStore/pages/carrinho.php">
            Carrinho <span class="badge bg-black"><?php echo $carrinhoQuantidade; ?></span>
          </a>
        </li>
        <li class="nav-item">
              <a class="nav-link" href="/yetzStore/pages/sobre.php">Sobre Nós</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="/yetzStore/pages/contato.php">Contato</a>
          </li>
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <!-- Ícone de usuário -->
          <li class="nav-item d-flex align-items-center">
            <a class="nav-link" href="/yetzStore/user.php" style="padding-top:0;padding-bottom:1;">
            <span class="usuario">Usuario</span><i class="bi bi-person-circle" style="font-size: 1.5rem;"></i> <!-- Ajuste de tamanho do ícone -->
            </a>
          </li>
          <?php if ($isAdmin): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarAdminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Admin
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarAdminDropdown">
                <li><a class="dropdown-item" href="/yetzStore/cadastrar_produto.php">Cadastrar Produto</a></li>
                <li><a class="dropdown-item" href="/yetzStore/listar_produtos.php">Listar Produtos</a></li>
              </ul>
            </li>
          <?php endif; ?>
          <li class="nav-item" style="padding-left:20px;">
            <form action="/yetzStore/logout.php" method="POST">
              <button type="submit" class="btn-logout">Logout</button>
            </form>
          </li>
        <?php else: ?>
          <?php if ($paginaAtual != "login.php"): ?>
            <li class="nav-item">
              <a class="nav-link" href="/yetzStore/pages/login.php">Login</a>
            </li>
          <?php endif; ?>
          <?php if ($paginaAtual == "login.php"): ?>
            <li class="nav-item">
              <a class="nav-link" href="cadastro.php">Cadastro</a>
            </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>



<!-- Lembre-se de adicionar o Bootstrap Icons CDN -->
<!-- Inclua no head do seu HTML -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
