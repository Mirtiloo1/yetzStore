<?php
session_start();
include 'conexao.php'; // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Processa a alteração de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Obtém os dados do usuário logado
    $sql = "SELECT senha FROM usuarios WHERE id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se a senha atual está correta
    if (password_verify($senha_atual, $usuario['senha'])) {
        // Verifica se as senhas novas são iguais
        if ($nova_senha === $confirmar_senha) {
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            // Atualiza a senha no banco de dados
            $sql = "UPDATE usuarios SET senha = :senha WHERE id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':senha', $nova_senha_hash, PDO::PARAM_STR);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            echo "<script>alert('Senha alterada com sucesso!'); window.location.href = 'user.php';</script>";
        } else {
            echo "<script>alert('As senhas não coincidem!');</script>";
        }
    } else {
        echo "<script>alert('Senha atual incorreta!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Alterar Senha - YetzStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
    h2 {
        text-align: center;
        margin:0;
    }
    .form-control {
        width: 100%;
    }
    .btn-dark{
        width:100%
    }
    .form-container {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding:30px;
        border:2px solid #A7A7A7;
        max-width: 410px;
        margin: 0 auto;
    }
    </style>
</head>
<body>
    <?php include 'nav-footer/navbar.php'; ?>
    <br><br><br>
    <div class="container">
        <div class="form-container mt-5">
            <form method="POST">
            <h2>Alterar Senha</h2><br>
                <div class="mb-3">
                    <label for="senha_atual" class="form-label">Senha Atual</label>
                    <input type="password" name="senha_atual" id="senha_atual" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="nova_senha" class="form-label">Nova Senha</label>
                    <input type="password" name="nova_senha" id="nova_senha" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                    <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark">Alterar Senha</button>
            </form>
        </div>
    </div>
</body>
</html>

    <?php include 'nav-footer/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
