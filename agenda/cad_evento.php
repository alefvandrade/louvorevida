<?php
require_once '../classes/Agenda.class.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $evento = new Agenda();
        $evento->setTitulo($_POST['titulo'] ?? '');
        $evento->setDescricao($_POST['descricao'] ?? null);
        $evento->setLocal($_POST['local'] ?? null);
        $evento->setDia($_POST['dia'] ?? null);
        $evento->setHora($_POST['hora'] ?? null);
        $evento->setAtivo(isset($_POST['ativo']) ? 1 : 0);
        $evento->inserir();

        $mensagem = "<div class='alert alert-success text-center'>Evento cadastrado com sucesso!</div>";
    } catch (Exception $e) {
        $mensagem = "<div class='alert alert-danger text-center'>Erro ao cadastrar evento: " . $e->getMessage() . "</div>";
    }
}
?>

<?php include '../dashboard/_header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Cadastro de Evento</h2>

    <?= $mensagem ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Título:</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição:</label>
            <textarea name="descricao" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Local:</label>
            <input type="text" name="local" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Data:</label>
            <input type="date" name="dia" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Hora:</label>
            <input type="time" name="hora" class="form-control">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="ativo" id="ativo" checked>
            <label class="form-check-label" for="ativo">Ativo</label>
        </div>

        <button type="submit" class="btn btn-success w-100">Cadastrar Evento</button>
    </form>
</div>

<?php include '../dashboard/_footer.php'; ?>
