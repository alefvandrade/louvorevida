<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../classes/Cabecalho.class.php';
require_once __DIR__ . '/../classes/Admin.class.php';

// Valores padrão
$cabecalhoTitulo = "Vocal Louvor & Vida";
$cabecalhoSubtitulo = "Louvor e adoração";
$cabecalhoFundo = "#0d6efd"; // cor padrão azul
$cabecalhoFundoImagem = null;

$cabecalho = new Cabecalho();
if ($cabecalho->buscar()) {
    $cabecalhoTitulo = htmlspecialchars($cabecalho->getTitulo() ?: $cabecalhoTitulo);
    $cabecalhoSubtitulo = htmlspecialchars($cabecalho->getSubtitulo() ?: $cabecalhoSubtitulo);

    if ($cabecalho->getFundo() && file_exists("../" . $cabecalho->getFundo())) {
        $cabecalhoFundoImagem = $cabecalho->getFundo();
    } else {
        $cabecalhoFundo = htmlspecialchars($cabecalhoFundo);
    }

    $logoMenu = $cabecalho->getLogo() ?: null;
} else {
    $logoMenu = null;
}

// Usuário logado
$adminNome = 'Nome não encontrado';
$adminUsuario = null;

if (!empty($_SESSION['admin']['id'])) {
    try {
        $admin = new Admin();
        if ($admin->carregarPorId((int) $_SESSION['admin']['id'])) {
            $nome = trim((string) $admin->getUsuario());
            if ($nome !== '') {
                $adminNome = $nome;
                $adminUsuario = $admin;
            }
        }
    } catch (Exception $e) {
        // Nenhuma mensagem de erro exibida
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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --header-bg-color:
                <?= $cabecalhoFundo ?>
            ;
        }

        header.navbar {
            background-color: var(--header-bg-color);
            <?php if ($cabecalhoFundoImagem): ?>
                background-image: url('../<?= $cabecalhoFundoImagem ?>');
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
    </style>
</head>

<body class="bg-light text-dark">

    <header class="navbar navbar-expand-lg navbar-dark shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-music-note-beamed fs-4"></i>
                <?= $cabecalhoTitulo ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarConteudo">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarConteudo">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a href="../dashboard/dashboard.php" class="nav-link">Início</a></li>
                    <li class="nav-item"><a href="agenda.php" class="nav-link">Agenda</a></li>
                    <li class="nav-item"><a href="ministerios.php" class="nav-link">Ministérios</a></li>
                    <li class="nav-item"><a href="contato.php" class="nav-link">Contato</a></li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <i class="bi bi-brightness-high theme-toggle fs-5 me-3" title="Trocar tema"></i>
                    </li>
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

        themeToggle.addEventListener('click', () => {
            setTheme(!body.classList.contains('bg-dark'));
        });

        if (localStorage.getItem('theme') === 'dark') setTheme(true);
    </script>