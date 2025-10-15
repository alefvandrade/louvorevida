<?php
if (session_status() !== PHP_SESSION_ACTIVE)
     session_start();
require_once '../classes/Integrantes.class.php';

$mensagem = '';
$erro = '';

$dirImagens = __DIR__ . '/../imagens/img_integrantes/';
$dirWeb = '../imagens/img_integrantes/';

if (!is_dir($dirImagens))
     mkdir($dirImagens, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     try {
          $integrante = new Integrante();
          $integrante->setNome($_POST['nome'] ?? '');
          $integrante->setNomeUser($_POST['nome_user'] ?? '');
          $integrante->setSenha($_POST['senha'] ?? '');
          $integrante->setFuncao($_POST['funcao'] ?? '');
          $integrante->setAtivo(isset($_POST['ativo']));

          // Foto
          if (!empty($_FILES['foto']['name'])) {
               $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
               $novoNome = 'foto_' . time() . '.' . $ext;
               $destino = $dirImagens . $novoNome;
               if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                    $integrante->setFoto($dirWeb . $novoNome);
               }
          }

          $integrante->cadastrar();
          $mensagem = "<div class='alert alert-success text-center'>Integrante cadastrado com sucesso!</div>";
     } catch (Exception $e) {
          $erro = "<div class='alert alert-danger text-center'>Erro ao cadastrar: " . $e->getMessage() . "</div>";
     }
}
?>

<?php include '../dashboard/_header.php'; ?>

<div class="container mt-5">
     <h2 class="text-center mb-4">Cadastro de Integrante</h2>

     <?= $mensagem ?>
     <?= $erro ?>

     <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
               <label class="form-label">Nome:</label>
               <input type="text" name="nome" class="form-control" required>
          </div>

          <div class="mb-3">
               <label class="form-label">Nome de Usuário:</label>
               <input type="text" name="nome_user" class="form-control" required>
          </div>

          <div class="mb-3 position-relative">
               <label class="form-label">Senha:</label>
               <input type="password" name="senha" id="senha" class="form-control" required>
               <span id="toggleSenha" style="position:absolute; top:38px; right:10px; cursor:pointer; color:black;">
                    <i class="bi bi-eye"></i>
               </span>
          </div>

          <div class="mb-3">
               <label class="form-label">Função:</label>
               <input list="funcoes" name="funcao" class="form-control" placeholder="Digite ou selecione...">
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
               <label class="form-label">Foto (opcional):</label>
               <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
               <div class="mt-2">
                    <img id="fotoPreview" src="" style="max-height:150px; display:none;" class="img-thumbnail">
               </div>
          </div>

          <div class="mb-3 form-check">
               <input type="checkbox" name="ativo" class="form-check-input" id="ativo" checked>
               <label class="form-check-label" for="ativo">Ativo</label>
          </div>

          <button type="submit" class="btn btn-success w-100">Cadastrar Integrante</button>
     </form>
</div>

<script>
     // Pré-visualização da foto
     const fotoInput = document.getElementById('foto');
     const fotoPreview = document.getElementById('fotoPreview');
     fotoInput.addEventListener('change', e => {
          const file = e.target.files[0];
          if (file) {
               fotoPreview.src = URL.createObjectURL(file);
               fotoPreview.style.display = 'block';
          } else {
               fotoPreview.style.display = 'none';
          }
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

<?php include '../dashboard/_footer.php'; ?>