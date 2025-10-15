<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../classes/Cabecalho.class.php';
require_once __DIR__ . '/../classes/Admin.class.php';

$cabecalho = new Cabecalho();
$cabecalho->buscar();

$mensagem = '';
$erro = '';

$dirImagens = __DIR__ . '/../imagens/img_cabecalho/';
$dirWeb = 'imagens/img_cabecalho/';

if (!is_dir($dirImagens)) {
    mkdir($dirImagens, 0755, true);
}

// REMOVER IMAGEM EXISTENTE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['remover_imagem'])) {
    $tipo = $_POST['remover_imagem']; // "logo" ou "fundo"
    $arquivo = $tipo === 'logo' ? $cabecalho->getLogo() : $cabecalho->getFundo();
    if ($arquivo && file_exists(__DIR__ . '/../' . $arquivo)) {
        unlink(__DIR__ . '/../' . $arquivo);
    }
    if ($tipo === 'logo')
        $cabecalho->setLogo(null);
    if ($tipo === 'fundo')
        $cabecalho->setFundo(null);
    $cabecalho->salvar();
    $mensagem = ucfirst($tipo) . ' removida com sucesso!';
}

// PROCESSAR FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['remover_imagem'])) {
    $titulo = $_POST['titulo'] ?? '';
    $subtitulo = $_POST['subtitulo'] ?? '';
    $corFundo = $_POST['corFundo'] ?? '#0d6efd';
    $logo = $cabecalho->getLogo();
    $fundo = $cabecalho->getFundo();

    // Logo
    if (!empty($_FILES['logo']['name'])) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $novoNome = 'logo_' . time() . '.' . $ext;
        $destino = $dirImagens . $novoNome;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $destino)) {
            if ($logo && file_exists(__DIR__ . '/../' . $logo))
                unlink(__DIR__ . '/../' . $logo);
            $logo = $dirWeb . $novoNome;
        }
    }

    // Fundo
    if (!empty($_FILES['fundo']['name'])) {
        $ext = pathinfo($_FILES['fundo']['name'], PATHINFO_EXTENSION);
        $novoFundo = 'fundo_' . time() . '.' . $ext;
        $destinoFundo = $dirImagens . $novoFundo;
        if (move_uploaded_file($_FILES['fundo']['tmp_name'], $destinoFundo)) {
            if ($fundo && file_exists(__DIR__ . '/../' . $fundo))
                unlink(__DIR__ . '/../' . $fundo);
            $fundo = $dirWeb . $novoFundo;
        }
    } elseif (!empty($_POST['remover_fundo'])) {
        if ($fundo && file_exists(__DIR__ . '/../' . $fundo))
            unlink(__DIR__ . '/../' . $fundo);
        $fundo = null;
    }

    // Atualizar objeto
    $cabecalho->setTitulo($titulo);
    $cabecalho->setSubtitulo($subtitulo);
    $cabecalho->setLogo($logo);
    $cabecalho->setFundo($fundo);       // imagem ou null
    $cabecalho->setCorFundo($corFundo); // cor sempre salva

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
        <!-- Título -->
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" class="form-control" id="titulo" name="titulo"
                value="<?= htmlspecialchars($cabecalho->getTitulo()) ?>" required>
        </div>

        <!-- Subtítulo -->
        <div class="mb-3">
            <label for="subtitulo" class="form-label">Subtítulo:</label>
            <input type="text" class="form-control" id="subtitulo" name="subtitulo"
                value="<?= htmlspecialchars($cabecalho->getSubtitulo()) ?>" required>
        </div>

        <!-- Logo -->
        <div class="mb-3">
            <label for="logo" class="form-label">Logo (opcional):</label>
            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
            <div class="mt-2">
                <img id="previewLogo" src="<?= $cabecalho->getLogo() ? '../' . $cabecalho->getLogo() : '' ?>"
                    class="img-thumbnail" style="max-height:100px; <?= $cabecalho->getLogo() ? '' : 'display:none;' ?>">
            </div>
            <?php if ($cabecalho->getLogo()): ?>
                <button type="submit" name="remover_imagem" value="logo" class="btn btn-danger btn-sm mt-2">Remover
                    Logo</button>
            <?php endif; ?>
        </div>

        <!-- Cor de Fundo -->
        <div class="mb-3">
            <label for="corFundo" class="form-label">Cor de fundo (sempre salva):</label>
            <input type="color" class="form-control form-control-color" id="corFundo" name="corFundo"
                value="<?= htmlspecialchars($cabecalho->getCorFundo() ?: '#0d6efd') ?>">
        </div>

        <!-- Fundo -->
        <div class="mb-3">
            <label for="fundo" class="form-label">Imagem de fundo (opcional):</label>
            <input type="file" class="form-control" id="fundo" name="fundo" accept="image/*">
            <div class="mt-2">
                <img id="previewFundo"
                    src="<?= $cabecalho->getFundo() && file_exists('../' . $cabecalho->getFundo()) ? '../' . $cabecalho->getFundo() : '' ?>"
                    class="img-thumbnail"
                    style="max-height:150px; <?= $cabecalho->getFundo() ? '' : 'display:none;' ?>">
            </div>
            <?php if ($cabecalho->getFundo() && file_exists('../' . $cabecalho->getFundo())): ?>
                <button type="submit" name="remover_imagem" value="fundo" class="btn btn-danger btn-sm mt-2">Remover
                    Fundo</button>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>

<script>
    document.getElementById('logo').addEventListener('change', function (e) {
        const preview = document.getElementById('previewLogo');
        const file = e.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'inline-block';
        } else {
            preview.style.display = 'none';
        }
    });

    document.getElementById('fundo').addEventListener('change', function (e) {
        const preview = document.getElementById('previewFundo');
        const file = e.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'inline-block';
        } else {
            preview.style.display = 'none';
        }
    });
</script>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>