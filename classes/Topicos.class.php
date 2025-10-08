<?php
require_once __DIR__ . "/CRUD.class.php";

class Topicos extends CRUD
{
     protected $tabela = "topicos";
     protected $chavePrimaria = "id";
     protected $campos = [
          "titulo",
          "texto",
          "botao_texto",
          "botao_link",
          "tipo_midia",
          "arquivo_midia",
          "lado",
          "ativo",
          "ordem"
     ];

     // Atributos
     protected $id;
     protected $titulo;
     protected $texto;
     protected $botao_texto;
     protected $botao_link;
     protected $tipo_midia;
     protected $arquivo_midia;
     protected $lado;
     protected $ativo;
     protected $ordem;

     public function __construct()
     {
          parent::__construct($this->tabela);
     }

     /* =====================
        GETTERS E SETTERS
        ===================== */
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
     public function setTitulo($titulo)
     {
          $this->titulo = trim($titulo);
     }

     public function getTexto()
     {
          return $this->texto;
     }
     public function setTexto($texto)
     {
          $this->texto = trim($texto);
     }

     public function getBotaoTexto()
     {
          return $this->botao_texto;
     }
     public function setBotaoTexto($texto)
     {
          $this->botao_texto = $texto ?: null;
     }

     public function getBotaoLink()
     {
          return $this->botao_link;
     }
     public function setBotaoLink($link)
     {
          $this->botao_link = $link ?: null;
     }

     public function getTipoMidia()
     {
          return $this->tipo_midia;
     }
     public function setTipoMidia($tipo)
     {
          $permitidos = ['imagem', 'video', 'nenhum'];
          $this->tipo_midia = in_array($tipo, $permitidos) ? $tipo : 'nenhum';
     }

     public function getArquivoMidia()
     {
          return $this->arquivo_midia;
     }
     public function setArquivoMidia($arquivo)
     {
          $this->arquivo_midia = $arquivo ?: null;
          $this->detectarTipoMidia();
     }

     public function getLado()
     {
          return $this->lado;
     }
     public function setLado($lado)
     {
          $permitidos = ['esquerda', 'direita'];
          $this->lado = in_array($lado, $permitidos) ? $lado : 'direita';
     }

     public function getAtivo()
     {
          return $this->ativo;
     }
     public function setAtivo($ativo)
     {
          $this->ativo = (int) $ativo === 1 ? 1 : 0;
     }

     public function getOrdem()
     {
          return $this->ordem;
     }
     public function setOrdem($ordem)
     {
          $this->ordem = (int) $ordem;
     }

     /* =====================
        DETECÇÃO AUTOMÁTICA
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
     public function inserir(): bool
     {
          if (!isset($this->ordem))
               $this->ordem = $this->proximaOrdem();

          $sql = "INSERT INTO {$this->tabela} 
                (titulo, texto, botao_texto, botao_link, tipo_midia, arquivo_midia, lado, ativo, ordem)
                VALUES (:titulo, :texto, :botao_texto, :botao_link, :tipo_midia, :arquivo_midia, :lado, :ativo, :ordem)";
          $stmt = $this->conexao->prepare($sql);

          return $stmt->execute([
               ':titulo' => $this->titulo,
               ':texto' => $this->texto,
               ':botao_texto' => $this->botao_texto,
               ':botao_link' => $this->botao_link,
               ':tipo_midia' => $this->tipo_midia ?? 'nenhum',
               ':arquivo_midia' => $this->arquivo_midia,
               ':lado' => $this->lado ?? 'direita',
               ':ativo' => $this->ativo ?? 1,
               ':ordem' => $this->ordem
          ]);
     }

     /* =====================
        READ
        ===================== */
     public function listarTodos(): array
     {
          $sql = "SELECT * FROM {$this->tabela} ORDER BY ordem ASC";
          $stmt = $this->conexao->query($sql);
          return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
     }

     public function listarAtivos(): array
     {
          $sql = "SELECT * FROM {$this->tabela} WHERE ativo = 1 ORDER BY ordem ASC";
          $stmt = $this->conexao->query($sql);
          return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
     }

     public function buscarPorId(int $id): ?array
     {
          $sql = "SELECT * FROM {$this->tabela} WHERE id = :id LIMIT 1";
          $stmt = $this->conexao->prepare($sql);
          $stmt->execute([':id' => $id]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          return $row ?: null;
     }

     /* =====================
        UPDATE
        ===================== */
     public function atualizar(): bool
     {
          if (empty($this->id))
               return false;

          $sql = "UPDATE {$this->tabela} SET
                    titulo = :titulo,
                    texto = :texto,
                    botao_texto = :botao_texto,
                    botao_link = :botao_link,
                    tipo_midia = :tipo_midia,
                    arquivo_midia = :arquivo_midia,
                    lado = :lado,
                    ativo = :ativo,
                    ordem = :ordem
                WHERE {$this->chavePrimaria} = :id";

          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':titulo' => $this->titulo,
               ':texto' => $this->texto,
               ':botao_texto' => $this->botao_texto,
               ':botao_link' => $this->botao_link,
               ':tipo_midia' => $this->tipo_midia,
               ':arquivo_midia' => $this->arquivo_midia,
               ':lado' => $this->lado,
               ':ativo' => $this->ativo,
               ':ordem' => $this->ordem,
               ':id' => $this->id
          ]);
     }

     /* =====================
        DELETE
        ===================== */
     public function excluir(int $id): bool
     {
          $sql = "DELETE FROM {$this->tabela} WHERE {$this->chavePrimaria} = :id";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([':id' => $id]);
     }

     /* =====================
        EXTRAS
        ===================== */
     public function proximaOrdem(): int
     {
          $sql = "SELECT COALESCE(MAX(ordem), -1) + 1 AS prox FROM {$this->tabela}";
          $row = $this->conexao->query($sql)->fetch(PDO::FETCH_ASSOC);
          return isset($row['prox']) ? (int) $row['prox'] : 0;
     }
}