<?php
require_once __DIR__ . '/CRUD.class.php';

class Topicos extends CRUD
{
    public ?int $id = null;
    public string $titulo = '';
    public string $texto = '';
    public ?string $botao_texto = null;
    public ?string $botao_link = null;
    public string $tipo_midia = 'nenhum';
    public ?string $arquivo_midia = null;
    public string $lado = 'direita';
    public int $ativo = 1;
    public int $ordem = 0;

    public function __construct()
    {
        parent::__construct('topicos');
        $this->setCampos([
            'titulo',
            'texto',
            'botao_texto',
            'botao_link',
            'tipo_midia',
            'arquivo_midia',
            'lado',
            'ativo',
            'ordem'
        ]);
        $this->chavePrimaria = 'id';
    }

    /* =====================
       Getters e Setters
    ===================== */
    public function setId($id)
    {
        $this->id = (int) $id ?: null;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = trim((string) $titulo);
    }
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTexto($texto)
    {
        $this->texto = trim((string) $texto);
    }
    public function getTexto(): string
    {
        return $this->texto;
    }

    public function setBotaoTexto($texto)
    {
        $this->botao_texto = $texto ?: null;
    }
    public function getBotaoTexto(): ?string
    {
        return $this->botao_texto;
    }

    public function setBotaoLink($link)
    {
        $this->botao_link = $link ?: null;
    }
    public function getBotaoLink(): ?string
    {
        return $this->botao_link;
    }

    public function setTipoMidia($tipo)
    {
        $permitidos = ['imagem', 'video', 'nenhum'];
        $this->tipo_midia = in_array($tipo, $permitidos) ? $tipo : 'nenhum';
    }
    public function getTipoMidia(): string
    {
        return $this->tipo_midia;
    }

    public function setArquivoMidia($arquivo)
    {
        $this->arquivo_midia = $arquivo ?: null;
        $this->detectarTipoMidia();
    }
    public function getArquivoMidia(): ?string
    {
        return $this->arquivo_midia;
    }

    public function setLado($lado)
    {
        $permitidos = ['esquerda', 'direita'];
        $this->lado = in_array($lado, $permitidos) ? $lado : 'direita';
    }
    public function getLado(): string
    {
        return $this->lado;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = ($ativo === 1) ? 1 : 0;
    }
    public function getAtivo(): int
    {
        return $this->ativo;
    }

    public function setOrdem($ordem)
    {
        $this->ordem = (int) $ordem ?: 0;
    }
    public function getOrdem(): int
    {
        return $this->ordem;
    }

    /* =====================
       Detecção automática de mídia
    ===================== */
    private function detectarTipoMidia()
    {
        if (!$this->arquivo_midia) {
            $this->tipo_midia = 'nenhum';
            return;
        }

        $extensao = strtolower(pathinfo($this->arquivo_midia, PATHINFO_EXTENSION));
        $imagens = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $videos = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];

        if (in_array($extensao, $imagens)) {
            $this->tipo_midia = 'imagem';
        } elseif (in_array($extensao, $videos)) {
            $this->tipo_midia = 'video';
        } else {
            $this->tipo_midia = 'nenhum';
        }
    }

    /* =====================
       CREATE
    ===================== */
    public function inserir(): int
    {
        if ($this->ordem === 0) {
            $this->ordem = $this->proximaOrdem();
        }

        return $this->create([
            'titulo' => $this->titulo,
            'texto' => $this->texto,
            'botao_texto' => $this->botao_texto,
            'botao_link' => $this->botao_link,
            'tipo_midia' => $this->tipo_midia,
            'arquivo_midia' => $this->arquivo_midia,
            'lado' => $this->lado,
            'ativo' => $this->ativo,
            'ordem' => $this->ordem
        ]);
    }

    /* =====================
       READ
    ===================== */
    public function listarTodos(): array
    {
        return $this->read('', [], 'ordem ASC');
    }

    public function listarAtivos(): array
    {
        return $this->read('ativo = 1', [], 'ordem ASC');
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->find($id);
    }

    /* =====================
       UPDATE
    ===================== */
    public function atualizar(): bool
    {
        if (!$this->id)
            return false;

        return $this->update($this->id, [
            'titulo' => $this->titulo,
            'texto' => $this->texto,
            'botao_texto' => $this->botao_texto,
            'botao_link' => $this->botao_link,
            'tipo_midia' => $this->tipo_midia,
            'arquivo_midia' => $this->arquivo_midia,
            'lado' => $this->lado,
            'ativo' => $this->ativo,
            'ordem' => $this->ordem
        ]);
    }

    /* =====================
       DELETE
    ===================== */
    public function excluir(int $id): bool
    {
        return $this->delete($id);
    }

    /* =====================
       Próxima ordem
    ===================== */
    public function proximaOrdem(): int
    {
        $sql = "SELECT COALESCE(MAX(ordem), -1) + 1 AS prox FROM {$this->tabela}";
        $resultado = $this->conexao->query($sql);
        $linha = $resultado->fetch_assoc();
        return (int) ($linha['prox'] ?? 0);
    }
}
?>