<?php
require_once '../conexao.php';
session_start();  // Inicia a sessão
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

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifica se o usuário existe
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Usuário encontrado, verifica a senha
        $user = $result->fetch_assoc();
        if (password_verify($senha, $user['senha'])) {
            // Senha correta, loga o usuário
            $_SESSION['usuario_id'] = $user['id'];  // Criação da sessão
            $_SESSION['usuario_nome'] = $user['nome'];  // Nome do usuário

            // Se o usuário for admin (is_admin = 1), define $_SESSION['admin'] como true
            if ($user['is_admin'] == 1) {
                $_SESSION['admin'] = true;
            } else {
                $_SESSION['admin'] = false;  // Caso contrário, define como falso
            }

            // Redireciona para a página principal ou painel administrativo
            header("Location: ../index.php");  // Redireciona para o index.php
            exit();
        } else {
            // Senha incorreta
            $erro = "Senha incorreta. Tente novamente.";
        }
    } else {
        // Usuário não encontrado
        $erro = "Usuário não encontrado. Verifique o e-mail.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - YetzStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(135deg, #007BFF, #6C757D);
      color: #fff;
      margin: 0;
    }

    .login-card {
      background: #fff;
      color: #333;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
    }

    .login-card h2 {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .login-card .btn-primary {
      background-color: #007BFF;
      border: none;
      transition: background-color 0.3s ease;
    }

    .login-card .btn-primary:hover {
      background-color: #0056b3;
    }

    .login-card a {
      color: #007BFF;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .login-card a:hover {
      color: #0056b3;
      text-decoration: underline;
    }

    .alert {
      text-align: center;
    }

    @media (max-width: 576px) {
      .login-card {
        padding: 1.5rem;
      }

      .login-card h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h2>Login</h2>

    <?php if (isset($erro)): ?>
      <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3">
        <label for="senha" class="form-label">Senha</label>
        <input type="password" class="form-control" id="senha" name="senha" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">Entrar</button>
    </form>

    <p class="mt-3 text-center">
      Não tem uma conta? <a href="cadastro.php">Cadastre-se</a>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

