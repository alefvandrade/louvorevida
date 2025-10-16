<?php
// require_once '../login/verifica_login.php';
require_once "../classes/Rodape.class.php";
require_once "../classes/Cabecalho.class.php";
require_once "../dashboard/_header.php";

// Rodapé
$rodape = new Rodape();
$dados = $rodape->listarTodos();

// Para preview: gerar HTML igual ao index
$itensRodape = $rodape->listarAtivosComIcones();

// Cabeçalho
$cabecalho = new Cabecalho();
$cabecalho->buscar();
?>

<div class="container mt-4">
    <h2>Itens do Rodapé</h2>
    <a href="cadastro_rodape.php" class="btn btn-primary mb-3">Cadastrar Novo</a>

    <!-- Tabela de gerenciamento -->
    <table class="table table-bordered text-center">
        <thead class="table-light">
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Valor</th>
                <th class="text-center">Ícone</th>
                <th class="text-center">Link</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= ucfirst($item['tipo']) ?></td>
                    <td><?= $item['valor'] ?></td>
                    <td><i class="<?= $item['icone_classe'] ?>"></i> <?= $item['icone_tipo'] ?></td>
                    <td><?= $item['link'] ?></td>
                    <td>
                        <a href="edit_rodape.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="db_rodape.php?acao=excluir&id=<?= $item['id'] ?>"
                            onclick="return confirm('Tem certeza que deseja excluir?')"
                            class="btn btn-danger btn-sm">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pré-visualização do footer -->
    <h3 class="mt-5">Pré-visualização do Footer</h3>
    <footer class="bg-success text-white py-4">
        <div class="container text-center" id="footer-content">
            <div class="d-flex justify-content-center gap-4 flex-wrap mb-3">
                <?php foreach ($itensRodape as $item): ?>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($item['icone_html'])): ?>
                            <span class="fs-5"><?= $item['icone_html'] ?></span>
                        <?php endif; ?>
                        <?php if (!empty($item['link'])): ?>
                            <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank"
                                class="text-white text-decoration-none">
                                <?= htmlspecialchars($item['valor']) ?>
                            </a>
                        <?php else: ?>
                            <span><?= htmlspecialchars($item['valor']) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="small mb-0">
                © <?= date('Y') ?> <?= htmlspecialchars($cabecalho->getTitulo() ?: 'Vocal Louvor & Vida') ?> - Todos os direitos reservados.
            </p>
        </div>
    </footer>
</div>

<?php require_once "../dashboard/_footer.php"; ?>
