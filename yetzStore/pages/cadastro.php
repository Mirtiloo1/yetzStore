<?php
session_start();
ob_start();  // Para evitar erros de cabeçalho

// Conexão com o banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "yetzStore";

$conn = new mysqli($host, $user, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
    $endereco = $_POST['endereco'];  // Captura o endereço enviado pelo formulário

    // Verifica se o e-mail já está cadastrado
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $erro = "E-mail já cadastrado. Tente outro.";
    } else {
        // Insere o novo usuário com o endereço
        $sql = "INSERT INTO usuarios (nome, email, senha, endereco) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nome, $email, $senha, $endereco);
        $stmt->execute();

        // Redireciona para a página de login
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro - YetzStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(135deg, #6C757D, #007BFF);
      color: #fff;
      margin: 0;
    }

    .register-card {
      background: #fff;
      color: #333;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
    }

    .register-card h2 {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .register-card .btn-primary {
      background-color: #007BFF;
      border: none;
      transition: background-color 0.3s ease;
    }

    .register-card .btn-primary:hover {
      background-color: #0056b3;
    }

    .register-card a {
      color: #007BFF;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .register-card a:hover {
      color: #0056b3;
      text-decoration: underline;
    }

    .alert {
      text-align: center;
    }

    @media (max-width: 576px) {
      .register-card {
        padding: 1.5rem;
      }

      .register-card h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="register-card">
    <h2>Cadastro</h2>

    <?php if (isset($erro)): ?>
      <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST" action="cadastro.php">
      <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3">
        <label for="senha" class="form-label">Senha</label>
        <input type="password" class="form-control" id="senha" name="senha" required>
      </div>
      <div class="mb-3">
        <label for="endereco" class="form-label">Endereço</label>
        <input type="text" class="form-control" id="endereco" name="endereco" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">Cadastrar</button>
    </form>

    <p class="mt-3 text-center">
      Já tem uma conta? <a href="login.php">Faça login</a>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
