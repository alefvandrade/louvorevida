<?php
// rodape/cad_rodape.php
session_start();
require_once __DIR__ . '/../classes/Rodape.class.php';

$mensagem = '';
$erro = '';

$r = new Rodape();
$icones = $r->listarIcones();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $r->tipo = $_POST['tipo'] ?? '';
        $r->valor = $_POST['valor'] ?? '';
        $r->icone_id = !empty($_POST['icone_id']) ? (int) $_POST['icone_id'] : null;
        $r->link = $_POST['link'] ?: null;

        $r->adicionar();
        $mensagem = "<div class='alert alert-success'>Item do rodapé cadastrado com sucesso.</div>";
    } catch (Exception $e) {
        $erro = "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<?php include __DIR__ . '/../dashboard/_header.php'; ?>

<div class="container my-5">
    <h2>Cadastrar Item do Rodapé</h2>
    <?= $mensagem . $erro ?>

    <form method="post" id="formRodape">
        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" name="tipo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Valor</label>
            <input type="text" name="valor" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Ícone</label>
            <select name="icone_id" class="form-select" id="icone_select">
                <option value="">-- Nenhum --</option>
                <?php foreach ($icones as $ico): ?>
                    <option value="<?= $ico['id'] ?>" data-classe="<?= htmlspecialchars($ico['classe']) ?>">
                        <?= htmlspecialchars($ico['tipo']) ?> (<?= htmlspecialchars($ico['descricao']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Preview do ícone -->
        <div class="mb-3">
            <label>Pré-visualização do ícone:</label>
            <div class="fs-3" id="icone_preview"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Link (opcional)</label>
            <input type="url" name="link" class="form-control">
        </div>

        <button class="btn btn-success">Cadastrar</button>
    </form>
</div>

<script>
    const iconeSelect = document.getElementById('icone_select');
    const iconePreview = document.getElementById('icone_preview');

    function atualizarPreview() {
        const option = iconeSelect.options[iconeSelect.selectedIndex];
        const classe = option.getAttribute('data-classe') || '';
        iconePreview.innerHTML = classe ? `<i class="${classe}"></i>` : '';
    }

    iconeSelect.addEventListener('change', atualizarPreview);
    window.addEventListener('load', atualizarPreview);
</script>

<?php include __DIR__ . '/../dashboard/_footer.php'; ?>