<?php
require_once __DIR__ . '/../classes/Conexao.class.php';
require_once __DIR__ . '/../classes/Admin.class.php';
// session_start();
// if (!isset($_SESSION['admin'])) {
//     header('Location: login.php');
//     exit;
// }
$admin = new Admin();
$admin->carregarPorId(1);
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioAtual = trim($_POST['usuarioAtual'] ?? '');
    $senhaAtual = trim($_POST['senhaAtual'] ?? '');
    $novaSenha = trim($_POST['novaSenha'] ?? '');
    $confirmarSenha = trim($_POST['confirmarSenha'] ?? '');
    if ($usuarioAtual !== $admin->getUsuario()) {
        $mensagem = '<div class="alert alert-warning">Usuário atual incorreto</div>';
    } elseif (!password_verify($senhaAtual, $admin->getSenha())) {
        $mensagem = '<div class="alert alert-warning">Senha atual incorreta</div>';
    } elseif ($novaSenha && $novaSenha !== $confirmarSenha) {
        $mensagem = '<div class="alert alert-warning">Nova senha e confirmação não coincidem</div>';
    } else {
        $admin->setUsuario($usuarioAtual);
        if ($novaSenha)
            $admin->setSenha($novaSenha);
        $admin->atualizar() ? $mensagem = '<div class="alert alert-success">Dados atualizados!</div>' : $mensagem = '<div class="alert alert-danger">Erro ao atualizar</div>';
    }
}
require_once __DIR__ . '/../dashboard/_header.php';
?>
<main class="container my-5" style="max-width:600px;">
    <h1 class="text-center mb-4">Editar Perfil</h1>
    <?= $mensagem ?>
    <form method="POST">
        <div class="mb-3 position-relative">
            <label>Usuário Atual</label>
            <input type="text" class="form-control" name="usuarioAtual"
                value="<?= htmlspecialchars($admin->getUsuario()) ?>" required>
        </div>
        <div class="mb-3 position-relative">
            <label>Senha Atual</label>
            <input type="password" class="form-control" id="senhaAtual" name="senhaAtual" required>
            <i class="bi bi-eye position-absolute" style="top:38px; right:12px; cursor:pointer; color:black;"
                onclick="togglePassword('senhaAtual',this)"></i>
        </div>
        <div class="mb-3 position-relative">
            <label>Nova Senha</label>
            <input type="password" class="form-control" id="novaSenha" name="novaSenha">
            <i class="bi bi-eye position-absolute" style="top:38px; right:12px; cursor:pointer; color:black;"
                onclick="togglePassword('novaSenha',this)"></i>
        </div>
        <div class="mb-3 position-relative">
            <label>Confirmar Nova Senha</label>
            <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha">
            <i class="bi bi-eye position-absolute" style="top:38px; right:12px; cursor:pointer; color:black;"
                onclick="togglePassword('confirmarSenha',this)"></i>
        </div>
        <button class="btn btn-primary w-100">Salvar Alterações</button>
    </form>
</main>
<?php require_once __DIR__ . '/../dashboard/_footer.php'; ?>
<script>
    function togglePassword(id, inputIcon) {
        const field = document.getElementById(id);
        if (field.type === 'password') {
            field.type = 'text';
            inputIcon.className = 'bi bi-eye-slash position-absolute';
            inputIcon.style.color = 'black'; // mantém sempre preto
        } else {
            field.type = 'password';
            inputIcon.className = 'bi bi-eye position-absolute';
            inputIcon.style.color = 'black'; // mantém sempre preto
        }
    }
</script>