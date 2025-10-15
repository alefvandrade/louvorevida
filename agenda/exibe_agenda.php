<?php
require_once '../classes/Agenda.class.php';

$agenda = new Agenda();

// Excluir evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $agenda->excluir((int)$_POST['id']);
}

// Listar todos
$eventos = $agenda->listarTodos();
?>

<?php include '../dashboard/_header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Agenda do Vocal</h2>

    <a href="cad_evento.php" class="btn btn-success mb-3">Novo Evento</a>

    <table class="table table-bordered table-striped text-center">
        <thead class="table-dark">
            <tr>
                 <th class="text-center">ID</th>
                 <th class="text-center">Título</th>
                <th class="text-center">Descrição</th>
                <th class="text-center">Local</th>
                <th class="text-center">Data</th>
                <th class="text-center">Hora</th>
                <th class="text-center">Ativo</th>
                <th class="text-center">Ordem</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eventos as $ev): ?>
            <tr>
                <td class="text-center"><?= $ev['id'] ?></td>
                <td class="text-center"><?= htmlspecialchars($ev['titulo']) ?></td>
                <td class="text-center"><?= htmlspecialchars($ev['descricao']) ?></td>
                <td class="text-center"><?= htmlspecialchars($ev['local']) ?></td>
                <td class="text-center"><?= $ev['dia'] ?></td>
                <td class="text-center"><?= $ev['hora'] ?></td>
                <td class="text-center"><?= $ev['ativo'] ? 'Sim' : 'Não' ?></td>
                <td class="text-center"><?= $ev['ordem'] ?></td>
                <td class="d-flex gap-1 justify-content-center">
                    <!-- Editar -->
                    <form method="get" action="edit_evento.php">
                        <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Editar</button>
                    </form>
                    <!-- Excluir -->
                    <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                        <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                        <button type="submit" name="excluir" class="btn btn-danger btn-sm">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../dashboard/_footer.php'; ?>
