<?php
if (session_status() !== PHP_SESSION_ACTIVE)
     session_start();
require_once '../classes/Integrantes.class.php';

$integranteObj = new Integrante();

// Excluir integrante completamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
     $id = (int) $_POST['id'];
     $dados = $integranteObj->read("id = ?", [$id])[0] ?? null;
     if ($dados) {
          // Apagar foto
          if ($dados['foto'] && file_exists(__DIR__ . '/../' . $dados['foto'])) {
               unlink(__DIR__ . '/../' . $dados['foto']);
          }
          // Excluir do banco
          $integranteObj->delete($id);
     }
}

// Listar todos ativos
$integrantes = $integranteObj->listar(true);
?>

<?php include '../dashboard/_header.php'; ?>

<div class="container mt-5">
     <h2 class="text-center mb-4">Integrantes</h2>
     <a href="cad_integrante.php" class="btn btn-success mb-3">Novo Integrante</a>

     <div class="row g-3">
          <?php foreach ($integrantes as $i): ?>
               <div class="col-md-3">
                    <div class="card h-100">
                         <img src="<?= htmlspecialchars($i['foto'] ?: '../imagens/default_user.png') ?>" class="card-img-top"
                              alt="<?= htmlspecialchars($i['nome']) ?>">
                         <div class="card-body">
                              <h5 class="card-title"><?= htmlspecialchars($i['nome']) ?></h5>
                              <p class="card-text"><?= htmlspecialchars($i['funcao']) ?></p>
                         </div>
                         <ul class="list-group list-group-flush">
                              <li class="list-group-item">Usuário: <?= htmlspecialchars($i['nome_user']) ?></li>
                              <li class="list-group-item">Ativo: <?= $i['ativo'] ? 'Sim' : 'Não' ?></li>
                         </ul>
                         <div class="card-body d-flex gap-2">
                              <form method="get" action="edit_integrante.php" class="w-50">
                                   <input type="hidden" name="id" value="<?= $i['id'] ?>">
                                   <button type="submit" class="btn btn-primary w-100 btn-sm">Editar</button>
                              </form>
                              <form method="post" onsubmit="return confirm('Deseja excluir este integrante?')" class="w-50">
                                   <input type="hidden" name="id" value="<?= $i['id'] ?>">
                                   <button type="submit" name="excluir" class="btn btn-danger w-100 btn-sm">Excluir</button>
                              </form>
                         </div>
                    </div>
               </div>
          <?php endforeach; ?>
     </div>
</div>

<?php include '../dashboard/_footer.php'; ?>