<?php
require_once __DIR__ . "/classes/Carrossel.class.php";
require_once __DIR__ . "/classes/Rodape.class.php";
require_once __DIR__ . "/classes/Cabecalho.class.php";
require_once __DIR__ . "/classes/Topicos.class.php";
require_once __DIR__ . "/classes/Integrantes.class.php";

session_start();

// --- Instâncias ---
$carrossel = new Carrossel();
$slides = $carrossel->listarTodos();

$rodape = new Rodape();
$itensRodape = $rodape->listarTodos();

$cabecalho = new Cabecalho();
$cabecalho->buscar();

$topicoObj = new Topicos();
$topicosAtivos = $topicoObj->listarAtivos();

$integranteObj = new Integrante();
$integrantes = $integranteObj->listar(true);

$usuarioLogado = $_SESSION['usuario'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($cabecalho->getTitulo() ?: 'Site Louvor & Vida') ?></title>

    <!-- Favicon -->
    <?php if ($cabecalho->getLogo()): ?>
        <link rel="shortcut icon" href="<?= htmlspecialchars($cabecalho->getLogo()) ?>" type="image/x-icon">
    <?php else: ?>
        <link rel="shortcut icon" href="imagens/favicon.png" type="image/x-icon">
    <?php endif; ?>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS -->
    <link rel="stylesheet" href="CSS/style_index.css">
</head>

<body class="fade-in d-flex flex-column min-vh-100 bg-light">

    <!-- HEADER -->
    <header class="text-center <?= $cabecalho->getFundo() ? 'header-overlay' : '' ?>"
        style="<?= $cabecalho->getFundo() ? "background-image: url('{$cabecalho->getFundo()}');" : "background: #fff;" ?>">
        <?php if ($cabecalho->getLogo()): ?>
            <img src="<?= htmlspecialchars($cabecalho->getLogo()) ?>" alt="Logo" class="logo mb-3">
        <?php endif; ?>
        <h1><?= htmlspecialchars($cabecalho->getTitulo() ?: 'Louvor & Vida') ?></h1>
        <p><?= htmlspecialchars($cabecalho->getSubtitulo() ?: 'Um ministério de fé e adoração') ?></p>
    </header>

    <!-- NAVBAR -->
    <nav id="mainNav" class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <?php if ($cabecalho->getLogo()): ?>
                    <img src="<?= htmlspecialchars($cabecalho->getLogo()) ?>" alt="Logo" class="logo-navbar">
                <?php endif; ?>
                <span><?= htmlspecialchars($cabecalho->getTitulo() ?: 'Louvor & Vida') ?></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php foreach ($topicosAtivos as $topico): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#topico-<?= (int) $topico['id'] ?>">
                                <?= htmlspecialchars($topico['titulo']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Alternância de tema -->
                <button id="toggleTheme" class="btn me-3" title="Alternar tema">
                    <i class="bi bi-moon"></i>
                </button>

                <!-- Botão Entrar/Login -->
                <?php if (!empty($_SESSION['nome'])): ?>
                    <a href="menu_sistem.php" class="btn btn-success fw-bold">
                        <i class="bi bi-person"></i> <?= htmlspecialchars($_SESSION['nome']) ?>
                    </a>
                <?php else: ?>
                    <a href="login/login.php" class="btn btn-outline-success fw-bold">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-fill">

        <!-- CARROSSEL -->
        <?php if (!empty($slides)): ?>
            <div id="carouselExample" class="carousel slide custom-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($slides as $i => $s):
                        $src = !empty($s['imagem']) ? 'imagens/' . htmlspecialchars($s['imagem']) : 'imagens/default.jpg';
                        ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <img src="<?= $src ?>" class="d-block w-100" alt="<?= htmlspecialchars($s['titulo']) ?>">
                            <?php if (!empty($s['titulo']) || !empty($s['descricao'])): ?>
                                <div class="carousel-caption d-none d-md-block">
                                    <h5><?= htmlspecialchars($s['titulo'] ?? '') ?></h5>
                                    <p><?= htmlspecialchars($s['descricao'] ?? '') ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- TÓPICOS -->
        <section class="container my-5">
            <div class="row g-4">
                <?php foreach ($topicosAtivos as $topico): ?>
                    <div class="col-md-4">
                        <div class="card topico-card shadow-sm" id="topico-<?= (int) $topico['id'] ?>">
                            <img src="imagens/<?= htmlspecialchars($topico['arquivo_midia'] ?? '') ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($topico['titulo']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($topico['titulo']) ?></h5>
                                <p class="card-text text-truncate"><?= htmlspecialchars($topico['texto'] ?? '') ?></p>
                                <button class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#modalTopico<?= (int) $topico['id'] ?>">Ler mais</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="text-white py-3 mt-auto bg-success">
        <div class="container text-center">
            <div class="d-flex justify-content-center flex-wrap gap-3 mb-3">
                <?php foreach ($itensRodape as $item): ?>
                    <div>
                        <?= $item['icone_html'] ?? '' ?>
                        <?php if (!empty($item['link'])): ?>
                            <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank"
                                class="text-white ms-1 text-decoration-none">
                                <?= htmlspecialchars($item['valor']) ?>
                            </a>
                        <?php else: ?>
                            <span class="ms-1"><?= htmlspecialchars($item['valor']) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="small mb-0">© <?= date('Y') ?> <?= htmlspecialchars($cabecalho->getTitulo() ?: 'Louvor & Vida') ?>
                - Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sticky Navbar
        const mainNav = document.getElementById('mainNav');
        const header = document.querySelector('header');
        window.addEventListener('scroll', () => {
            mainNav.classList.toggle('sticky', window.scrollY >= header.offsetHeight);
        });

        // Alternância de tema
        const toggleBtn = document.getElementById('toggleTheme');
        const body = document.body;

        function loadTheme() {
            if (localStorage.getItem('theme') === 'dark') {
                body.classList.add('dark-mode');
                toggleBtn.innerHTML = '<i class="bi bi-sun"></i>';
            } else {
                body.classList.remove('dark-mode');
                toggleBtn.innerHTML = '<i class="bi bi-moon"></i>';
            }
        }

        toggleBtn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            toggleBtn.innerHTML = isDark ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';
        });

        loadTheme();
    </script>
</body>

</html>