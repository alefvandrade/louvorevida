<?php
require_once __DIR__ . '/CRUD.class.php';

class Videos extends CRUD
{
    public ?int $id = null;
    public string $titulo_video = '';
    public ?string $data_gravacao = null;
    public ?string $capa_video = null;
    public ?string $video = null;
    public int $exibir_no_index = 0;
    public string $orientacao = 'auto';
    public int $ativo = 1;

    public function __construct()
    {
        parent::__construct('videos');
        $this->setCampos([
            'titulo_video',
            'data_gravacao',
            'capa_video',
            'video',
            'exibir_no_index',
            'orientacao',
            'ativo'
        ]);
        $this->chavePrimaria = 'id';
    }

    /* ===========================
       Getters e Setters
    =========================== */
    public function setId($id)
    {
        $this->id = (int) $id ?: null;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTituloVideo($titulo)
    {
        $this->titulo_video = trim((string) $titulo);
    }
    public function getTituloVideo(): string
    {
        return $this->titulo_video;
    }

    public function setDataGravacao($data)
    {
        $this->data_gravacao = $data ?: null;
    }
    public function getDataGravacao(): ?string
    {
        return $this->data_gravacao;
    }

    public function setCapaVideo($capa)
    {
        $this->capa_video = $capa ?: null;
    }
    public function getCapaVideo(): ?string
    {
        return $this->capa_video;
    }

    public function setVideo($video)
    {
        $this->video = $video ?: null;
    }
    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setExibirNoIndex($valor)
    {
        $this->exibir_no_index = ($valor === 1) ? 1 : 0;
    }
    public function getExibirNoIndex(): int
    {
        return $this->exibir_no_index;
    }

    public function setOrientacao($orientacao)
    {
        $permitidos = ['horizontal', 'vertical', 'auto'];
        $this->orientacao = in_array($orientacao, $permitidos) ? $orientacao : 'auto';
    }
    public function getOrientacao(): string
    {
        return $this->orientacao;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = ($ativo === 1) ? 1 : 0;
    }
    public function getAtivo(): int
    {
        return $this->ativo;
    }

    /* ===========================
       CREATE
    =========================== */
    public function inserir(): int
    {
        return $this->create([
            'titulo_video' => $this->titulo_video,
            'data_gravacao' => $this->data_gravacao,
            'capa_video' => $this->capa_video,
            'video' => $this->video,
            'exibir_no_index' => $this->exibir_no_index,
            'orientacao' => $this->orientacao,
            'ativo' => $this->ativo
        ]);
    }

    /* ===========================
       READ
    =========================== */
    public function listarTodos(): array
    {
        return $this->read('', [], 'criado_em DESC');
    }

    public function listarAtivos(): array
    {
        return $this->read('ativo = 1', [], 'criado_em DESC');
    }

    public function listarIndex(): array
    {
        return $this->read('ativo = 1 AND exibir_no_index = 1', [], 'criado_em DESC');
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->find($id);
    }

    /* ===========================
       UPDATE
    =========================== */
    public function atualizar(): bool
    {
        if (!$this->id)
            return false;

        return $this->update($this->id, [
            'titulo_video' => $this->titulo_video,
            'data_gravacao' => $this->data_gravacao,
            'capa_video' => $this->capa_video,
            'video' => $this->video,
            'exibir_no_index' => $this->exibir_no_index,
            'orientacao' => $this->orientacao,
            'ativo' => $this->ativo
        ]);
    }

    /* ===========================
       DELETE
    =========================== */
    public function excluir(int $id): bool
    {
        return $this->delete($id);
    }
}
?>