<?php
// videos/edit.php
session_start();
require_once __DIR__ . '/../classes/Videos.class.php';

$mensagem=''; $erro='';
$dirVideos=__DIR__.'/../imagens/videos/';
$webVideos='imagens/videos/';

$vObj=new Videos();
$video=null;

if(!empty($_GET['id'])){
    $video=$vObj->buscarPorId((int)$_GET['id']);
}

if($_SERVER['REQUEST_METHOD']==='POST'){
    try{
        $id=(int)$_POST['id'];
        $vObj->setId($id);
        $vObj->setTituloVideo($_POST['titulo_video'] ?? '');
        $vObj->setDataGravacao($_POST['data_gravacao'] ?: null);
        $vObj->setExibirNoIndex(isset($_POST['exibir_no_index'])?1:0);
        $vObj->setOrientacao($_POST['orientacao'] ?? 'auto');
        $vObj->setAtivo(isset($_POST['ativo'])?1:0);

        // capa
        if(!empty($_FILES['capa_video']['name'])){
            if(!empty($_POST['capa_atual'])) @unlink(__DIR__.'/../'.$_POST['capa_atual']);
            $ext=pathinfo($_FILES['capa_video']['name'], PATHINFO_EXTENSION);
            $novo='capa_'.time().'.'.$ext;
            if(move_uploaded_file($_FILES['capa_video']['tmp_name'],$dirVideos.$novo)){
                $vObj->setCapaVideo($webVideos.$novo);
            }
        } else {
            $vObj->setCapaVideo($_POST['capa_atual']??null);
        }

        // video
        if(!empty($_FILES['video']['name'])){
            if(!empty($_POST['video_atual'])) @unlink(__DIR__.'/../'.$_POST['video_atual']);
            $ext=pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
            $novo='video_'.time().'.'.$ext;
            if(move_uploaded_file($_FILES['video']['tmp_name'],$dirVideos.$novo)){
                $vObj->setVideo($webVideos.$novo);
            }
        } else {
            $vObj->setVideo($_POST['video_atual']??null);
        }

        if($vObj->atualizar()){
            $mensagem="<div class='alert alert-success'>Vídeo atualizado com sucesso.</div>";
            $video=$vObj->buscarPorId($id);
        } else $erro="<div class='alert alert-danger'>Erro ou nada alterado.</div>";

    }catch(Exception $e){
        $erro="<div class='alert alert-danger'>Erro: ".htmlspecialchars($e->getMessage())."</div>";
    }
}
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
<h2>Editar Vídeo</h2>
<?= $mensagem.$erro ?>

<?php if($video): ?>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= $video['id'] ?>">
<input type="hidden" name="capa_atual" value="<?= htmlspecialchars($video['capa_video']) ?>">
<input type="hidden" name="video_atual" value="<?= htmlspecialchars($video['video']) ?>">

<div class="mb-3">
    <label class="form-label">Título</label>
    <input type="text" name="titulo_video" class="form-control" value="<?= htmlspecialchars($video['titulo_video']) ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Data de Gravação</label>
    <input type="date" name="data_gravacao" class="form-control" value="<?= $video['data_gravacao'] ?>">
</div>

<div class="mb-3">
    <label class="form-label">Capa</label>
    <input type="file" name="capa_video" accept="image/*" class="form-control">
    <?php if(!empty($video['capa_video'])): ?>
        <img src="<?= htmlspecialchars($video['capa_video']) ?>" style="max-width:200px;margin-top:10px;">
    <?php endif; ?>
    <div id="preview_capa_edit" style="margin-top:10px;"></div>
</div>

<div class="mb-3">
    <label class="form-label">Arquivo de vídeo</label>
    <input type="file" name="video" accept="video/*" class="form-control">
    <?php if(!empty($video['video'])): ?>
        <video src="<?= htmlspecialchars($video['video']) ?>" controls style="max-width:100%; margin-top:10px;"></video>
    <?php endif; ?>
    <div id="preview_video_edit" style="margin-top:10px;"></div>
</div>

<div class="mb-3">
    <label class="form-label">Orientação</label>
    <select name="orientacao" class="form-select">
        <option value="auto" <?= $video['orientacao']=='auto'?'selected':'' ?>>Auto</option>
        <option value="horizontal" <?= $video['orientacao']=='horizontal'?'selected':'' ?>>Horizontal</option>
        <option value="vertical" <?= $video['orientacao']=='vertical'?'selected':'' ?>>Vertical</option>
    </select>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="exibir_no_index" class="form-check-input" id="exibir_no_index" <?= $video['exibir_no_index']?'checked':'' ?>>
    <label class="form-check-label" for="exibir_no_index">Exibir no index</label>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="ativo" class="form-check-input" id="ativo" <?= $video['ativo']?'checked':'' ?>>
    <label class="form-check-label" for="ativo">Ativo</label>
</div>

<button class="btn btn-primary">Atualizar Vídeo</button>
</form>
<?php else: ?>
<div class="alert alert-warning">Vídeo não encontrado.</div>
<?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    function previewFile(input, previewId){
        const container=document.getElementById(previewId);
        container.innerHTML='';
        if(input.files && input.files[0]){
            const file=input.files[0];
            const ext=file.name.split('.').pop().toLowerCase();
            if(['jpg','jpeg','png','gif','webp'].includes(ext)){
                const img=document.createElement('img');
                img.style.maxWidth='200px';
                img.src=URL.createObjectURL(file);
                container.appendChild(img);
            }else if(['mp4','webm','ogg','mov','avi','mkv'].includes(ext)){
                const video=document.createElement('video');
                video.style.maxWidth='100%';
                video.controls=true;
                video.src=URL.createObjectURL(file);
                container.appendChild(video);
            }
        }
    }

    const inputCapa=document.querySelector('input[name="capa_video"]');
    if(inputCapa) inputCapa.addEventListener('change', function(){ previewFile(this,'preview_capa_edit'); });

    const inputVid=document.querySelector('input[name="video"]');
    if(inputVid) inputVid.addEventListener('change', function(){ previewFile(this,'preview_video_edit'); });
});
</script>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>
