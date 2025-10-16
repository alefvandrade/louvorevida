<?php
// topicos/exibe_topicos.php
session_start();
require_once __DIR__ . '/../classes/Topicos.class.php';
require_once __DIR__ . '/../classes/Videos.class.php';

$t = new Topicos();
$videosClass = new Videos();
$mensagem = '';
$erro = '';

// excluir tópico (apaga imagem ou video do disco se existir)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $id = (int) $_POST['id'];
    $dados = $t->buscarPorId($id);
    if ($dados) {
        if ($dados['tipo_midia'] === 'imagem' && !empty($dados['arquivo_midia'])) {
            $arquivo = __DIR__ . '/../' . $dados['arquivo_midia'];
            if (file_exists($arquivo))
                @unlink($arquivo);
        } elseif ($dados['tipo_midia'] === 'video' && !empty($dados['arquivo_midia'])) {
            $arquivo = __DIR__ . '/../' . $dados['arquivo_midia'];
            if (file_exists($arquivo))
                @unlink($arquivo);
        }
        $t->excluir($id);
        $mensagem = "<div class='alert alert-success'>Tópico excluído.</div>";
    } else {
        $erro = "<div class='alert alert-danger'>Tópico não encontrado.</div>";
    }
}

$lista = $t->listarTodos();
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Tópicos</h2>
    <?= $mensagem . $erro ?>
    <a href="cad_topicos.php" class="btn btn-success mb-3">Novo Tópico</a>

    <div class="row g-4">
        <?php foreach ($lista as $tp): ?>
            <div class="col-md-6">
                <div class="card p-3 shadow-sm">
                    <div class="row g-3 align-items-center">
                        <?php if ($tp['lado'] === 'esquerda'): ?>
                            <div class="col-md-6">
                                <?php if ($tp['tipo_midia'] === 'imagem' && $tp['arquivo_midia'] && file_exists(__DIR__ . '/..' . '/' . $tp['arquivo_midia'])): ?>
                                    <img src="../<?= $tp['arquivo_midia'] ?>" class="img-fluid rounded" alt="Capa">
                                <?php elseif ($tp['tipo_midia'] === 'video' && $tp['arquivo_midia'] && file_exists(__DIR__ . '/..' . '/' . $tp['arquivo_midia'])): ?>
                                    <?php
                                    // Tenta encontrar a capa do vídeo
                                    $videoId = basename($tp['arquivo_midia']);
                                    $videoDados = $videosClass->listarTodos();
                                    $poster = '';
                                    foreach ($videoDados as $vd) {
                                        if ($vd['video'] === $tp['arquivo_midia']) {
                                            $poster = $vd['capa_video'] ?? '';
                                            break;
                                        }
                                    }
                                    ?>
                                    <video controls poster="<?= $poster ? '../' . $poster : '' ?>" style="width:100%;"
                                        onclick="this.requestFullscreen()">
                                        <source src="../<?= $tp['arquivo_midia'] ?>" type="video/mp4">
                                        Seu navegador não suporta vídeo.
                                    </video>
                                <?php else: ?>
                                    <div class="text-muted text-center p-3">Sem mídia</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h5><?= htmlspecialchars($tp['titulo']) ?></h5>
                                <p><?= nl2br(htmlspecialchars($tp['texto'])) ?></p>
                                <?php if ($tp['botao_texto'] && $tp['botao_link']): ?>
                                    <a href="<?= htmlspecialchars($tp['botao_link']) ?>" class="btn btn-primary"
                                        target="_blank"><?= htmlspecialchars($tp['botao_texto']) ?></a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="col-md-6">
                                <h5><?= htmlspecialchars($tp['titulo']) ?></h5>
                                <p><?= nl2br(htmlspecialchars($tp['texto'])) ?></p>
                                <?php if ($tp['botao_texto'] && $tp['botao_link']): ?>
                                    <a href="<?= htmlspecialchars($tp['botao_link']) ?>" class="btn btn-primary"
                                        target="_blank"><?= htmlspecialchars($tp['botao_texto']) ?></a>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if ($tp['tipo_midia'] === 'imagem' && $tp['arquivo_midia'] && file_exists(__DIR__ . '/..' . '/' . $tp['arquivo_midia'])): ?>
                                    <img src="../<?= $tp['arquivo_midia'] ?>" class="img-fluid rounded" alt="Capa">
                                <?php elseif ($tp['tipo_midia'] === 'video' && $tp['arquivo_midia'] && file_exists(__DIR__ . '/..' . '/' . $tp['arquivo_midia'])): ?>
                                    <?php
                                    $poster = '';
                                    foreach ($videoDados as $vd) {
                                        if ($vd['video'] === $tp['arquivo_midia']) {
                                            $poster = $vd['capa_video'] ?? '';
                                            break;
                                        }
                                    }
                                    ?>
                                    <video controls poster="<?= $poster ? '../' . $poster : '' ?>" style="width:100%;"
                                        onclick="this.requestFullscreen()">
                                        <source src="../<?= $tp['arquivo_midia'] ?>" type="video/mp4">
                                        Seu navegador não suporta vídeo.
                                    </video>
                                <?php else: ?>
                                    <div class="text-muted text-center p-3">Sem mídia</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="edit_topicos.php?id=<?= $tp['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <form method="post" onsubmit="return confirm('Excluir tópico?')">
                            <input type="hidden" name="id" value="<?= $tp['id'] ?>">
                            <button name="excluir" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>