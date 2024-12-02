<?php
// Inicia a sessão
session_start();

// Verifica se o usuário é administrador
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "Acesso negado!";
    exit();
}

// Conexão com o banco de dados
include 'conexao.php';

// Captura o filtro de categoria (se houver)
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Monta a consulta SQL
$sql = "SELECT * FROM produtos";
if ($categoria_filtro) {
    $sql .= " WHERE categoria LIKE :categoria";
}

// Executa a consulta
$stmt = $pdo->prepare($sql);
if ($categoria_filtro) {
    $stmt->bindValue(':categoria', '%' . $categoria_filtro . '%');
}
$stmt->execute();
$produtos = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listar Produtos - YetzStore</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
      color: #333;
    }

    .container {
      max-width: 95%;
      margin: 20px auto;
      padding: 20px;
      background: #ffffff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    h1 {
      text-align: center;
      font-size: 28px;
      color: #007BFF;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      overflow-x: auto;
    }

    table th, table td {
      text-align: left;
      padding: 12px;
      border: 1px solid #ddd;
    }

    table th {
      background-color: #007BFF;
      color: #fff;
    }

    table td img {
      max-width: 80px;
      height: auto;
      border-radius: 4px;
    }

    table tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    table tr:hover {
      background-color: #e9f5ff;
    }

    table a {
      color: #007BFF;
      text-decoration: none;
      margin: 0 5px;
    }

    table a:hover {
      text-decoration: underline;
    }

    .back-button {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #6c757d;
      color: #fff;
      text-decoration: none;
      text-align: center;
      border-radius: 4px;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .back-button:hover {
      background-color: #5a6268;
    }

    .delete-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .delete-btn:hover {
      background-color: #c82333;
    }

    .notification {
      position: fixed;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
      padding: 10px;
      background-color: #28a745;
      color: white;
      border-radius: 5px;
      display: none;
      z-index: 1000;
      font-size: 14px;
    }

    .notification.error {
      background-color: #dc3545;
    }

    @media (max-width: 768px) {
      table th, table td {
        font-size: 12px;
        padding: 8px;
      }

      .back-button {
        padding: 8px 16px;
        font-size: 14px;
      }

      .delete-btn {
        padding: 6px 10px;
        font-size: 14px;
      }
    }

    @media (max-width: 480px) {
      .container {
        margin: 10px;
        padding: 15px;
      }

      table td img {
        max-width: 60px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Lista de Produtos</h1>
    <a href="index.php" class="back-button">Voltar para a Home</a>

    <div id="notification" class="notification"></div>

    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Descrição</th>
          <th>Preço</th>
          <th>Categoria</th>
          <th>Imagem</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produtos as $produto): ?>
          <tr id="produto-<?php echo $produto['id']; ?>">
            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
            <td><?php echo htmlspecialchars($produto['descricao']); ?></td>
            <td><?php echo "R$ " . number_format($produto['preco'], 2, ',', '.'); ?></td>
            <td><?php echo htmlspecialchars($produto['categoria']); ?></td>
            <td><img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="Imagem"></td>
            <td>
              <a href="/yetzstore/editar_produto.php?id=<?php echo $produto['id']; ?>">Editar</a> |
              <button class="delete-btn" data-id="<?php echo $produto['id']; ?>">Excluir</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script>
  function showNotification(message, isError = false) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.classList.toggle('error', isError);
    notification.style.display = 'block';

    setTimeout(() => {
      notification.style.display = 'none';
    }, 3000);
  }

  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
      const produtoId = this.getAttribute('data-id');

      if (confirm('Tem certeza que deseja excluir este produto?')) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'excluir_produto.php', true); // Alterado para 'POST'
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Cabeçalho para enviar dados como URL encoded
        xhr.onload = function() {
          const response = JSON.parse(xhr.responseText);

          if (response.success) {
            document.getElementById('produto-' + produtoId).remove();
            showNotification(response.message);
          } else {
            showNotification(response.error, true);
          }
        };
        // Envia o ID do produto via POST
        xhr.send('id=' + produtoId);
      }
    });
  });
</script>

  
</body>
</html>

