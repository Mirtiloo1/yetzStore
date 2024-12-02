<?php
session_start();
include '../conexao.php'; // Inclui a conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YetzStore - Sobre Nós</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  
  <style>
    /* Estilo para o título principal */
    h1 {
      font-size: 2.5rem;
      color: #333;
      font-weight: bold;
      text-align: center;
      margin-bottom: 20px;
    }

    /* Estilo para os parágrafos */
    p {
      font-size: 1.2rem;
      color: #555;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    /* Ajustes no container para espaçamento e alinhamento */
    .containerr {
      padding-top: 100px;
      padding-bottom: 40px;
    }

    /* Estilo para o banner */
    .banner {
      width: 100%;
      height: auto;
      margin-bottom: 30px;
    }

    /* Personalização de ícones */
    .bi {
      font-size: 1.5rem;
      color: #333;
    }

    img{
      border-radius: 20px;
    }

    /* Responsividade para dispositivos móveis */
    @media (max-width: 768px) {
      h1 {
        font-size: 2rem;
      }

      p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- Inclui a navbar -->
  <?php include '../nav-footer/navbar.php'; ?>

  <div class="container">
  <div class="containerr mt-4">
    <!-- Título da página -->
    <h1>Sobre a YetzStore</h1>

    <!-- Descrição da loja -->
    <p>A YetzStore é uma loja online especializada em oferecer produtos de alta qualidade com preços acessíveis. Nosso objetivo é proporcionar uma experiência de compra agradável e segura para nossos clientes.</p>
    <p>Com uma variedade de produtos, garantimos sempre a melhor qualidade e um ótimo atendimento. Trabalhamos constantemente para inovar e melhorar nossos serviços para garantir a satisfação de todos os nossos consumidores.</p>

    <!-- Imagem de banner (opcional, você pode adicionar uma imagem relevante aqui) -->
    <img src="../assets/images/banner2.jpg" class="banner" alt="Banner YetzStore">
  </div>
  </div>
  <!-- Inclui o footer -->
  <?php include '../nav-footer/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
