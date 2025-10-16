<?php
// topicos/cad.php
session_start();
require_once __DIR__ . '/../classes/Topicos.class.php';
require_once __DIR__ . '/../classes/Videos.class.php';

$mensagem = '';
$erro = '';

$dirTopicos = __DIR__ . '/../imagens/img_topicos/';
$dirVideos = __DIR__ . '/../imagens/videos/';
$webTopicos = 'imagens/img_topicos/';
$webVideos = 'imagens/videos/';

if (!is_dir($dirTopicos)) mkdir($dirTopicos, 0755, true);
if (!is_dir($dirVideos)) mkdir($dirVideos, 0755, true);

$t = new Topicos();
$videosObj = new Videos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $t->setTitulo($_POST['titulo'] ?? '');
        $t->setTexto($_POST['texto'] ?? '');
        $t->setBotaoTexto($_POST['botao_texto'] ?: null);
        $t->setBotaoLink($_POST['botao_link'] ?: null);
        $t->setLado($_POST['lado'] ?? 'direita');
        $t->setAtivo(isset($_POST['ativo']) ? 1 : 0);

        // Se escolheu tipo imagem
        if ($_POST['tipo_midia'] === 'imagem') {
            if (!empty($_FILES['arquivo_midia']['name'])) {
                $ext = pathinfo($_FILES['arquivo_midia']['name'], PATHINFO_EXTENSION);
                $novo = 'topico_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['arquivo_midia']['tmp_name'], $dirTopicos . $novo)) {
                    $t->setArquivoMidia($webTopicos . $novo);
                }
            }
        } elseif ($_POST['tipo_midia'] === 'video') {
            // selecionar vídeo já cadastrado (select) ou enviar novo
            if (!empty($_POST['video_existente'])) {
                $t->setArquivoMidia($_POST['video_existente']);
            } elseif (!empty($_FILES['video_upload']['name'])) {
                // enviar novo vídeo para pasta imagens/videos
                $ext = pathinfo($_FILES['video_upload']['name'], PATHINFO_EXTENSION);
                $novo = 'video_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['video_upload']['tmp_name'], $dirVideos . $novo)) {
                    // cadastrar registro em videos
                    $v = new Videos();
                    $v->setTituloVideo($_POST['titulo_video_novo'] ?? 'Vídeo tópico');
                    $v->setVideo($webVideos . $novo);
                    $v->setAtivo(1);
                    $v->inserir();
                    $t->setArquivoMidia($webVideos . $novo);
                }
            }
        } else {
            $t->setArquivoMidia(null);
        }

        $t->setTipoMidia($_POST['tipo_midia'] ?? 'nenhum');

        $t->inserir();
        $mensagem = "<div class='alert alert-success'>Tópico cadastrado com sucesso.</div>";
    } catch (Exception $e) {
        $erro = "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// carregar lista de vídeos para seleção
$videosLista = (new Videos())->listarAtivos();
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Cadastrar Tópico</h2>
    <?= $mensagem . $erro ?>

    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Texto</label>
                    <textarea name="texto" class="form-control" rows="6"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Botão (texto)</label>
                    <input type="text" name="botao_texto" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Link do botão</label>
                    <input type="url" name="botao_link" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Lado da mídia</label>
                    <select name="lado" class="form-select">
                        <option value="direita">Direita</option>
                        <option value="esquerda">Esquerda</option>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="ativo" class="form-check-input" id="ativo" checked>
                    <label class="form-check-label" for="ativo">Ativo</label>
                </div>
            </div>

            <div class="col-md-6">
                <h5>Mídia (lado direito)</h5>

                <div class="mb-3">
                    <label class="form-label">Tipo de mídia</label>
                    <select name="tipo_midia" id="tipo_midia" class="form-select">
                        <option value="nenhum">Nenhum</option>
                        <option value="imagem">Imagem</option>
                        <option value="video">Vídeo</option>
                    </select>
                </div>

                <div id="media_imagem" style="display:none;">
                    <label class="form-label">Enviar imagem</label>
                    <input type="file" name="arquivo_midia" accept="image/*" class="form-control">
                </div>

                <div id="media_video" style="display:none;">
                    <label class="form-label">Selecionar vídeo existente</label>
                    <select name="video_existente" class="form-select mb-2">
                        <option value="">-- escolher --</option>
                        <?php foreach ($videosLista as $vv): ?>
                            <option value="<?= htmlspecialchars($vv['video']) ?>"><?= htmlspecialchars($vv['titulo_video']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <div class="mb-2">ou envie um novo vídeo (será cadastrado automaticamente):</div>
                    <div class="mb-3">
                        <label class="form-label">Arquivo de vídeo</label>
                        <input type="file" name="video_upload" accept="video/*" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Título para novo vídeo (opcional)</label>
                        <input type="text" name="titulo_video_novo" class="form-control">
                    </div>
                </div>

            </div>
        </div>

        <button class="btn btn-success mt-3">Cadastrar Tópico</button>
    </form>
</div>

<script>
    const tipo = document.getElementById('tipo_midia');
    const imgDiv = document.getElementById('media_imagem');
    const vidDiv = document.getElementById('media_video');

    function toggleMedia() {
        if (tipo.value === 'imagem') {
            imgDiv.style.display = 'block';
            vidDiv.style.display = 'none';
        } else if (tipo.value === 'video') {
            imgDiv.style.display = 'none';
            vidDiv.style.display = 'block';
        } else {
            imgDiv.style.display = 'none';
            vidDiv.style.display = 'none';
        }
    }
    tipo.addEventListener('change', toggleMedia);
    toggleMedia();
</script>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>
