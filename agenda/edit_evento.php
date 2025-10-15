<?php
require_once '../classes/Agenda.class.php';

$evento = null;
$mensagem = '';
$erro = '';

if (!empty($_GET['id'])) {
    $e = new Agenda();
    if ($dados = $e->buscarPorId((int)$_GET['id'])) {
        $evento = $e;
        $evento->setId($dados['id']);
        $evento->setTitulo($dados['titulo']);
        $evento->setDescricao($dados['descricao']);
        $evento->setLocal($dados['local']);
        $evento->setDia($dados['dia']);
        $evento->setHora($dados['hora']);
        $evento->setAtivo((int)$dados['ativo']);
        $evento->setOrdem((int)$dados['ordem']);
    }
}

// Processar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $evento = new Agenda();
        $evento->setId((int)$_POST['id']);
        $evento->setTitulo($_POST['titulo'] ?? '');
        $evento->setDescricao($_POST['descricao'] ?? null);
        $evento->setLocal($_POST['local'] ?? null);
        $evento->setDia($_POST['dia'] ?? null);
        $evento->setHora($_POST['hora'] ?? null);
        $evento->setAtivo(isset($_POST['ativo']) ? 1 : 0);
        $evento->setOrdem((int)($_POST['ordem'] ?? 0));

        if ($evento->atualizar()) {
            $mensagem = "<div class='alert alert-success text-center'>Evento atualizado com sucesso!</div>";
        } else {
            $erro = "<div class='alert alert-danger text-center'>Erro ao atualizar evento.</div>";
        }
    } catch (Exception $e) {
        $erro = "<div class='alert alert-danger text-center'>Erro: " . $e->getMessage() . "</div>";
    }
}
?>

<?php include '../dashboard/_header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Editar Evento</h2>

    <?= $mensagem ?>
    <?= $erro ?>

    <?php if ($evento): ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $evento->getId() ?>">

        <div class="mb-3">
            <label class="form-label">Título:</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($evento->getTitulo()) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição:</label>
            <textarea name="descricao" class="form-control"><?= htmlspecialchars($evento->getDescricao()) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Local:</label>
            <input type="text" name="local" class="form-control" value="<?= htmlspecialchars($evento->getLocal()) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Data:</label>
            <input type="date" name="dia" class="form-control" value="<?= $evento->getDia() ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Hora:</label>
            <input type="time" name="hora" class="form-control" value="<?= $evento->getHora() ?>">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="ativo" id="ativo" <?= $evento->getAtivo() ? 'checked' : '' ?>>
            <label class="form-check-label" for="ativo">Ativo</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Ordem (opcional):</label>
            <input type="number" name="ordem" class="form-control" value="<?= $evento->getOrdem() ?>">
        </div>

        <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
    </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">Evento não encontrado.</div>
    <?php endif; ?>
</div>

<?php include '../dashboard/_footer.php'; ?>
