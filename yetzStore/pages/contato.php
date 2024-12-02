<?php
session_start();
include '../conexao.php'; // Inclui a conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YetzStore - Contato</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>

    .containerr{
        padding: 50px;
    }
    /* Estilo para o título da página */
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

    /* Container do formulário */
    .form-container {
      margin-top: 40px;
    }

    /* Estilo para o formulário */
    .form-group input,
    .form-group textarea {
      border-radius: 5px;
      border: 1px solid #ccc;
      padding: 12px;
      width: 100%;
      font-size: 1rem;
    }

    /* Estilo para o botão de envio */
    .btn-submit {
      background-color: #333;
      color: white;
      border: none;
      padding: 12px 20px;
      font-size: 1rem;
      cursor: pointer;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    .btn-submit:hover {
      background-color: #555;
    }

    /* Responsividade para dispositivos móveis */
    @media (max-width: 768px) {
        .containerr{
        padding: 0px;
    }
      h1 {
        font-size: 2rem;
      }

      p {
        font-size: 1rem;
      }

      .form-container {
        margin-top: 20px;
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
    <h1>Entre em Contato Conosco</h1>

    <!-- Descrição da página de contato -->
    <p>Se você tiver alguma dúvida, sugestão ou precisar de ajuda, fique à vontade para entrar em contato conosco. Preencha o formulário abaixo e nossa equipe responderá o mais rápido possível.</p>

    <!-- Formulário de contato -->
    <div class="form-container">
      <form action="contato_enviar.php" method="POST">
        <!-- Nome -->
        <div class="form-group mb-3">
          <label for="nome" class="form-label">Nome Completo</label>
          <input type="text" class="form-control" id="nome" name="nome" required>
        </div>

        <!-- E-mail -->
        <div class="form-group mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- Assunto -->
        <div class="form-group mb-3">
          <label for="assunto" class="form-label">Assunto</label>
          <input type="text" class="form-control" id="assunto" name="assunto" required>
        </div>

        <!-- Mensagem -->
        <div class="form-group mb-3">
          <label for="mensagem" class="form-label">Mensagem</label>
          <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
        </div>

        <!-- Botão de envio -->
        <button type="submit" class="btn-submit w-100">Enviar</button>
      </form>
    </div>
  </div>
  </div>
  <!-- Inclui o footer -->
  <?php include '../nav-footer/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
