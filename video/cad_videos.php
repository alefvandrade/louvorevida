<?php
// videos/cad.php
session_start();
require_once __DIR__ . '/../classes/Videos.class.php';
require_once __DIR__ . '/../classes/Topicos.class.php';

$mensagem = '';
$erro = '';

$dirVideos = __DIR__ . '/../imagens/videos/';
$webVideos = 'imagens/videos/';

if (!is_dir($dirVideos)) mkdir($dirVideos, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $v = new Videos();
        $v->setTituloVideo($_POST['titulo_video'] ?? '');
        $v->setDataGravacao($_POST['data_gravacao'] ?: null);
        $v->setExibirNoIndex(isset($_POST['exibir_no_index']) ? 1 : 0);
        $v->setOrientacao($_POST['orientacao'] ?? 'auto');
        $v->setAtivo(isset($_POST['ativo']) ? 1 : 0);

        // Capa (opcional)
        if (!empty($_FILES['capa_video']['name'])) {
            $ext = pathinfo($_FILES['capa_video']['name'], PATHINFO_EXTENSION);
            $novo = 'capa_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['capa_video']['tmp_name'], $dirVideos . $novo)) {
                $v->setCapaVideo($webVideos . $novo);
            }
        }

        // Arquivo de vídeo (aceita mp4,webm,ogg,mkv,... conforme solicitado)
        if (!empty($_FILES['video']['name'])) {
            $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
            $novo = 'video_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['video']['tmp_name'], $dirVideos . $novo)) {
                $v->setVideo($webVideos . $novo);
            } else {
                throw new Exception('Falha ao mover arquivo de vídeo.');
            }
        } else {
            throw new Exception('Envie um arquivo de vídeo.');
        }

        $v->inserir();
        $mensagem = "<div class='alert alert-success'>Vídeo cadastrado com sucesso.</div>";
    } catch (Exception $e) {
        $erro = "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Cadastrar Vídeo</h2>
    <?= $mensagem . $erro ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título do Vídeo</label>
            <input type="text" name="titulo_video" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Gravação</label>
            <input type="date" name="data_gravacao" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Capa (opcional)</label>
            <input type="file" name="capa_video" accept="image/*" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Arquivo de Vídeo (mp4, webm, ogg, avi, mkv...)</label>
            <input type="file" name="video" accept="video/*" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Orientação</label>
            <select name="orientacao" class="form-select">
                <option value="auto">Auto</option>
                <option value="horizontal">Horizontal</option>
                <option value="vertical">Vertical</option>
            </select>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="exibir_no_index" class="form-check-input" id="exibir_no_index">
            <label class="form-check-label" for="exibir_no_index">Exibir no index</label>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="ativo" class="form-check-input" id="ativo" checked>
            <label class="form-check-label" for="ativo">Ativo</label>
        </div>

        <button class="btn btn-success">Cadastrar Vídeo</button>
    </form>
</div>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>
