<?php
require_once __DIR__ . '/../classes/Videos.class.php';
require_once __DIR__ . '/../classes/Topicos.class.php';

$videosClass = new Videos();
$topicosClass = new Topicos();

// ==========================
// EXCLUSÃO DIRETA
// ==========================
if (isset($_GET['excluir_id'])) {
    $idExcluir = (int) $_GET['excluir_id'];
    $videoDados = $videosClass->buscarPorId($idExcluir);

    if ($videoDados) {
        // Caminhos corretos
        $videoPath = $videoDados['video'] ? __DIR__ . '/../imagens/videos/' . basename($videoDados['video']) : null;
        $capaPath = $videoDados['capa_video'] ? __DIR__ . '/../imagens/videos/' . basename($videoDados['capa_video']) : null;

        // Excluir do banco
        if ($videosClass->excluir($idExcluir)) {
            // Excluir arquivos físicos
            if ($videoPath && file_exists($videoPath))
                unlink($videoPath);
            if ($capaPath && file_exists($capaPath))
                unlink($capaPath);
        }
    }

    header('Location: exibe_videos.php');
    exit;
}

$videos = $videosClass->listarTodos();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Vídeos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .video-card {
            transition: transform 0.2s ease;
        }

        .video-card:hover {
            transform: scale(1.02);
        }

        video {
            width: 100%;
            height: auto;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .video-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<?php require_once __DIR__ . '/../dashboard/_header.php'; ?>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">📹 Gerenciar Vídeos</h2>
            <a href="cad_videos.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Adicionar Vídeo</a>
        </div>

        <?php if (empty($videos)): ?>
            <div class="alert alert-warning text-center">Nenhum vídeo cadastrado.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($videos as $video): ?>
                    <?php
                    $titulo = htmlspecialchars($video['titulo_video'] ?? '', ENT_QUOTES, 'UTF-8');
                    $arquivo = $video['video'] ?? '';
                    $capa = $video['capa_video'] ?? '';
                    $data = htmlspecialchars($video['data_gravacao'] ?? '', ENT_QUOTES, 'UTF-8');
                    $orientacao = htmlspecialchars($video['orientacao'] ?? '', ENT_QUOTES, 'UTF-8');
                    $ativo = (int) ($video['ativo'] ?? 0);

                    $videoSrc = $arquivo ? "../imagens/videos/" . basename($arquivo) : null;
                    $posterSrc = $capa ? "../imagens/videos/" . basename($capa) : "../imagens/videos/default_video.jpg";

                    // Verificar se está em algum tópico
                    $topicoUsando = null;
                    $topicos = $topicosClass->listarTodos();
                    foreach ($topicos as $topico) {
                        if (($topico['tipo_midia'] ?? '') === 'video' && ($topico['arquivo_midia'] ?? '') === $arquivo) {
                            $topicoUsando = $topico;
                            break;
                        }
                    }
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card video-card shadow-sm h-100">
                            <?php if ($videoSrc): ?>
                                <video controls poster="<?= htmlspecialchars($posterSrc, ENT_QUOTES, 'UTF-8') ?>"
                                    onclick="this.requestFullscreen()">
                                    <source src="<?= htmlspecialchars($videoSrc, ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
                                    Seu navegador não suporta vídeos.
                                </video>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">Sem vídeo</div>
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title"><?= $titulo ?></h5>
                                <p class="card-text mb-1"><strong>Data:</strong> <?= $data ?: '—' ?></p>
                                <p class="card-text mb-1"><strong>Orientação:</strong> <?= $orientacao ?></p>
                                <p class="card-text mb-2">
                                    <strong>Status:</strong>
                                    <?= $ativo ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>' ?>
                                </p>

                                <?php if ($topicoUsando): ?>
                                    <p class="text-primary mb-2">
                                        <i class="bi bi-link"></i> Usado no tópico:
                                        <strong><?= htmlspecialchars($topicoUsando['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                    </p>
                                <?php else: ?>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-x-circle"></i> Não usado em nenhum tópico
                                    </p>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between">
                                    <a href="edit_videos.php?id=<?= (int) $video['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <a href="?excluir_id=<?= (int) $video['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir este vídeo?')">
                                        <i class="bi bi-trash"></i> Excluir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php require_once __DIR__ . '/../dashboard/_footer.php'; ?>

</html>