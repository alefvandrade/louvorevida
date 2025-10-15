<?php
// Verifica se a senha foi enviada pelo formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'] ?? '';

    if (empty($senha)) {
        $mensagem = "❌ Digite uma senha!";
    } else {
        // Criptografa a senha usando bcrypt
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $mensagem = "✅ Senha criptografada: <strong>$hash</strong>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gerar Hash de Senha</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:500px;">
    <h2 class="mb-4 text-center">Gerar Hash de Senha</h2>
    
    <?php if (!empty($mensagem)) echo "<div class='alert alert-info'>$mensagem</div>"; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="text" class="form-control" name="senha" id="senha" placeholder="Digite a senha">
        </div>
        <button class="btn btn-primary w-100">Gerar Hash</button>
    </form>
</div>
</body>
</html>
