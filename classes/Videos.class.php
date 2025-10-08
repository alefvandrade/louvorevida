<?php
require_once "CRUD.class.php";

class Videos extends CRUD
{
     protected $tabela = "videos";
     protected $chavePrimaria = "id";
     protected $campos = [
          "titulo_video",
          "data_gravacao",
          "capa_video",
          "video",
          "exibir_no_index",
          "orientacao",
          "ativo"
     ];

     // Atributos
     protected $id;
     protected $titulo_video;
     protected $data_gravacao;
     protected $capa_video;
     protected $video;
     protected $exibir_no_index;
     protected $orientacao;
     protected $ativo;

     public function __construct()
     {
          parent::__construct($this->tabela);
     }

     /* ===========================
        GETTERS e SETTERS
        =========================== */
     public function getId()
     {
          return $this->id;
     }
     public function setId($id)
     {
          $this->id = (int) $id;
     }

     public function getTituloVideo()
     {
          return $this->titulo_video;
     }
     public function setTituloVideo($titulo)
     {
          $this->titulo_video = trim($titulo);
     }

     public function getDataGravacao()
     {
          return $this->data_gravacao;
     }
     public function setDataGravacao($data)
     {
          $this->data_gravacao = $data ?: null;
     }

     public function getCapaVideo()
     {
          return $this->capa_video;
     }
     public function setCapaVideo($capa)
     {
          $this->capa_video = $capa ?: null;
     }

     public function getVideo()
     {
          return $this->video;
     }
     public function setVideo($video)
     {
          $this->video = $video ?: null;
     }

     public function getExibirNoIndex()
     {
          return $this->exibir_no_index;
     }
     public function setExibirNoIndex($valor)
     {
          $this->exibir_no_index = (int) $valor === 1 ? 1 : 0;
     }

     public function getOrientacao()
     {
          return $this->orientacao;
     }
     public function setOrientacao($orientacao)
     {
          $permitidos = ['horizontal', 'vertical', 'auto'];
          $this->orientacao = in_array($orientacao, $permitidos) ? $orientacao : 'auto';
     }

     public function getAtivo()
     {
          return $this->ativo;
     }
     public function setAtivo($ativo)
     {
          $this->ativo = (int) $ativo === 1 ? 1 : 0;
     }

     /* ===========================
        CREATE
        =========================== */
     public function inserir(): bool
     {
          $sql = "INSERT INTO {$this->tabela} 
                (titulo_video, data_gravacao, capa_video, video, exibir_no_index, orientacao, ativo)
                VALUES (:titulo_video, :data_gravacao, :capa_video, :video, :exibir_no_index, :orientacao, :ativo)";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':titulo_video' => $this->titulo_video,
               ':data_gravacao' => $this->data_gravacao,
               ':capa_video' => $this->capa_video,
               ':video' => $this->video,
               ':exibir_no_index' => $this->exibir_no_index ?? 0,
               ':orientacao' => $this->orientacao ?? 'auto',
               ':ativo' => $this->ativo ?? 1
          ]);
     }

     /* ===========================
        READ
        =========================== */
     public function listarTodos(): array
     {
          $sql = "SELECT * FROM {$this->tabela} ORDER BY criado_em DESC";
          $stmt = $this->conexao->query($sql);
          return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
     }

     public function listarAtivos(): array
     {
          $sql = "SELECT * FROM {$this->tabela} WHERE ativo = 1 ORDER BY criado_em DESC";
          $stmt = $this->conexao->query($sql);
          return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
     }

     public function listarIndex(): array
     {
          $sql = "SELECT * FROM {$this->tabela} 
                WHERE ativo = 1 AND exibir_no_index = 1 
                ORDER BY criado_em DESC";
          $stmt = $this->conexao->query($sql);
          return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
     }

     public function buscarPorId(int $id): ?array
     {
          $sql = "SELECT * FROM {$this->tabela} WHERE {$this->chavePrimaria} = :id LIMIT 1";
          $stmt = $this->conexao->prepare($sql);
          $stmt->execute([':id' => $id]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          return $row ?: null;
     }

     /* ===========================
        UPDATE
        =========================== */
     public function atualizar(): bool
     {
          if (empty($this->id))
               return false;

          $sql = "UPDATE {$this->tabela} SET
                    titulo_video = :titulo_video,
                    data_gravacao = :data_gravacao,
                    capa_video = :capa_video,
                    video = :video,
                    exibir_no_index = :exibir_no_index,
                    orientacao = :orientacao,
                    ativo = :ativo
                WHERE {$this->chavePrimaria} = :id";

          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':titulo_video' => $this->titulo_video,
               ':data_gravacao' => $this->data_gravacao,
               ':capa_video' => $this->capa_video,
               ':video' => $this->video,
               ':exibir_no_index' => $this->exibir_no_index,
               ':orientacao' => $this->orientacao,
               ':ativo' => $this->ativo,
               ':id' => $this->id
          ]);
     }

     /* ===========================
        DELETE
        =========================== */
     public function excluir(int $id): bool
     {
          $sql = "DELETE FROM {$this->tabela} WHERE {$this->chavePrimaria} = :id";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([':id' => $id]);
     }
}
?>