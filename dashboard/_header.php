<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../classes/Cabecalho.class.php';
require_once __DIR__ . '/../classes/Admin.class.php';

$cabecalho = new Cabecalho();
$cabecalho->buscar();

// Cabeçalho
$cabecalhoTitulo = htmlspecialchars($cabecalho->getTitulo() ?: "Vocal Louvor & Vida");
$cabecalhoSubtitulo = htmlspecialchars($cabecalho->getSubtitulo() ?: "Louvor e adoração");
$cabecalhoFundo = $cabecalho->getCorFundo() ?: "#0d6efd";
$cabecalhoFundoImagem = $cabecalho->getFundo() && file_exists("../" . $cabecalho->getFundo()) ? "../" . $cabecalho->getFundo() : null;
$logoMenu = $cabecalho->getLogo() ?: null;

// Usuário logado
$adminNome = 'Nome não encontrado';
$adminUsuario = null;
if (!empty($_SESSION['admin']['id'])) {
    try {
        $admin = new Admin();
        if ($admin->carregarPorId((int) $_SESSION['admin']['id'])) {
            $adminNome = trim((string) $admin->getUsuario()) ?: $adminNome;
            $adminUsuario = $admin;
        }
    } catch (Exception $e) {
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?= $cabecalhoTitulo ?></title>
    <meta name="description" content="<?= $cabecalhoSubtitulo ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <?php if ($logoMenu && file_exists('../' . $logoMenu)): ?>
        <link rel="icon" href="../<?= $logoMenu ?>" type="image/png">
    <?php else: ?>
        <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <?php endif; ?>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        header.navbar {
            background-color:
                <?= $cabecalhoFundo ?>
            ;
            <?php if ($cabecalhoFundoImagem): ?>
                background-image: url('<?= $cabecalhoFundoImagem ?>');
                background-size: cover;
                background-position: center;
            <?php endif; ?>
        }

        .nav-link {
            color: #fff !important;
        }

        .nav-link:hover {
            opacity: 0.8;
        }

        .navbar-nav {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            flex-grow: 1;
        }

        .theme-toggle {
            cursor: pointer;
            color: white;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand img {
            height: 40px;
            object-fit: contain;
        }
    </style>
</head>

<body class="bg-light text-dark">

    <header class="navbar navbar-expand-lg navbar-dark shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <?php if ($logoMenu && file_exists('../' . $logoMenu)): ?>
                    <img src="../<?= $logoMenu ?>" alt="Logo">
                <?php endif; ?>
                <span><?= $cabecalhoTitulo ?></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarConteudo">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarConteudo">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a href="../dashboard/dashboard.php" class="nav-link">Início</a></li>
                    <li class="nav-item"><a href="../agenda/exibe_agenda.php" class="nav-link">Agenda</a></li>
                    <li class="nav-item"><a href="../integrantes/exibe_integrante.php" class="nav-link">Integrantes</a></li>
                    <li class="nav-item"><a href="contato.php" class="nav-link">Contato</a></li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><i class="bi bi-brightness-high theme-toggle fs-5 me-3"
                            title="Trocar tema"></i></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars(trim((string) ($adminUsuario ? $adminUsuario->getUsuario() : $adminNome))) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="../admin/edit_admin.php">Editar Perfil</a></li>
                            <li><a class="dropdown-item" href="../cabecalho/edit_cabecalho.php">Editar Cabeçalho</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="../login/logout.php">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <script>
        const themeToggle = document.querySelector('.theme-toggle');
        const body = document.body;

        function setTheme(dark) {
            if (dark) {
                body.classList.replace('bg-light', 'bg-dark');
                body.classList.replace('text-dark', 'text-light');
                themeToggle.classList.replace('bi-brightness-high', 'bi-moon-stars');
                localStorage.setItem('theme', 'dark');
            } else {
                body.classList.replace('bg-dark', 'bg-light');
                body.classList.replace('text-light', 'text-dark');
                themeToggle.classList.replace('bi-moon-stars', 'bi-brightness-high');
                localStorage.setItem('theme', 'light');
            }
        }

        themeToggle.addEventListener('click', () => { setTheme(!body.classList.contains('bg-dark')) });
        if (localStorage.getItem('theme') === 'dark') setTheme(true);
    </script>