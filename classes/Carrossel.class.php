<?php
require_once __DIR__ . '/CRUD.class.php';

class Carrossel extends CRUD
{
    public ?int $id = null;
    public ?string $titulo = null;
    public ?string $subtitulo = null;
    public ?string $fundo = null;
    public ?string $botao_texto = null;
    public ?string $botao_link = null;
    public ?int $mostrar_botao = null;
    public ?int $ordem = null;
    public ?int $ativo = null;

    public function __construct()
    {
        parent::__construct("carrossel");
        $this->setCampos([
            "titulo",
            "subtitulo",
            "fundo",
            "botao_texto",
            "botao_link",
            "mostrar_botao",
            "ordem",
            "ativo"
        ]);
        $this->chavePrimaria = "id";
    }

    /* ==========================================================
       Próxima ordem do carrossel
    ========================================================== */
    public function proximaOrdem(): int
    {
        $sql = "SELECT MAX(ordem) AS max FROM carrossel";
        $resultado = $this->conexao->query($sql);
        $linha = $resultado->fetch_assoc();
        return (($linha['max'] ?? 0) + 1);
    }

    /* ==========================================================
       Inserir novo carrossel
    ========================================================== */
    public function inserir(): int
    {
        if (!$this->ordem) {
            $this->ordem = $this->proximaOrdem();
        }

        return $this->create([
            "titulo" => $this->titulo,
            "subtitulo" => $this->subtitulo,
            "fundo" => $this->fundo,
            "botao_texto" => $this->botao_texto,
            "botao_link" => $this->botao_link,
            "mostrar_botao" => $this->mostrar_botao ?? 1,
            "ordem" => $this->ordem,
            "ativo" => $this->ativo ?? 1
        ]);
    }

    /* ==========================================================
       Atualizar carrossel existente
    ========================================================== */
    public function atualizar(): bool
    {
        return $this->update($this->id, [
            "titulo" => $this->titulo,
            "subtitulo" => $this->subtitulo,
            "fundo" => $this->fundo,
            "botao_texto" => $this->botao_texto,
            "botao_link" => $this->botao_link,
            "mostrar_botao" => $this->mostrar_botao,
            "ordem" => $this->ordem,
            "ativo" => $this->ativo
        ]);
    }

    /* ==========================================================
       Listar todos os carrosseis (opcional: ativos)
    ========================================================== */
    public function listarTodos(bool $ativosApenas = false): array
    {
        $condicao = $ativosApenas ? "ativo = 1" : "";
        return $this->read($condicao, [], "ordem ASC");
    }

    /* ==========================================================
       Buscar por ID
    ========================================================== */
    public function buscarPorId(int $id): ?array
    {
        return $this->find($id);
    }
}
?>
