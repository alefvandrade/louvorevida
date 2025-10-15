<?php
require_once __DIR__ . '/Conexao.class.php';

class Cabecalho
{
  private mysqli $conn;
  private ?int $id = null;
  private ?string $titulo = null;
  private ?string $subtitulo = null;
  private ?string $logo = null;
  private ?string $fundo = null;

  public function __construct()
  {
    $this->conn = Conexao::conectar();
  }

  /* =======================
     GETTERS e SETTERS
  ======================= */
  public function getId(): ?int
  {
    return $this->id;
  }
  public function setId(int $id)
  {
    $this->id = $id;
  }

  public function getTitulo(): ?string
  {
    return $this->titulo;
  }
  public function setTitulo(string $titulo)
  {
    $this->titulo = $titulo;
  }

  public function getSubtitulo(): ?string
  {
    return $this->subtitulo;
  }
  public function setSubtitulo(string $subtitulo)
  {
    $this->subtitulo = $subtitulo;
  }

  public function getLogo(): ?string
  {
    return $this->logo;
  }
  public function setLogo(?string $logo)
  {
    $this->logo = $logo;
  }

  public function getFundo(): ?string
  {
    return $this->fundo;
  }
  public function setFundo(?string $fundo)
  {
    $this->fundo = $fundo;
  }

  /* =======================
     BANCO DE DADOS
  ======================= */

  // Buscar cabeçalho (só existe 1)
  public function buscar(): ?array
  {
    $sql = "SELECT * FROM cabecalho ORDER BY id DESC LIMIT 1";
    $result = $this->conn->query($sql);

    if ($result && $dados = $result->fetch_assoc()) {
      $this->id = (int) $dados['id'];
      $this->titulo = $dados['titulo'];
      $this->subtitulo = $dados['subtitulo'];
      $this->logo = $dados['logo'];
      $this->fundo = $dados['fundo'];
      return $dados;
    }
    return null;
  }

  // Inserir ou atualizar
  public function salvar(): bool
  {
    if ($this->id) {
      // Atualizar
      $stmt = $this->conn->prepare("
                UPDATE cabecalho 
                SET titulo = ?, subtitulo = ?, logo = ?, fundo = ?, atualizado_em = NOW()
                WHERE id = ?
            ");
      $stmt->bind_param("ssssi", $this->titulo, $this->subtitulo, $this->logo, $this->fundo, $this->id);
      return $stmt->execute();
    } else {
      // Inserir
      $stmt = $this->conn->prepare("
                INSERT INTO cabecalho (titulo, subtitulo, logo, fundo, criado_em, atualizado_em) 
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");
      $stmt->bind_param("ssss", $this->titulo, $this->subtitulo, $this->logo, $this->fundo);
      $ok = $stmt->execute();
      if ($ok) {
        $this->id = $stmt->insert_id;
      }
      return $ok;
    }
  }
}
?>