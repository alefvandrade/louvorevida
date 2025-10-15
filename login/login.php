<?php
session_start();
require_once __DIR__ . "/../classes/Admin.class.php";
require_once __DIR__ . "/../classes/Integrantes.class.php";

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    // --- Verifica Admin ---
    $admin = new Admin();
    if ($admin->login($usuario, $senha)) {
        $_SESSION['admin'] = [
            'id' => $admin->getId(),
            'usuario' => $admin->getUsuario()
        ];
        header('Location: ../dashboard/dashboard.php');
        exit;
    }

    // --- Verifica Integrante ---
    $integrante = new Integrante();
    $dados = $integrante->login($usuario, $senha); // retorna array ou null
    if ($dados) {
        $_SESSION['integrante'] = $dados; // dados do próprio integrante
        header('Location: meu_cartao.php');
        exit;
    }

    $mensagem = "<div class='alert alert-danger text-center'>Usuário ou senha incorretos</div>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height:100vh">
        <div class="card p-4 shadow" style="width: 360px;">
            <h3 class="text-center mb-3">Login</h3>
            <?= $mensagem ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Usuário</label>
                    <input type="text" class="form-control" name="usuario" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" class="form-control" name="senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>