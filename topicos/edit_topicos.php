<?php
// topicos/edit.php
session_start();
require_once __DIR__ . '/../classes/Topicos.class.php';
require_once __DIR__ . '/../classes/Videos.class.php';

$mensagem = '';
$erro = '';

$dirTopicos = __DIR__ . '/../imagens/img_topicos/';
$dirVideos = __DIR__ . '/../imagens/videos/';
$webTopicos = 'imagens/img_topicos/';
$webVideos = 'imagens/videos/';

if (!is_dir($dirTopicos))
    mkdir($dirTopicos, 0755, true);
if (!is_dir($dirVideos))
    mkdir($dirVideos, 0755, true);

$t = new Topicos();
$vObj = new Videos();

$topico = null;
if (!empty($_GET['id'])) {
    $dados = $t->buscarPorId((int) $_GET['id']);
    if ($dados)
        $topico = $dados;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int) $_POST['id'];
        $t->setId($id);
        $t->setTitulo($_POST['titulo'] ?? '');
        $t->setTexto($_POST['texto'] ?? '');
        $t->setBotaoTexto($_POST['botao_texto'] ?: null);
        $t->setBotaoLink($_POST['botao_link'] ?: null);
        $t->setLado($_POST['lado'] ?? 'direita');
        $t->setAtivo(isset($_POST['ativo']) ? 1 : 0);

        $tipo = $_POST['tipo_midia'] ?? 'nenhum';

        if ($tipo === 'imagem') {
            if (!empty($_FILES['arquivo_midia']['name'])) {
                if (!empty($_POST['arquivo_midia_atual']) && strpos($_POST['arquivo_midia_atual'], 'imagens/img_topicos') === 0) {
                    @unlink(__DIR__ . '/..' . '/' . $_POST['arquivo_midia_atual']);
                }
                $ext = pathinfo($_FILES['arquivo_midia']['name'], PATHINFO_EXTENSION);
                $novo = 'topico_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['arquivo_midia']['tmp_name'], $dirTopicos . $novo)) {
                    $t->setArquivoMidia($webTopicos . $novo);
                }
            } else {
                $t->setArquivoMidia($_POST['arquivo_midia_atual'] ?: null);
            }
        } elseif ($tipo === 'video') {
            if (!empty($_POST['video_existente'])) {
                $t->setArquivoMidia($_POST['video_existente']);
            } elseif (!empty($_FILES['video_upload']['name'])) {
                $ext = pathinfo($_FILES['video_upload']['name'], PATHINFO_EXTENSION);
                $novo = 'video_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['video_upload']['tmp_name'], $dirVideos . $novo)) {
                    $nv = new Videos();
                    $nv->setTituloVideo($_POST['titulo_video_novo'] ?? 'Vídeo tópico');
                    $nv->setVideo($webVideos . $novo);
                    $nv->setAtivo(1);
                    $nv->inserir();
                    $t->setArquivoMidia($webVideos . $novo);
                }
            } else {
                $t->setArquivoMidia($_POST['arquivo_midia_atual'] ?: null);
            }
        } else {
            if (!empty($_POST['remover_midia']) && $_POST['remover_midia'] === '1') {
                if (!empty($_POST['arquivo_midia_atual']) && strpos($_POST['arquivo_midia_atual'], 'imagens/img_topicos/') === 0) {
                    @unlink(__DIR__ . '/..' . '/' . $_POST['arquivo_midia_atual']);
                }
                $t->setArquivoMidia(null);
            } else {
                $t->setArquivoMidia($_POST['arquivo_midia_atual'] ?: null);
            }
        }

        $t->setTipoMidia($tipo);
        if ($t->atualizar()) {
            $mensagem = "<div class='alert alert-success'>Tópico atualizado.</div>";
            $topico = $t->buscarPorId($id);
        } else {
            $erro = "<div class='alert alert-danger'>Erro ao atualizar ou nada alterado.</div>";
        }

    } catch (Exception $e) {
        $erro = "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

$videosLista = $vObj->listarAtivos();
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Editar Tópico</h2>
    <?= $mensagem . $erro ?>

    <?php if ($topico): ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $topico['id'] ?>">
            <input type="hidden" name="arquivo_midia_atual" value="<?= htmlspecialchars($topico['arquivo_midia']) ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control"
                            value="<?= htmlspecialchars($topico['titulo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Texto</label>
                        <textarea name="texto" class="form-control"
                            rows="6"><?= htmlspecialchars($topico['texto']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Botão texto</label>
                        <input type="text" name="botao_texto" class="form-control"
                            value="<?= htmlspecialchars($topico['botao_texto']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Botão link</label>
                        <input type="url" name="botao_link" class="form-control"
                            value="<?= htmlspecialchars($topico['botao_link']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lado</label>
                        <select name="lado" class="form-select">
                            <option value="direita" <?= $topico['lado'] == 'direita' ? 'selected' : '' ?>>Direita</option>
                            <option value="esquerda" <?= $topico['lado'] == 'esquerda' ? 'selected' : '' ?>>Esquerda</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="ativo" class="form-check-input" id="ativo"
                            <?= $topico['ativo'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ativo">Ativo</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5>Mídia</h5>
                    <div class="mb-3">
                        <label class="form-label">Tipo de mídia</label>
                        <select name="tipo_midia" id="tipo_midia" class="form-select">
                            <option value="nenhum" <?= $topico['tipo_midia'] == 'nenhum' ? 'selected' : '' ?>>Nenhum</option>
                            <option value="imagem" <?= $topico['tipo_midia'] == 'imagem' ? 'selected' : '' ?>>Imagem</option>
                            <option value="video" <?= $topico['tipo_midia'] == 'video' ? 'selected' : '' ?>>Vídeo</option>
                        </select>
                    </div>

                    <div id="media_imagem" style="display:none;">
                        <label class="form-label">Enviar imagem</label>
                        <input type="file" name="arquivo_midia" accept="image/*" class="form-control">
                        <?php if (!empty($topico['arquivo_midia']) && $topico['tipo_midia'] == 'imagem'): ?>
                            <img src="<?= htmlspecialchars($topico['arquivo_midia']) ?>"
                                style="max-width:100%; margin-top:10px;">
                        <?php endif; ?>
                        <div id="preview_edit_topico_img"></div>
                    </div>

                    <div id="media_video" style="display:none;">
                        <label class="form-label">Selecionar vídeo existente</label>
                        <select name="video_existente" class="form-select mb-2">
                            <option value="">-- escolher --</option>
                            <?php foreach ($videosLista as $v): ?>
                                <option value="<?= htmlspecialchars($v['video']) ?>"
                                    <?= $topico['arquivo_midia'] == $v['video'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($v['titulo_video']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <div class="mb-2">ou envie novo vídeo</div>
                        <input type="file" name="video_upload" accept="video/*" class="form-control">
                        <input type="text" name="titulo_video_novo" class="form-control mt-2"
                            placeholder="Título para novo vídeo">
                        <?php if (!empty($topico['arquivo_midia']) && $topico['tipo_midia'] == 'video'): ?>
                            <video src="<?= htmlspecialchars($topico['arquivo_midia']) ?>" controls
                                style="max-width:100%; margin-top:10px;"></video>
                        <?php endif; ?>
                        <div id="preview_edit_topico_vid"></div>
                    </div>

                    <div class="form-check mt-2">
                        <input type="checkbox" name="remover_midia" value="1" class="form-check-input" id="remover_midia">
                        <label class="form-check-label" for="remover_midia">Remover mídia atual</label>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary mt-3">Atualizar Tópico</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">Tópico não encontrado.</div>
    <?php endif; ?>
</div>

<script>
    const tipo = document.getElementById('tipo_midia');
    const imgDiv = document.getElementById('media_imagem');
    const vidDiv = document.getElementById('media_video');
    function toggleMedia() {
        if (tipo.value === 'imagem') { imgDiv.style.display = 'block'; vidDiv.style.display = 'none'; }
        else if (tipo.value === 'video') { imgDiv.style.display = 'none'; vidDiv.style.display = 'block'; }
        else { imgDiv.style.display = 'none'; vidDiv.style.display = 'none'; }
    }
    tipo.addEventListener('change', toggleMedia);
    toggleMedia();

    document.addEventListener('DOMContentLoaded', function () {
        function previewFile(input, previewId) {
            const container = document.getElementById(previewId);
            container.innerHTML = '';
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const ext = file.name.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    const img = document.createElement('img');
                    img.style.maxWidth = '100%';
                    img.src = URL.createObjectURL(file);
                    container.appendChild(img);
                } else if (['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'].includes(ext)) {
                    const video = document.createElement('video');
                    video.style.maxWidth = '100%';
                    video.controls = true;
                    video.src = URL.createObjectURL(file);
                    container.appendChild(video);
                }
            }
        }
        const inputImg = document.querySelector('input[name="arquivo_midia"]');
        if (inputImg) inputImg.addEventListener('change', function () { previewFile(this, 'preview_edit_topico_img'); });
        const inputVid = document.querySelector('input[name="video_upload"]');
        if (inputVid) inputVid.addEventListener('change', function () { previewFile(this, 'preview_edit_topico_vid'); });
    });
</script>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>