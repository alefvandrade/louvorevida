<?php
session_start();
require_once __DIR__ . '/../classes/Carrossel.class.php';

$mensagem = '';
$erro = '';

$dirCarrossel = __DIR__ . '/../imagens/img_carrossel/';
$webCarrossel = 'imagens/img_carrossel/';

$c = new Carrossel();
$carrossel = null;

if (!empty($_GET['id'])) {
    $carrossel = $c->buscarPorId((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $carrossel) {
    try {
        $c->id = (int)$_POST['id'];
        $c->titulo = $_POST['titulo'] ?? '';
        $c->subtitulo = $_POST['subtitulo'] ?? '';
        $c->botao_texto = $_POST['botao_texto'] ?: null;
        $c->botao_link = $_POST['botao_link'] ?: null;
        $c->mostrar_botao = isset($_POST['mostrar_botao']) ? 1 : 0;
        $c->ativo = isset($_POST['ativo']) ? 1 : 0;

        // substituir imagem
        if (!empty($_FILES['fundo']['name'])) {
            if (!empty($_POST['fundo_atual']) && file_exists(__DIR__ . '/..' . '/' . $_POST['fundo_atual'])) {
                @unlink(__DIR__ . '/..' . '/' . $_POST['fundo_atual']);
            }
            $ext = pathinfo($_FILES['fundo']['name'], PATHINFO_EXTENSION);
            $novo = 'carrossel_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['fundo']['tmp_name'], $dirCarrossel . $novo)) {
                $c->fundo = $webCarrossel . $novo;
            }
        } else {
            $c->fundo = $_POST['fundo_atual'] ?: null;
        }

        if ($c->atualizar()) {
            $mensagem = "<div class='alert alert-success'>Carrossel atualizado.</div>";
            $carrossel = $c->buscarPorId($c->id);
        } else {
            $erro = "<div class='alert alert-danger'>Nada alterado ou erro.</div>";
        }
    } catch (Exception $e) {
        $erro = "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Editar Carrossel</h2>
    <?= $mensagem . $erro ?>

    <?php if ($carrossel): ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $carrossel['id'] ?>">
            <input type="hidden" name="fundo_atual" value="<?= htmlspecialchars($carrossel['fundo']) ?>">

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($carrossel['titulo']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Subtítulo</label>
                <input type="text" name="subtitulo" class="form-control" value="<?= htmlspecialchars($carrossel['subtitulo']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Imagem de fundo</label>
                <?php if ($carrossel['fundo'] && file_exists(__DIR__.'/..'.'/'.$carrossel['fundo'])): ?>
                    <img src="../<?= $carrossel['fundo'] ?>" id="fundoPreview" style="max-width:100%;" alt="Pré-visualização">
                <?php else: ?>
                    <img id="fundoPreview" style="max-width:100%; display:none;" alt="Pré-visualização">
                <?php endif; ?>
                <input type="file" name="fundo" accept="image/*" class="form-control mt-2" id="fundoInput">
            </div>

            <div class="mb-3">
                <label class="form-label">Botão (opcional)</label>
                <input type="text" name="botao_texto" class="form-control" value="<?= htmlspecialchars($carrossel['botao_texto']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Link do botão</label>
                <input type="url" name="botao_link" class="form-control" value="<?= htmlspecialchars($carrossel['botao_link']) ?>">
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="mostrar_botao" class="form-check-input" id="mostrar_botao" <?= $carrossel['mostrar_botao'] ? 'checked':'' ?>>
                <label class="form-check-label" for="mostrar_botao">Mostrar botão</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="ativo" class="form-check-input" id="ativo" <?= $carrossel['ativo'] ? 'checked':'' ?>>
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>

            <button class="btn btn-primary">Salvar Alterações</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">Carrossel não encontrado.</div>
    <?php endif; ?>
</div>

<script>
    document.getElementById('fundoInput').addEventListener('change', function(e){
        const [file] = e.target.files;
        if (file) {
            const preview = document.getElementById('fundoPreview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
</script>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>
