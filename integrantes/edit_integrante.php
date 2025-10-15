<?php
if (session_status() !== PHP_SESSION_ACTIVE)
     session_start();
require_once '../classes/Integrantes.class.php';

$erro = '';
$mensagem = '';

$dirImagens = __DIR__ . '/../imagens/img_integrantes/';
$dirWeb = '../imagens/img_integrantes/';

if (!is_dir($dirImagens))
     mkdir($dirImagens, 0755, true);

$integrante = null;
if (!empty($_GET['id'])) {
     $i = new Integrante();
     $dados = $i->read("id = ?", [$_GET['id']])[0] ?? null;
     if ($dados) {
          $integrante = $i;
          $integrante->setId($dados['id']);
          $integrante->setNome($dados['nome']);
          $integrante->setNomeUser($dados['nome_user']);
          $integrante->setFuncao($dados['funcao']);
          $integrante->setFoto($dados['foto']);
          $integrante->setAtivo($dados['ativo']);
     }
}

// Processar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     try {
          $integrante = new Integrante();
          $integrante->setId((int) $_POST['id']);
          $integrante->setNome($_POST['nome'] ?? '');
          $integrante->setNomeUser($_POST['nome_user'] ?? '');
          $integrante->setFuncao($_POST['funcao'] ?? '');
          $integrante->setAtivo(isset($_POST['ativo']));
          if (!empty($_POST['senha']))
               $integrante->setSenha($_POST['senha']);

          // Foto
          $fotoAtual = $_POST['foto_atual'] ?? '';
          if (!empty($_FILES['foto']['name'])) {
               if ($fotoAtual && file_exists(__DIR__ . '/../' . $fotoAtual)) {
                    unlink(__DIR__ . '/../' . $fotoAtual);
               }
               $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
               $novoNome = 'foto_' . time() . '.' . $ext;
               $destino = $dirImagens . $novoNome;
               if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                    $integrante->setFoto($dirWeb . $novoNome);
               }
          } else {
               $integrante->setFoto($fotoAtual);
          }

          if ($integrante->atualizar()) {
               $mensagem = "<div class='alert alert-success text-center'>Integrante atualizado com sucesso!</div>";
          } else {
               $erro = "<div class='alert alert-danger text-center'>Erro ao atualizar.</div>";
          }
     } catch (Exception $e) {
          $erro = "<div class='alert alert-danger text-center'>Erro: " . $e->getMessage() . "</div>";
     }
}
?>

<?php include '../dashboard/_header.php'; ?>

<div class="container mt-5">
     <h2 class="text-center mb-4">Editar Integrante</h2>
     <?= $mensagem ?>
     <?= $erro ?>

     <?php if ($integrante): ?>
          <div class="row justify-content-center">
               <div class="col-md-5">
                    <form method="POST" enctype="multipart/form-data">
                         <input type="hidden" name="id" value="<?= $integrante->getId() ?>">
                         <input type="hidden" name="foto_atual" value="<?= htmlspecialchars($integrante->getFoto()) ?>">

                         <div class="mb-3">
                              <label class="form-label">Nome:</label>
                              <input type="text" name="nome" id="nome" class="form-control"
                                   value="<?= htmlspecialchars($integrante->getNome()) ?>" required>
                         </div>

                         <div class="mb-3">
                              <label class="form-label">Nome de Usuário:</label>
                              <input type="text" name="nome_user" id="nome_user" class="form-control"
                                   value="<?= htmlspecialchars($integrante->getNomeUser()) ?>" required>
                         </div>

                         <div class="mb-3 position-relative">
                              <label class="form-label">Senha (deixe em branco para não alterar):</label>
                              <input type="password" name="senha" id="senha" class="form-control">
                              <span id="toggleSenha"
                                   style="position:absolute; top:38px; right:10px; cursor:pointer; color:black;">
                                   <i class="bi bi-eye"></i>
                              </span>
                         </div>

                         <div class="mb-3">
                              <label class="form-label">Função:</label>
                              <input list="funcoes" name="funcao" id="funcao" class="form-control"
                                   placeholder="Digite ou selecione..."
                                   value="<?= htmlspecialchars($integrante->getFuncao()) ?>">
                              <datalist id="funcoes">
                                   <option value="1º tenor">
                                   <option value="2º tenor">
                                   <option value="tenor">
                                   <option value="soprano">
                                   <option value="mezzo">
                                   <option value="contralto">
                                   <option value="barítono">
                                   <option value="baixo">
                                   <option value="orador">
                              </datalist>
                         </div>

                         <div class="mb-3">
                              <label class="form-label">Foto:</label>
                              <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                         </div>

                         <div class="mb-3 form-check">
                              <input type="checkbox" name="ativo" class="form-check-input" id="ativo"
                                   <?= $integrante->isAtivo() ? 'checked' : '' ?>>
                              <label class="form-check-label" for="ativo">Ativo</label>
                         </div>

                         <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                    </form>
               </div>

               <div class="col-md-4 text-center">
                    <h5>Pré-visualização do Cartão:</h5>
                    <div class="card" style="width: 18rem; margin:auto;" id="cardPreview">
                         <img src="<?= $integrante->getFoto() ?: '../imagens/default_user.png' ?>" class="card-img-top"
                              id="fotoPreview" alt="Foto">
                         <div class="card-body">
                              <h5 class="card-title" id="cardNome"><?= htmlspecialchars($integrante->getNome()) ?></h5>
                              <p class="card-text" id="cardFuncao"><?= htmlspecialchars($integrante->getFuncao()) ?></p>
                         </div>
                    </div>
               </div>
          </div>

          <script>
               // Atualizar cartão em tempo real
               document.getElementById('nome').addEventListener('input', e => document.getElementById('cardNome').textContent = e.target.value);
               document.getElementById('funcao').addEventListener('input', e => document.getElementById('cardFuncao').textContent = e.target.value);
               document.getElementById('foto').addEventListener('change', e => {
                    const file = e.target.files[0];
                    if (file) document.getElementById('fotoPreview').src = URL.createObjectURL(file);
               });

               // Mostrar/ocultar senha
               const toggleSenha = document.getElementById('toggleSenha');
               const senhaInput = document.getElementById('senha');
               toggleSenha.addEventListener('click', () => {
                    if (senhaInput.type === 'password') {
                         senhaInput.type = 'text';
                         toggleSenha.innerHTML = '<i class="bi bi-eye-slash"></i>';
                    } else {
                         senhaInput.type = 'password';
                         toggleSenha.innerHTML = '<i class="bi bi-eye"></i>';
                    }
               });
          </script>
     <?php endif; ?>
</div>

<?php include '../dashboard/_footer.php'; ?>