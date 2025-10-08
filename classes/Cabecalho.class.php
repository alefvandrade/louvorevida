<?php
require_once "CRUD.class.php";

class Cabecalho
{
     private $conn;
     protected $id;
     protected $nome;
     protected $descricao;
     protected $logo;
     protected $fundo;

     public function __construct()
     {
          $this->conn = Conexao::conectar();
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
          $this->id = $id;
     }

     public function getNome()
     {
          return $this->nome;
     }
     public function setNome($nome)
     {
          $this->nome = $nome;
     }

     public function getDescricao()
     {
          return $this->descricao;
     }
     public function setDescricao($descricao)
     {
          $this->descricao = $descricao;
     }

     public function getLogo()
     {
          return $this->logo;
     }
     public function setLogo($logo)
     {
          $this->logo = $logo;
     }

     public function getFundo()
     {
          return $this->fundo;
     }
     public function setFundo($fundo)
     {
          $this->fundo = $fundo;
     }

     /* =====================
        BANCO DE DADOS
        ===================== */

     // Busca o cabeçalho atual (só existe um)
     public function buscar()
     {
          try {
               $sql = "SELECT * FROM cabecalho LIMIT 1";
               $stmt = $this->conn->query($sql);
               $dados = $stmt->fetch(PDO::FETCH_ASSOC);

               if ($dados) {
                    $this->id = $dados['id'];
                    $this->nome = $dados['nome'];
                    $this->descricao = $dados['descricao'];
                    $this->logo = $dados['logo'];
                    $this->fundo = $dados['fundo'];
               }

               return $dados ?: null;
          } catch (PDOException $e) {
               error_log("Erro ao buscar cabeçalho: " . $e->getMessage());
               return null;
          }
     }

     // Insere ou atualiza o cabeçalho
     public function salvar()
     {
          try {
               if ($this->id) {
                    $sql = "UPDATE cabecalho 
                        SET nome = :nome, descricao = :descricao, logo = :logo, fundo = :fundo 
                        WHERE id = :id";
                    $stmt = $this->conn->prepare($sql);
                    return $stmt->execute([
                         ':nome' => $this->nome,
                         ':descricao' => $this->descricao,
                         ':logo' => $this->logo,
                         ':fundo' => $this->fundo,
                         ':id' => $this->id
                    ]);
               } else {
                    $sql = "INSERT INTO cabecalho (nome, descricao, logo, fundo)
                        VALUES (:nome, :descricao, :logo, :fundo)";
                    $stmt = $this->conn->prepare($sql);
                    $ok = $stmt->execute([
                         ':nome' => $this->nome,
                         ':descricao' => $this->descricao,
                         ':logo' => $this->logo,
                         ':fundo' => $this->fundo
                    ]);

                    if ($ok) {
                         $this->id = $this->conn->lastInsertId();
                    }
                    return $ok;
               }
          } catch (PDOException $e) {
               error_log("Erro ao salvar cabeçalho: " . $e->getMessage());
               return false;
          }
     }

     // Remove o cabeçalho atual (opcional)
     public function excluir()
     {
          if (!$this->id)
               return false;

          try {
               $sql = "DELETE FROM cabecalho WHERE id = :id";
               $stmt = $this->conn->prepare($sql);
               return $stmt->execute([':id' => $this->id]);
          } catch (PDOException $e) {
               error_log("Erro ao excluir cabeçalho: " . $e->getMessage());
               return false;
          }
     }
}
?>