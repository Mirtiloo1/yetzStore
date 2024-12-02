<?php
// Inicia a sessão
session_start();

// Verifica se o usuário é administrador
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "Acesso negado! Você não tem permissão para acessar esta página.";
    exit();
}

// Conexão com o banco de dados
include 'conexao.php';

// Variável para mensagens de feedback
$mensagem = "";

// Processa o formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];

    // Verifica se a imagem foi enviada
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        // Define o caminho da imagem
        $imagem_nome = $_FILES['imagem']['name'];
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_destino = 'assets/images/' . $imagem_nome;

        // Move a imagem para o diretório correto
        if (move_uploaded_file($imagem_tmp, $imagem_destino)) {
            // Insere o produto no banco de dados
            try {
                $sql = "INSERT INTO produtos (nome, descricao, preco, imagem, categoria) 
                        VALUES (:nome, :descricao, :preco, :imagem, :categoria)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
                $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
                $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
                $stmt->bindParam(':imagem', $imagem_destino, PDO::PARAM_STR);
                $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
                $stmt->execute();

                $mensagem = "Produto cadastrado com sucesso!";
            } catch (PDOException $e) {
                $mensagem = "Erro ao cadastrar produto: " . $e->getMessage();
            }
        } else {
            $mensagem = "Erro ao mover a imagem.";
        }
    } else {
        $mensagem = "Imagem não enviada ou erro ao enviar imagem.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/style.css">
  <title>Cadastrar Produto - YetzStore</title>
  <style>

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
      color: #333;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      padding: 20px;
      background: #ffffff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    .container h1 {
      font-size: 24px;
      color: #007BFF;
      text-align: center;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    label {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 5px;
    }

    input, textarea, button {
      font-size: 16px;
      margin-bottom: 15px;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      width: 100%;
      box-sizing: border-box;
    }

    input:focus, textarea:focus {
      border-color: #007BFF;
      outline: none;
    }

    textarea {
      resize: vertical;
    }

    button {
      background-color: #007BFF;
      color: #fff;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    .back-button {
      display: inline-block;
      text-align: center;
      background-color: #6c757d;
      color: #fff;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 4px;
      margin-top: 20px;
      transition: background-color 0.3s ease;
    }

    .back-button:hover {
      background-color: #5a6268;
    }


    .mensagem {
      margin-bottom: 20px;
      padding: 15px;
      border-radius: 4px;
      font-size: 16px;
    }

    .mensagem.sucesso {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .mensagem.erro {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }


    @media (max-width: 600px) {
      .container {
        margin: 20px;
        padding: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Cadastrar Novo Produto</h1>

    <?php if (!empty($mensagem)): ?>
      <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
        <?php echo $mensagem; ?>
      </div>
    <?php endif; ?>

    <form action="cadastrar_produto.php" method="POST" enctype="multipart/form-data">
      <label for="nome">Nome do Produto:</label>
      <input type="text" name="nome" id="nome" required>

      <label for="descricao">Descrição do Produto:</label>
      <textarea name="descricao" id="descricao" rows="4" required></textarea>

      <label for="preco">Preço:</label>
      <input type="text" name="preco" id="preco" required>

      <label for="categoria">Categoria:</label>
      <input type="text" name="categoria" id="categoria" required>

      <label for="imagem">Imagem do Produto:</label>
      <input type="file" name="imagem" id="imagem" required>

      <button type="submit">Cadastrar Produto</button>
    </form>


    <a href="index.php" class="back-button">Voltar para a Home</a>
  </div>
</body>
</html>
