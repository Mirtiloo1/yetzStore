<?php
session_start();
include 'conexao.php'; // Inclui a conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redireciona para login se não estiver logado
    exit();
}

// Obtém os dados do usuário logado
$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM usuarios WHERE id = :usuario_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    // Se o usuário não existir, redireciona para login
    header("Location: login.php");
    exit();
}

// Processa a atualização de dados do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualização de dados do usuário
    if (isset($_POST['atualizar_dados'])) {
        $novo_nome = $_POST['nome'];
        $novo_email = $_POST['email'];
        $novo_endereco = $_POST['endereco']; // Atualiza o endereço

        $sql = "UPDATE usuarios SET nome = :nome, email = :email, endereco = :endereco WHERE id = :usuario_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $novo_nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $novo_email, PDO::PARAM_STR);
        $stmt->bindParam(':endereco', $novo_endereco, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        // Atualiza as informações do usuário na sessão
        $_SESSION['usuario_nome'] = $novo_nome;
        $_SESSION['usuario_email'] = $novo_email;
        $_SESSION['usuario_endereco'] = $novo_endereco;

        echo "<script>alert('Dados atualizados com sucesso!');</script>";
    }

    // Exclusão de conta
    if (isset($_POST['excluir_conta'])) {
        // Exclui o usuário do banco de dados
        $sql = "DELETE FROM usuarios WHERE id = :usuario_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        // Destrói a sessão do usuário
        session_destroy();

        header("Location: pages/login.php"); // Redireciona para login após excluir conta
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - YetzStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff; /* Fundo branco */
            padding-top: 56px; /* Mantém a margem para navbar fixa */
            color: #333333; /* Texto em cinza escuro */
        }

        .formulario {
            max-width: 900px; /* Largura máxima do formulário */
            margin: auto; /* Centraliza o formulário horizontalmente */
        }

        .card{
            border: 2px solid #A7A7A7;
        }

        .btn-warning, .btn-danger, .btn-primary {
            width: 100%; /* Ajuste para que os botões ocupem toda a largura disponível em dispositivos pequenos */
            max-width: 170px; /* Limite de largura para evitar que os botões fiquem largos demais em telas grandes */
        }

        .grupo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        /* Responsividade adicional */
        @media (max-width: 768px) {
            .formulario {
                width: 100%; /* Adapta o formulário para tela menor */
                padding: 15px;
            }

            .grupo {
                flex-direction: column; /* Faz os botões ficarem empilhados em dispositivos menores */
                gap: 10px; /* Adiciona espaçamento entre os botões */
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'nav-footer/navbar.php'; ?>

    <div class="container mt-3">
        <h2>Minha Conta</h2><br>

        <!-- Exibe os dados do usuário -->
        <div class="formulario">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Dados do Usuário</h5><br>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuario['nome']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                    <p><strong>Endereço:</strong> <?php echo htmlspecialchars($usuario['endereco']); ?></p>
                </div>
            </div>

            <!-- Formulário para atualizar dados -->
            <h3>Atualizar Dados</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo htmlspecialchars($usuario['endereco']); ?>" required>
                </div>
                <button type="submit" name="atualizar_dados" class="btn btn-primary">Atualizar Dados</button><br><hr>
                <div class="grupo">
                    <a href="alterar_senha.php" class="btn btn-warning">Alterar Senha</a>
                    <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Esta ação é permanente e não poderá ser desfeita!');" style="margin: 0;">
                        <button type="submit" name="excluir_conta" class="btn btn-danger">Excluir Minha Conta</button>
                    </form>
                </div>
            </form>
        </div>
    </div><br><br>

    <?php include 'nav-footer/footer.php'; ?>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
