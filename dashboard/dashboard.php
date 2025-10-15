<?php
session_start();
require_once __DIR__ . "/_header.php"; // chama o header

// Protege a página para admins
if (empty($_SESSION['admin'])) {
     header("Location: login.php");
     exit;
}

$adminUsuario = $_SESSION['admin']['usuario'];

require_once __DIR__ . "/../classes/Integrantes.class.php";
$integrante = new Integrante();
$totalIntegrantes = count($integrante->listar()); // retorna apenas ativos por padrão
?>

<div class="container my-5">
     <h1 class="mb-5 text-center">Bem-vindo, <?= htmlspecialchars($adminUsuario) ?>!</h1>

     <div class="row">
          <!-- Total de Integrantes -->
          <div class="col-md-6 col-lg-4 mb-4">
               <div class="card shadow-sm">
                    <div class="card-body">
                         <h5 class="card-title">Total de Integrantes</h5>
                         <p class="card-text fs-4"><?= $totalIntegrantes ?></p>
                    </div>
               </div>
          </div>

          <!-- Página Rápida -->
          <div class="col-md-6 col-lg-4 mb-4">
               <div class="card shadow-sm">
                    <div class="card-body">
                         <h5 class="card-title">Página Rápida</h5>
                         <p class="card-text">Acesse rapidamente as seções do sistema:</p>
                         <a href="edit_cabecalho.php" class="btn btn-primary btn-sm">Editar Cabeçalho</a>
                         <a href="integrantes.php" class="btn btn-secondary btn-sm">Gerenciar Integrantes</a>
                    </div>
               </div>
          </div>
     </div>
</div>

<?php
require_once __DIR__ . "/_footer.php"; // chama o footer
?>