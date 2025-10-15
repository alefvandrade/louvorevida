<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$adminNome = 'Nome não encontrado';

if (!empty($_SESSION['admin']['id'])) {
    try {
        $admin = new Admin();
        if ($admin->carregarPorId((int)$_SESSION['admin']['id'])) {
            $nome = trim((string)$admin->getUsuario());
            if ($nome !== '') {
                $adminNome = $nome;
            }
        }
    } catch (Exception $e) {
        // Mantém "Nome não encontrado" caso dê erro
    }
}
?>

<header class="navbar navbar-expand-lg navbar-dark bg-dark py-3 shadow-sm fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <!-- Logo -->
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
      <i class="bi bi-lightning-charge-fill"></i>
      Louvor & Vida
    </a>

    <!-- Botão responsivo -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menus principais centralizados -->
    <div class="collapse navbar-collapse justify-content-center" id="mainMenu">
      <ul class="navbar-nav d-flex justify-content-center flex-wrap gap-4">
        <!-- Exemplo de menu Produtos -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="produtosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Produtos
          </a>
          <ul class="dropdown-menu shadow" aria-labelledby="produtosDropdown">
            <li><a class="dropdown-item" href="<?= $base_url ?>/produtos/cadastro_produto.php">Cadastrar Produto</a></li>
            <li><a class="dropdown-item" href="<?= $base_url ?>/produtos/list_produto.php">Editar Produto</a></li>
          </ul>
        </li>

        <!-- Exemplo de outro menu Clientes -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="clientesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Clientes
          </a>
          <ul class="dropdown-menu shadow" aria-labelledby="clientesDropdown">
            <li><a class="dropdown-item" href="<?= $base_url ?>/clientes/cadastro_cliente.php">Cadastrar Cliente</a></li>
            <li><a class="dropdown-item" href="<?= $base_url ?>/clientes/list_cliente.php">Editar Cliente</a></li>
          </ul>
        </li>

        <!-- Exemplo de outro menu Relatórios -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="relatoriosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Relatórios
          </a>
          <ul class="dropdown-menu shadow" aria-labelledby="relatoriosDropdown">
            <li><a class="dropdown-item" href="#">Relatório 1</a></li>
            <li><a class="dropdown-item" href="#">Relatório 2</a></li>
          </ul>
        </li>
      </ul>
    </div>

    <!-- Quick Box do usuário -->
    <div class="dropdown ms-3">
      <button 
        class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3" 
        type="button" 
        id="userMenu" 
        data-bs-toggle="dropdown" 
        aria-expanded="false">
        <i class="bi bi-person-circle fs-5"></i>
        <?= htmlspecialchars((string)$admin->getUsuario()) ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
        <li>
          <a class="dropdown-item d-flex align-items-center gap-2" href="/../admin/edit_admin.php">
            <i class="bi bi-pencil-square"></i> Editar Perfil
          </a>
        </li>
        <li>
          <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="logout.php">
            <i class="bi bi-box-arrow-right"></i> Sair do Sistema
          </a>
        </li>
      </ul>
    </div>
  </div>
</header>

<!-- Compensar o header fixo -->
<div style="height: 80px;"></div>

<!-- Bootstrap & Ícones -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
  header.navbar {
    z-index: 1050;
  }

  .navbar-nav .nav-link {
    color: #f8f9fa !important;
    font-weight: 500;
    transition: color 0.2s ease;
  }

  .navbar-nav .nav-link:hover {
    color: #0d6efd !important;
  }

  .navbar-nav {
    text-align: center;
  }

  .navbar-nav .nav-item {
    white-space: nowrap;
  }

  .dropdown-menu {
    min-width: 180px;
    border-radius: 0.75rem;
    border: none;
  }

  .dropdown-item:hover {
    background-color: #f8f9fa;
  }

  .btn-outline-light:hover {
    background-color: #f8f9fa;
    color: #212529;
  }

  @media (max-width: 768px) {
    .navbar-nav {
      gap: 1rem;
    }
  }
</style>