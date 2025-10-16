<?php
session_start();
require_once __DIR__ . '/../classes/Carrossel.class.php';

$mensagem = '';
$erro = '';

$dirCarrossel = __DIR__ . '/../imagens/img_carrossel/';
$webCarrossel = 'imagens/img_carrossel/';

if (!is_dir($dirCarrossel)) mkdir($dirCarrossel, 0755, true);

$c = new Carrossel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $c->titulo = $_POST['titulo'] ?? '';
        $c->subtitulo = $_POST['subtitulo'] ?? '';
        $c->botao_texto = $_POST['botao_texto'] ?: null;
        $c->botao_link = $_POST['botao_link'] ?: null;
        $c->mostrar_botao = isset($_POST['mostrar_botao']) ? 1 : 0;
        $c->ativo = isset($_POST['ativo']) ? 1 : 0;

        // imagem de fundo
        if (!empty($_FILES['fundo']['name'])) {
            $ext = pathinfo($_FILES['fundo']['name'], PATHINFO_EXTENSION);
            $novo = 'carrossel_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['fundo']['tmp_name'], $dirCarrossel . $novo)) {
                $c->fundo = $webCarrossel . $novo;
            }
        }

        $c->inserir();
        $mensagem = "<div class='alert alert-success'>Carrossel cadastrado com sucesso.</div>";
    } catch (Exception $e) {
        $erro = "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Cadastrar Carrossel</h2>
    <?= $mensagem . $erro ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Subtítulo</label>
            <input type="text" name="subtitulo" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Imagem de fundo</label>
            <input type="file" name="fundo" accept="image/*" class="form-control" id="fundoInput">
            <div class="mt-2">
                <img id="fundoPreview" src="#" style="max-width:100%; display:none;" alt="Pré-visualização">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Botão (opcional)</label>
            <input type="text" name="botao_texto" class="form-control" placeholder="Texto do botão">
        </div>

        <div class="mb-3">
            <label class="form-label">Link do botão</label>
            <input type="url" name="botao_link" class="form-control" placeholder="https://">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="mostrar_botao" class="form-check-input" id="mostrar_botao" checked>
            <label class="form-check-label" for="mostrar_botao">Mostrar botão</label>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="ativo" class="form-check-input" id="ativo" checked>
            <label class="form-check-label" for="ativo">Ativo</label>
        </div>

        <button class="btn btn-success">Cadastrar Carrossel</button>
    </form>
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
