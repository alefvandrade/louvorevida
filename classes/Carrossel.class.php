<?php
require_once "CRUD.class.php";

class Carrossel extends CRUD
{
     protected $tabela = "carrossel";
     protected $chavePrimaria = "id";
     protected $campos = ["titulo", "subtitulo", "fundo", "botao_texto", "botao_link", "mostrar_botao", "ordem", "ativo"];

     // Atributos do modelo
     protected $id;
     protected $titulo;
     protected $subtitulo;
     protected $fundo;
     protected $botao_texto;
     protected $botao_link;
     protected $mostrar_botao;
     protected $ordem;
     protected $ativo;

     public function __construct()
     {
          parent::__construct($this->tabela); // conexão herdada do CRUD
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

     public function getTitulo()
     {
          return $this->titulo;
     }
     public function setTitulo($t)
     {
          $this->titulo = trim($t);
     }

     public function getSubtitulo()
     {
          return $this->subtitulo;
     }
     public function setSubtitulo($s)
     {
          $this->subtitulo = trim($s);
     }

     public function getFundo()
     {
          return $this->fundo;
     }
     public function setFundo($f)
     {
          $this->fundo = $f;
     }

     public function getBotaoTexto()
     {
          return $this->botao_texto;
     }
     public function setBotaoTexto($bt)
     {
          $this->botao_texto = trim($bt);
     }

     public function getBotaoLink()
     {
          return $this->botao_link;
     }
     public function setBotaoLink($bl)
     {
          $this->botao_link = trim($bl);
     }

     public function getMostrarBotao()
     {
          return $this->mostrar_botao;
     }
     public function setMostrarBotao($mb)
     {
          $this->mostrar_botao = (int) $mb === 1 ? 1 : 0;
     }

     public function getOrdem()
     {
          return $this->ordem;
     }
     public function setOrdem($o)
     {
          $this->ordem = (int) $o;
     }

     public function getAtivo()
     {
          return $this->ativo;
     }
     public function setAtivo($a)
     {
          $this->ativo = (int) $a === 1 ? 1 : 0;
     }

     /* ===========================
        CREATE
        =========================== */
     public function inserir(): bool
     {
          // Se não informar a ordem, define automaticamente
          if (empty($this->ordem)) {
               $this->ordem = $this->proximaOrdem();
          }

          $sql = "INSERT INTO {$this->tabela} 
                    (titulo, subtitulo, fundo, botao_texto, botao_link, mostrar_botao, ordem, ativo)
                VALUES
                    (:titulo, :subtitulo, :fundo, :botao_texto, :botao_link, :mostrar_botao, :ordem, :ativo)";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':titulo' => $this->titulo,
               ':subtitulo' => $this->subtitulo,
               ':fundo' => $this->fundo,
               ':botao_texto' => $this->botao_texto,
               ':botao_link' => $this->botao_link,
               ':mostrar_botao' => $this->mostrar_botao,
               ':ordem' => $this->ordem,
               ':ativo' => $this->ativo ?? 1
          ]);
     }

     /* ===========================
        READ
        =========================== */
     public function listarTodos(): array
     {
          $sql = "SELECT * FROM {$this->tabela} ORDER BY ordem ASC, id ASC";
          $stmt = $this->conexao->query($sql);
          return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
     }

     public function listarAtivos(): array
     {
          $sql = "SELECT * FROM {$this->tabela} WHERE ativo = 1 ORDER BY ordem ASC, id ASC";
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
                    titulo = :titulo,
                    subtitulo = :subtitulo,
                    fundo = :fundo,
                    botao_texto = :botao_texto,
                    botao_link = :botao_link,
                    mostrar_botao = :mostrar_botao,
                    ordem = :ordem,
                    ativo = :ativo
                WHERE {$this->chavePrimaria} = :id";

          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':titulo' => $this->titulo,
               ':subtitulo' => $this->subtitulo,
               ':fundo' => $this->fundo,
               ':botao_texto' => $this->botao_texto,
               ':botao_link' => $this->botao_link,
               ':mostrar_botao' => $this->mostrar_botao,
               ':ordem' => $this->ordem,
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

     /* ===========================
        AUXILIAR
        =========================== */
     public function proximaOrdem(): int
     {
          $sql = "SELECT COALESCE(MAX(ordem), 0) + 1 AS prox FROM {$this->tabela}";
          $stmt = $this->conexao->query($sql);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          return $row ? (int) $row['prox'] : 1;
     }
}