<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../classes/Cabecalho.class.php';
require_once __DIR__ . '/../classes/Admin.class.php';

// if (empty($_SESSION['admin']['id'])) {
//     header('Location: login.php');
//     exit;
// }

// Instância do cabeçalho
$cabecalho = new Cabecalho();
$cabecalho->buscar(); // carrega dados existentes

$mensagem = '';
$erro = '';

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $subtitulo = $_POST['subtitulo'] ?? '';
    $corFundo = $_POST['corFundo'] ?? '#0d6efd';
    $logo = $cabecalho->getLogo();
    $fundo = $cabecalho->getFundo();

    // Logo
    if (!empty($_FILES['logo']['name'])) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $novoNome = 'uploads/logo_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/../' . $novoNome)) {
            $logo = $novoNome;
        }
    }

    // Fundo (imagem)
    if (!empty($_FILES['fundo']['name'])) {
        $ext = pathinfo($_FILES['fundo']['name'], PATHINFO_EXTENSION);
        $novoFundo = 'uploads/fundo_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['fundo']['tmp_name'], __DIR__ . '/../' . $novoFundo)) {
            $fundo = $novoFundo;
            $corFundo = null; // imagem tem prioridade
        }
    } elseif (!empty($_POST['remover_fundo'])) {
        $fundo = null;
    }

    // Salvar no banco
    $cabecalho->setTitulo($titulo);
    $cabecalho->setSubtitulo($subtitulo);
    $cabecalho->setLogo($logo);
    $cabecalho->setFundo($fundo ?? $corFundo);

    try {
        $cabecalho->salvar();
        $mensagem = 'Cabeçalho atualizado com sucesso!';
    } catch (Exception $e) {
        $erro = 'Erro ao salvar: ' . $e->getMessage();
    }
}
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>


<div class="container my-5">
    <h1>Editar Cabeçalho</h1>

    <?php if ($mensagem): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" class="form-control" id="titulo" name="titulo"
                   value="<?= htmlspecialchars($cabecalho->getTitulo()) ?>" required>
        </div>

        <div class="mb-3">
            <label for="subtitulo" class="form-label">Subtítulo:</label>
            <input type="text" class="form-control" id="subtitulo" name="subtitulo"
                   value="<?= htmlspecialchars($cabecalho->getSubtitulo()) ?>" required>
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">Logo (opcional):</label>
            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
            <?php if ($cabecalho->getLogo()): ?>
                <img src="../<?= $cabecalho->getLogo() ?>" alt="Logo" class="img-thumbnail mt-2" style="max-height:100px;">
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="corFundo" class="form-label">Cor de fundo (opcional):</label>
            <input type="color" class="form-control form-control-color" id="corFundo" name="corFundo"
                   value="<?= htmlspecialchars($cabecalho->getFundo() && !file_exists('../'.$cabecalho->getFundo()) ? $cabecalho->getFundo() : '#0d6efd') ?>">
            <small class="text-muted">Escolha uma cor ou carregue uma imagem abaixo.</small>
        </div>

        <div class="mb-3">
            <label for="fundo" class="form-label">Imagem de fundo (opcional):</label>
            <input type="file" class="form-control" id="fundo" name="fundo" accept="image/*">
            <?php if ($cabecalho->getFundo() && file_exists('../'.$cabecalho->getFundo())): ?>
                <img src="../<?= $cabecalho->getFundo() ?>" alt="Fundo" class="img-thumbnail mt-2" style="max-height:150px;">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="remover_fundo" id="remover_fundo">
                    <label class="form-check-label" for="remover_fundo">Remover fundo</label>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>