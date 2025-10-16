<?php
session_start();
require_once __DIR__ . '/../classes/Carrossel.class.php';

$c = new Carrossel();
$mensagem = '';
$erro = '';

// Excluir
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $id = (int)$_POST['id'];
    $car = $c->buscarPorId($id);
    if ($car) {
        if ($car['fundo'] && file_exists(__DIR__.'/..'.'/'.$car['fundo'])) {
            @unlink(__DIR__.'/..'.'/'.$car['fundo']);
        }
        $c->delete($id);
        $mensagem = "<div class='alert alert-success'>Carrossel excluído.</div>";
    } else {
        $erro = "<div class='alert alert-danger'>Carrossel não encontrado.</div>";
    }
}

$lista = $c->listarTodos();
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Carrossel</h2>
    <?= $mensagem . $erro ?>
    <a href="cad_carrossel.php" class="btn btn-success mb-3">Novo Carrossel</a>

    <div class="row g-4">
        <?php foreach ($lista as $car): ?>
            <div class="col-md-6">
                <div class="border p-3 position-relative text-center" style="background: url('../<?= $car['fundo'] ?>') center/cover no-repeat; height:250px;">
                    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white bg-dark bg-opacity-50 p-2 rounded">
                        <h4><?= htmlspecialchars($car['titulo']) ?></h4>
                        <p><?= htmlspecialchars($car['subtitulo']) ?></p>
                        <?php if ($car['mostrar_botao'] && $car['botao_texto'] && $car['botao_link']): ?>
                            <a href="<?= htmlspecialchars($car['botao_link']) ?>" class="btn btn-primary"><?= htmlspecialchars($car['botao_texto']) ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="mt-2 d-flex gap-1 justify-content-center">
                        <a href="edit_carrossel.php?id=<?= $car['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <form method="post" onsubmit="return confirm('Excluir carrossel?')">
                            <input type="hidden" name="id" value="<?= $car['id'] ?>">
                            <button name="excluir" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>
