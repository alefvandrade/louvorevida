<?php
require_once __DIR__ . '/../classes/Cabecalho.class.php';

$cabecalho = new Cabecalho();
$cabecalho->buscar(); // Pega o último registrado
$tituloCabecalho = $cabecalho->getTitulo() ?? "Vocal Louvor & Vida";
?>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <div class="container">
        &copy; <?= date("Y") ?> <?= htmlspecialchars($tituloCabecalho) ?>- Todos os direitos reservados.
    </div>
</footer>

<style>
html, body {
    height: 100%;
}
body {
    display: flex;
    flex-direction: column;
}
main {
    flex: 1 0 auto; /* empurra o footer para baixo */
}
footer {
    flex-shrink: 0;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>