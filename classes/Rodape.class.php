<?php
require_once __DIR__ . "/CRUD.class.php";

class Rodape extends CRUD
{
     protected $tabela = 'rodape';
     protected $campos = ['tipo', 'valor', 'icone_id', 'link'];
     protected $chavePrimaria = 'id';
     protected $id;
     protected $tipo;
     protected $valor;
     protected $icone_id;
     protected $link;

     public function __construct()
     {
          parent::__construct($this->tabela);
          $this->inserirIconesPadrao(); // 🔥 garante que os ícones existam
     }

     /* ==========================================================
        ÍCONES PADRÕES E GERENCIAMENTO INTERNO
     ========================================================== */
     private function inserirIconesPadrao()
     {
          $sql = "SELECT COUNT(*) as total FROM icones";
          $stmt = $this->conexao->query($sql);
          $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

          if ($total == 0) {
               $sqlInsert = "
                INSERT INTO icones (tipo, classe, descricao) VALUES
                ('facebook', 'bi bi-facebook', 'Ícone do Facebook'),
                ('instagram', 'bi bi-instagram', 'Ícone do Instagram'),
                ('twitter', 'bi bi-twitter', 'Ícone do Twitter'),
                ('linkedin', 'bi bi-linkedin', 'Ícone do LinkedIn'),
                ('youtube', 'bi bi-youtube', 'Ícone do YouTube'),
                ('whatsapp', 'bi bi-whatsapp', 'Ícone do WhatsApp'),
                ('telefone', 'bi bi-telephone', 'Ícone de Telefone'),
                ('email', 'bi bi-envelope', 'Ícone de Email'),
                ('localizacao', 'bi bi-geo-alt', 'Ícone de Localização/Endereço'),
                ('site', 'bi bi-globe', 'Ícone de Site/Globo');
            ";
               $this->conexao->exec($sqlInsert);
          }
     }

     public function listarIcones()
     {
          $sql = "SELECT * FROM icones ORDER BY tipo ASC";
          $stmt = $this->conexao->query($sql);
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

     public function adicionarIcone($tipo, $classe, $descricao = null)
     {
          $sql = "INSERT INTO icones (tipo, classe, descricao) VALUES (:tipo, :classe, :descricao)";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':tipo' => $tipo,
               ':classe' => $classe,
               ':descricao' => $descricao
          ]);
     }

     public function editarIcone($id, $tipo, $classe, $descricao = null)
     {
          $sql = "UPDATE icones 
                SET tipo = :tipo, classe = :classe, descricao = :descricao
                WHERE id = :id";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':tipo' => $tipo,
               ':classe' => $classe,
               ':descricao' => $descricao,
               ':id' => $id
          ]);
     }

     public function excluirIcone($id)
     {
          $sql = "DELETE FROM icones WHERE id = :id";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([':id' => $id]);
     }

     /* ==========================================================
        GETTERS E SETTERS
     ========================================================== */
     public function getId()
     {
          return $this->id;
     }

     public function getTipo()
     {
          return $this->tipo;
     }

     public function setTipo($tipo)
     {
          $this->tipo = $tipo;
     }

     public function getValor()
     {
          return $this->valor;
     }

     public function setValor($valor)
     {
          $this->valor = $valor;
     }

     public function getIconeId()
     {
          return $this->icone_id;
     }

     public function setIconeId($icone_id)
     {
          $this->icone_id = $icone_id;
     }

     public function getLink()
     {
          return $this->link;
     }

     public function setLink($link)
     {
          $this->link = $link;
     }

     /* ==========================================================
        CRUD COMPLETO DO RODAPÉ
     ========================================================== */

     // CREATE
     public function adicionar()
     {
          $sql = "INSERT INTO {$this->tabela} (tipo, valor, icone_id, link)
                VALUES (:tipo, :valor, :icone_id, :link)";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':tipo' => $this->tipo,
               ':valor' => $this->valor,
               ':icone_id' => $this->icone_id,
               ':link' => $this->link
          ]);
     }

     // UPDATE
     public function editar()
     {
          $sql = "UPDATE {$this->tabela} 
                SET tipo = :tipo, valor = :valor, icone_id = :icone_id, link = :link
                WHERE id = :id";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([
               ':tipo' => $this->tipo,
               ':valor' => $this->valor,
               ':icone_id' => $this->icone_id,
               ':link' => $this->link,
               ':id' => $this->id
          ]);
     }

     // DELETE
     public function excluir()
     {
          $sql = "DELETE FROM {$this->tabela} WHERE id = :id";
          $stmt = $this->conexao->prepare($sql);
          return $stmt->execute([':id' => $this->id]);
     }

     // READ - listar todos os itens com ícones
     public function listarTodos()
     {
          $sql = "SELECT r.*, i.tipo AS icone_tipo, i.classe AS icone_classe
                FROM {$this->tabela} r
                LEFT JOIN icones i ON r.icone_id = i.id
                ORDER BY r.id ASC";
          $stmt = $this->conexao->query($sql);
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

     // READ - buscar um por ID
     public function buscarPorId($id)
     {
          $sql = "SELECT r.*, i.tipo AS icone_tipo, i.classe AS icone_classe
                FROM {$this->tabela} r
                LEFT JOIN icones i ON r.icone_id = i.id
                WHERE r.id = :id";
          $stmt = $this->conexao->prepare($sql);
          $stmt->execute([':id' => $id]);
          $dados = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($dados) {
               $this->id = $dados['id'];
               $this->tipo = $dados['tipo'];
               $this->valor = $dados['valor'];
               $this->icone_id = $dados['icone_id'];
               $this->link = $dados['link'];
          }

          return $dados;
     }

     // READ - lista para exibição pública (index.php)
     public function listarAtivosComIcones()
     {
          $sql = "SELECT r.*, i.tipo AS icone_tipo, i.classe AS icone_classe
                FROM {$this->tabela} r
                LEFT JOIN icones i ON r.icone_id = i.id
                ORDER BY r.id ASC";
          $stmt = $this->conexao->query($sql);
          $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

          foreach ($dados as &$item) {
               $item['icone_html'] = !empty($item['icone_classe'])
                    ? '<i class="' . htmlspecialchars($item['icone_classe']) . '"></i>'
                    : '';
          }

          return $dados;
     }
}
?>