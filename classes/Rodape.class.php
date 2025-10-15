<?php
require_once __DIR__ . '/CRUD.class.php';

class Rodape extends CRUD
{
  public ?int $id = null;
  public ?string $tipo = null;
  public ?string $valor = null;
  public ?int $icone_id = null;
  public ?string $link = null;

  public function __construct()
  {
    parent::__construct("rodape");
    $this->setCampos(["tipo", "valor", "icone_id", "link"]);
    $this->chavePrimaria = "id";

    $this->inserirIconesPadrao();
  }

  /* ==========================================================
     ÍCONES PADRÕES E GERENCIAMENTO
  ========================================================== */
  public function inserirIconesPadrao(): void
  {
    $resultado = $this->conexao->query("SELECT COUNT(*) AS total FROM icones");
    $total = $resultado->fetch_assoc()['total'] ?? 0;

    if ($total == 0) {
      $sql = "
                INSERT INTO icones (tipo, classe, descricao) VALUES
                ('facebook', 'bi bi-facebook', 'Ícone do Facebook'),
                ('instagram', 'bi bi-instagram', 'Ícone do Instagram'),
                ('twitter', 'bi bi-twitter', 'Ícone do Twitter'),
                ('linkedin', 'bi bi-linkedin', 'Ícone do LinkedIn'),
                ('youtube', 'bi bi-youtube', 'Ícone do YouTube'),
                ('whatsapp', 'bi bi-whatsapp', 'Ícone do WhatsApp'),
                ('telefone', 'bi bi-telephone', 'Ícone de Telefone'),
                ('email', 'bi bi-envelope', 'Ícone de Email'),
                ('localizacao', 'bi bi-geo-alt', 'Ícone de Localização'),
                ('site', 'bi bi-globe', 'Ícone de Site')
            ";
      $this->conexao->query($sql);
    }
  }

  public function listarIcones(): array
  {
    return $this->readFromTable("icones", "", [], "tipo ASC");
  }

  public function adicionarIcone(string $tipo, string $classe, ?string $descricao = null): bool
  {
    $sql = "INSERT INTO icones (tipo, classe, descricao) VALUES (?, ?, ?)";
    $stmt = $this->conexao->prepare($sql);
    $stmt->bind_param("sss", $tipo, $classe, $descricao);
    return $stmt->execute();
  }

  public function editarIcone(int $id, string $tipo, string $classe, ?string $descricao = null): bool
  {
    $sql = "UPDATE icones SET tipo = ?, classe = ?, descricao = ? WHERE id = ?";
    $stmt = $this->conexao->prepare($sql);
    $stmt->bind_param("sssi", $tipo, $classe, $descricao, $id);
    return $stmt->execute();
  }

  public function excluirIcone(int $id): bool
  {
    return $this->deleteFromTable("icones", $id);
  }

  /* ==========================================================
     CRUD DO RODAPÉ
  ========================================================== */
  public function adicionar(): int
  {
    return $this->create([
      "tipo" => $this->tipo,
      "valor" => $this->valor,
      "icone_id" => $this->icone_id,
      "link" => $this->link
    ]);
  }

  public function editar(): bool
  {
    return $this->update($this->id, [
      "tipo" => $this->tipo,
      "valor" => $this->valor,
      "icone_id" => $this->icone_id,
      "link" => $this->link
    ]);
  }

  public function excluir(): bool
  {
    return $this->delete($this->id);
  }

  public function listarTodos(): array
  {
    $sql = "
            SELECT r.*, i.tipo AS icone_tipo, i.classe AS icone_classe
            FROM rodape r
            LEFT JOIN icones i ON r.icone_id = i.id
            ORDER BY r.id ASC
        ";
    $resultado = $this->conexao->query($sql);
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
  }

  public function buscarPorId(int $id): ?array
  {
    $sql = "
            SELECT r.*, i.tipo AS icone_tipo, i.classe AS icone_classe
            FROM rodape r
            LEFT JOIN icones i ON r.icone_id = i.id
            WHERE r.id = ?
            LIMIT 1
        ";
    $stmt = $this->conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($dados = $res->fetch_assoc()) {
      foreach ($dados as $chave => $valor) {
        $this->$chave = $valor;
      }
      return $dados;
    }
    return null;
  }

  public function listarAtivosComIcones(): array
  {
    $todos = $this->listarTodos();
    foreach ($todos as &$item) {
      $item['icone_html'] = !empty($item['icone_classe']) ? "<i class=\"{$item['icone_classe']}\"></i>" : "";
    }
    return $todos;
  }

  /* =============================
     Métodos auxiliares para icones
     (CRUD genérico de tabela diferente)
  ============================= */
  private function readFromTable(string $tabela, string $condicao = '', array $parametros = [], string $ordem = ''): array
  {
    $sql = "SELECT * FROM $tabela";
    if ($condicao)
      $sql .= " WHERE $condicao";
    if ($ordem)
      $sql .= " ORDER BY $ordem";

    $stmt = $this->conexao->prepare($sql);
    if (!empty($parametros)) {
      $tipos = str_repeat('s', count($parametros));
      $stmt->bind_param($tipos, ...$parametros);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
  }

  private function deleteFromTable(string $tabela, int $id): bool
  {
    $sql = "DELETE FROM $tabela WHERE id = ?";
    $stmt = $this->conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
  }
}
?>