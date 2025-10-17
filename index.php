<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . "/classes/Carrossel.class.php";
require_once __DIR__ . "/classes/Rodape.class.php";
require_once __DIR__ . "/classes/Cabecalho.class.php";
require_once __DIR__ . "/classes/Topicos.class.php";
require_once __DIR__ . "/classes/Integrantes.class.php";
require_once __DIR__ . "/classes/Agenda.class.php";

session_start();

/* ==============================
   OBJETOS E DADOS
============================== */
$cabecalhoObj = new Cabecalho();
$cabecalhoObj->buscar();
$tituloSite = $cabecalhoObj->getTitulo() ?: 'Louvor & Vida';
$subtituloSite = $cabecalhoObj->getSubtitulo() ?: '';
$logoSite = $cabecalhoObj->getLogo();
$fundoSite = $cabecalhoObj->getFundo();
$corFundo = $cabecalhoObj->getCorFundo() ?: '#00B4D8';

$carrossel = (new Carrossel())->listarTodos() ?: [];
$topicos = (new Topicos())->listarAtivos();
$integrantes = (new Integrante())->listar(true);
$rodape = (new Rodape())->listarTodos();
$eventos = (new Agenda())->listarAtivos();

/* ==============================
   FUNÇÃO UTILITÁRIA
============================== */
function getImgSrc($tipo, $valor, $baseImagePath)
{
    $valor = trim((string) $valor);
    if ($valor === '')
        return $baseImagePath['default'];

    if (preg_match('#^(https?://|/)#i', $valor))
        return $valor;

    $arquivo = basename($valor);
    $ext = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
    $imgs = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    $videos = ['mp4', 'webm', 'ogg', 'mov', 'mkv'];

    if (in_array($ext, $imgs))
        return rtrim($baseImagePath[$tipo], '/') . '/' . $arquivo;

    if (in_array($ext, $videos))
        return rtrim($baseImagePath['videos'], '/') . '/' . $arquivo;

    return $baseImagePath['default'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($tituloSite) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars(getImgSrc('cabecalho', $logoSite, $baseImagePath)) ?>">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="CSS/style_index.css">
</head>

<body class="fade-in d-flex flex-column min-vh-100">

    <!-- CABEÇALHO -->
    <?php
    $bgStyle = $fundoSite
        ? "background-image: url('" . htmlspecialchars(getImgSrc('cabecalho', $fundoSite, $baseImagePath)) . "');"
        : "background-color: {$corFundo};";
    ?>
    <header class="site-header text-center <?= $fundoSite ? 'header-overlay' : '' ?>" style="<?= $bgStyle ?>">
        <?php if ($logoSite): ?>
            <img src="<?= htmlspecialchars(getImgSrc('cabecalho', $logoSite, $baseImagePath)) ?>" alt="Logo"
                class="logo mb-3">
        <?php endif; ?>
        <div class="container">
            <h1><?= htmlspecialchars($tituloSite) ?></h1>
            <p class="lead mb-0"><?= htmlspecialchars($subtituloSite) ?></p>
        </div>
    </header>

    <!-- NAVBAR -->
    <nav id="mainNav" class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <?php if ($logoSite): ?>
                    <img src="<?= htmlspecialchars(getImgSrc('cabecalho', $logoSite, $baseImagePath)) ?>"
                        class="logo-navbar" alt="Logo">
                <?php endif; ?>
                <span><?= htmlspecialchars($tituloSite) ?></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php foreach ($topicos as $t): ?>
                        <li class="nav-item"><a class="nav-link"
                                href="#topico-<?= (int) $t['id'] ?>"><?= htmlspecialchars($t['titulo']) ?></a></li>
                    <?php endforeach; ?>
                    <li class="nav-item"><a class="nav-link" href="#eventos">Eventos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#integrantes">Integrantes</a></li>
                </ul>

                <a href="login/login.php" class="btn btn-outline-success fw-bold ms-auto"><i
                        class="bi bi-box-arrow-in-right"></i> Entrar</a>
            </div>
        </div>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-fill">

        <!-- CARROSSEL -->
        <section class="container my-4">
            <?php if ($carrossel): ?>
                <div id="carouselExample" class="carousel slide custom-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($carrossel as $i => $s): ?>
                            <?php
                            $src = getImgSrc('carrossel', $s['imagem'] ?? $s['arquivo'] ?? '', $baseImagePath);
                            if (!file_exists($src))
                                $src = $baseImagePath['default'];
                            ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                <img src="<?= htmlspecialchars($src) ?>" class="d-block w-100" alt="Carrossel">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5><?= htmlspecialchars($s['titulo'] ?? '') ?></h5>
                                    <p><?= htmlspecialchars($s['descricao'] ?? '') ?></p>
                                    <?php if (!empty($s['botao']) && !empty($s['link'])): ?>
                                        <a href="<?= htmlspecialchars($s['link']) ?>"
                                            class="btn btn-primary"><?= htmlspecialchars($s['botao']) ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">Nenhum slide disponível no momento.</div>
            <?php endif; ?>
        </section>

        <!-- TÓPICOS -->
        <section class="container my-5" id="topicos">
            <h2 class="mb-4 text-center">Tópicos</h2>
            <div class="row g-4">
                <?php foreach ($topicos as $t): ?>
                    <?php
                    $midia = $t['arquivo_midia'] ?? $t['imagem'] ?? '';
                    $src = getImgSrc('topicos', $midia, $baseImagePath);
                    $ext = strtolower(pathinfo($midia, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'mkv']);
                    ?>
                    <div class="col-md-4">
                        <div class="card topico-card h-100 shadow-sm text-center p-2" id="topico-<?= (int) $t['id'] ?>">
                            <?php if ($isVideo): ?>
                                <div class="video-container">
                                    <img src="<?= $baseImagePath['videos'] . pathinfo($midia, PATHINFO_FILENAME) ?>.jpg"
                                        class="video-thumb" alt="Capa vídeo">
                                    <video class="video-player" src="<?= $baseImagePath['videos'] . $midia ?>"></video>
                                </div>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($src) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($t['titulo']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5><?= htmlspecialchars($t['titulo']) ?></h5>
                                <p><?= htmlspecialchars($t['texto'] ?? '') ?></p>
                                <?php if (!empty($t['botao']) && !empty($t['link'])): ?>
                                    <a href="<?= htmlspecialchars($t['link']) ?>"
                                        class="btn btn-topico"><?= htmlspecialchars($t['botao']) ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- EVENTOS -->
        <section class="container my-5" id="eventos">
            <h2 class="mb-4 text-center">Eventos</h2>
            <?php if ($eventos): ?>
                <div class="row g-4">
                    <?php foreach ($eventos as $e): ?>
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm p-3 text-center">
                                <h5><?= htmlspecialchars($e['titulo']) ?></h5>
                                <?php if (!empty($e['descricao'])): ?>
                                    <p><?= htmlspecialchars($e['descricao']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($e['local'])): ?>
                                    <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($e['local']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($e['dia']) || !empty($e['hora'])): ?>
                                    <p><i class="bi bi-calendar-event"></i>
                                        <?= !empty($e['dia']) ? date('d/m/Y', strtotime($e['dia'])) : '' ?>
                                        <?= !empty($e['hora']) ? date('H:i', strtotime($e['hora'])) : '' ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">Nenhum evento disponível no momento.</div>
            <?php endif; ?>
        </section>

        <!-- INTEGRANTES -->
        <section class="bg-light py-5" id="integrantes">
            <div class="container text-center">
                <h2 class="mb-4">Integrantes</h2>
                <div class="integrantes-marquee-wrapper overflow-hidden position-relative">
                    <div class="integrantes-marquee d-flex">
                        <?php foreach ($integrantes as $int):
                            $img = getImgSrc('integrantes', $int['foto'] ?? '', $baseImagePath);
                            ?>
                            <div class="card integrante-card mx-3 shadow-sm flex-shrink-0">
                                <img src="<?= htmlspecialchars($img) ?>" class="rounded-circle integrante-foto mx-auto mt-3"
                                    alt="<?= htmlspecialchars($int['nome']) ?>">
                                <div class="card-body">
                                    <h6><?= htmlspecialchars($int['nome']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($int['cargo']) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach ($integrantes as $int): // duplicação para loop infinito ?>
                            <div class="card integrante-card mx-3 shadow-sm flex-shrink-0">
                                <img src="<?= htmlspecialchars($img) ?>" class="rounded-circle integrante-foto mx-auto mt-3"
                                    alt="<?= htmlspecialchars($int['nome']) ?>">
                                <div class="card-body">
                                    <h6><?= htmlspecialchars($int['nome']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($int['cargo']) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- RODAPÉ -->
        <footer class="site-footer text-white py-4">
            <div class="container text-center">
                <div class="d-flex justify-content-center flex-wrap gap-3 mb-3">
                    <?php foreach ($rodape as $item): ?>
                        <div class="footer-item d-flex align-items-center">
                            <?= $item['icone_html'] ?? '' ?>
                            <?php if (!empty($item['link'])): ?>
                                <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank"
                                    class="text-white ms-2"><?= htmlspecialchars($item['valor']) ?></a>
                            <?php else: ?>
                                <span class="ms-2"><?= htmlspecialchars($item['valor']) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="small mb-0">© <?= date('Y') ?> <?= htmlspecialchars($tituloSite) ?> - Todos os direitos
                    reservados.</p>
            </div>
        </footer>
    </main>

    <!-- JS -->
    <script>
        // Navbar fixa
        window.addEventListener("scroll", () => {
            document.getElementById("mainNav").classList.toggle("sticky", window.scrollY > 100);
        });

        // Efeito vídeo com capa
        document.querySelectorAll(".video-container").forEach(container => {
            const video = container.querySelector("video");
            const thumb = container.querySelector(".video-thumb");

            thumb.addEventListener("click", () => {
                thumb.style.display = "none";
                video.play();
            });

            video.addEventListener("pause", () => thumb.style.display = "block");
            video.addEventListener("ended", () => thumb.style.display = "block");
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>