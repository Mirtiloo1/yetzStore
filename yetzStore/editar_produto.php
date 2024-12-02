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

// Verifica se o ID do produto foi passado
if (isset($_GET['id'])) {
    $produto_id = $_GET['id'];

    // Busca o produto no banco de dados
    $sql = "SELECT * FROM produtos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
    $stmt->execute();
    $produto = $stmt->fetch();

    if (!$produto) {
        echo "Produto não encontrado!";
        exit();
    }
}

// Processa o formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];

    // Verifica se a imagem foi enviada
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem_nome = $_FILES['imagem']['name'];
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_destino = 'assets/images/' . $imagem_nome;

        if (move_uploaded_file($imagem_tmp, $imagem_destino)) {
            $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, imagem = :imagem, categoria = :categoria WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
            $stmt->bindParam(':imagem', $imagem_destino, PDO::PARAM_STR);
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
            $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
            $stmt->execute();

            $mensagem = "Produto atualizado com sucesso!";
        } else {
            $mensagem = "Erro ao mover a imagem.";
        }
    } else {
        $sql = "UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, categoria = :categoria WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
        $stmt->execute();

        $mensagem = "Produto atualizado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Produto - YetzStore</title>
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
    <h1>Editar Produto</h1>

    <?php if (!empty($mensagem)): ?>
      <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
        <?php echo $mensagem; ?>
      </div>
    <?php endif; ?>

    <form action="editar_produto.php?id=<?php echo $produto['id']; ?>" method="POST" enctype="multipart/form-data">
      <label for="nome">Nome do Produto:</label>
      <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>

      <label for="descricao">Descrição do Produto:</label>
      <textarea name="descricao" id="descricao" rows="4" required><?php echo htmlspecialchars($produto['descricao']); ?></textarea>

      <label for="preco">Preço:</label>
      <input type="text" name="preco" id="preco" value="<?php echo htmlspecialchars($produto['preco']); ?>" required>

      <label for="categoria">Categoria:</label>
      <input type="text" name="categoria" id="categoria" value="<?php echo htmlspecialchars($produto['categoria']); ?>" required>

      <label for="imagem">Imagem do Produto:</label>
      <input type="file" name="imagem" id="imagem">

      <button type="submit">Salvar Alterações</button>
    </form>

    <a href="index.php" class="back-button">Voltar para a Home</a>
  </div>

</body>
</html>
