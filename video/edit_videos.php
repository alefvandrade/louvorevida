<?php
// videos/edit.php
session_start();
require_once __DIR__ . '/../classes/Videos.class.php';
require_once __DIR__ . '/../classes/Topicos.class.php';

$mensagem = '';
$erro = '';

$dirVideos = __DIR__ . '/../imagens/videos/';
$webVideos = 'imagens/videos/';

if (!is_dir($dirVideos)) mkdir($dirVideos, 0755, true);

$v = new Videos();
$videoDados = null;

if (!empty($_GET['id'])) {
    $videoDados = $v->buscarPorId((int)$_GET['id']);
    if ($videoDados) {
        $v->setId($videoDados['id']);
        $v->setTituloVideo($videoDados['titulo_video']);
        $v->setDataGravacao($videoDados['data_gravacao']);
        $v->setCapaVideo($videoDados['capa_video']);
        $v->setVideo($videoDados['video']);
        $v->setExibirNoIndex($videoDados['exibir_no_index']);
        $v->setOrientacao($videoDados['orientacao']);
        $v->setAtivo($videoDados['ativo']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $v->setId((int)$_POST['id']);
        $v->setTituloVideo($_POST['titulo_video'] ?? '');
        $v->setDataGravacao($_POST['data_gravacao'] ?: null);
        $v->setExibirNoIndex(isset($_POST['exibir_no_index']) ? 1 : 0);
        $v->setOrientacao($_POST['orientacao'] ?? 'auto');
        $v->setAtivo(isset($_POST['ativo']) ? 1 : 0);

        // Capa
        if (!empty($_FILES['capa_video']['name'])) {
            // remove antiga
            if ($v->getCapaVideo() && file_exists(__DIR__ . '/..' . '/' . $v->getCapaVideo())) {
                @unlink(__DIR__ . '/..' . '/' . $v->getCapaVideo());
            }
            $ext = pathinfo($_FILES['capa_video']['name'], PATHINFO_EXTENSION);
            $novo = 'capa_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['capa_video']['tmp_name'], $dirVideos . $novo)) {
                $v->setCapaVideo($webVideos . $novo);
            }
        }

        // Vídeo
        if (!empty($_FILES['video']['name'])) {
            // remove antigo
            if ($v->getVideo() && file_exists(__DIR__ . '/..' . '/' . $v->getVideo())) {
                @unlink(__DIR__ . '/..' . '/' . $v->getVideo());
            }
            $ext = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
            $novo = 'video_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['video']['tmp_name'], $dirVideos . $novo)) {
                $v->setVideo($webVideos . $novo);
            } else {
                throw new Exception('Falha ao subir o novo vídeo.');
            }
        }

        if ($v->atualizar()) {
            $mensagem = "<div class='alert alert-success'>Vídeo atualizado com sucesso.</div>";
        } else {
            $erro = "<div class='alert alert-danger'>Nada alterado ou erro ao atualizar.</div>";
        }
    } catch (Exception $e) {
        $erro = "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Para checar uso em tópicos
$topicos = new Topicos();

?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Editar Vídeo</h2>
    <?= $mensagem . $erro ?>

    <?php if ($videoDados): ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $videoDados['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo_video" class="form-control" value="<?= htmlspecialchars($videoDados['titulo_video']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Data de Gravação</label>
                <input type="date" name="data_gravacao" class="form-control" value="<?= htmlspecialchars($videoDados['data_gravacao']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Capa atual</label><br>
                <?php if ($videoDados['capa_video'] && file_exists(__DIR__ . '/..' . '/' . $videoDados['capa_video'])): ?>
                    <img src="../<?= $videoDados['capa_video'] ?>" style="max-width:200px" alt="capa">
                <?php else: ?>
                    <div class="text-muted">Nenhuma capa</div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Substituir capa (opcional)</label>
                <input type="file" name="capa_video" accept="image/*" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Vídeo atual</label><br>
                <?php if ($videoDados['video'] && file_exists(__DIR__ . '/..' . '/' . $videoDados['video'])): ?>
                    <video controls style="max-width:100%" src="../<?= $videoDados['video'] ?>"></video>
                <?php else: ?>
                    <div class="text-muted">Arquivo de vídeo não encontrado.</div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Substituir vídeo</label>
                <input type="file" name="video" accept="video/*" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Orientação</label>
                <select name="orientacao" class="form-select">
                    <option value="auto" <?= $videoDados['orientacao']=='auto'? 'selected':'' ?>>Auto</option>
                    <option value="horizontal" <?= $videoDados['orientacao']=='horizontal'? 'selected':'' ?>>Horizontal</option>
                    <option value="vertical" <?= $videoDados['orientacao']=='vertical'? 'selected':'' ?>>Vertical</option>
                </select>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="exibir_no_index" class="form-check-input" id="exibir_no_index" <?= $videoDados['exibir_no_index'] ? 'checked':'' ?>>
                <label class="form-check-label" for="exibir_no_index">Exibir no index</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="ativo" class="form-check-input" id="ativo" <?= $videoDados['ativo'] ? 'checked':'' ?>>
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>

            <button class="btn btn-primary">Salvar Alterações</button>
        </form>

        <hr>

        <h5>Uso do vídeo em tópicos</h5>
        <?php
            $usos = $topicos->read("tipo_midia = 'video' AND arquivo_midia = ?", [$videoDados['video']]);
            if ($usos) {
                echo "<ul>";
                foreach ($usos as $u) {
                    echo "<li>" . htmlspecialchars($u['titulo']) . " (ID: {$u['id']})</li>";
                }
                echo "</ul>";
            } else {
                echo "<div class='text-muted'>Este vídeo não está sendo utilizado por nenhum tópico.</div>";
            }
        ?>

    <?php else: ?>
        <div class="alert alert-warning">Vídeo não encontrado.</div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>
