<?php
require_once __DIR__ . '/Conexao.php';

/**
 * Classe genérica para operações CRUD (Create, Read, Update, Delete)
 * Sistema: Vocal Louvor & Vida (versão PHP)
 */
class CRUD {
    protected string $tabela;
    protected mysqli $conexao;
    protected array $campos = [];
    protected string $chavePrimaria = 'id';

    public function __construct(string $tabela) {
        $this->tabela = $tabela;
        $this->conexao = Conexao::conectar();
    }

    /** Define os campos válidos do CRUD */
    public function setCampos(array $campos): void {
        $this->campos = $campos;
    }

    /** Executa uma query SQL genérica */
    public function executar(string $sql, array $params = []): mixed {
        $stmt = $this->conexao->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar SQL: " . $this->conexao->error);
        }

        if (!empty($params)) {
            $tipos = str_repeat('s', count($params));
            $stmt->bind_param($tipos, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar SQL: " . $stmt->error);
        }

        $resultado = $stmt->get_result();
        if ($resultado !== false) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }

        return [
            'affectedRows' => $stmt->affected_rows,
            'insertId' => $stmt->insert_id
        ];
    }

    /** CREATE — Inserir novo registro */
    public function create(array $dados): int {
        $camposFiltrados = array_intersect_key($dados, array_flip($this->campos));

        $colunas = implode(', ', array_keys($camposFiltrados));
        $placeholders = implode(', ', array_fill(0, count($camposFiltrados), '?'));
        $valores = array_values($camposFiltrados);

        $sql = "INSERT INTO {$this->tabela} ($colunas) VALUES ($placeholders)";
        $stmt = $this->conexao->prepare($sql);
        $tipos = str_repeat('s', count($valores));
        $stmt->bind_param($tipos, ...$valores);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao inserir em {$this->tabela}: " . $stmt->error);
        }

        return $stmt->insert_id;
    }

    /** READ — Buscar registros */
    public function read(string $condicao = '', array $parametros = [], string $ordem = ''): array {
        $sql = "SELECT * FROM {$this->tabela}";
        if ($condicao) $sql .= " WHERE $condicao";
        if ($ordem) $sql .= " ORDER BY $ordem";

        $stmt = $this->conexao->prepare($sql);
        if (!empty($parametros)) {
            $tipos = str_repeat('s', count($parametros));
            $stmt->bind_param($tipos, ...$parametros);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** FIND — Buscar um registro por ID */
    public function find(int|string $id): ?array {
        $sql = "SELECT * FROM {$this->tabela} WHERE {$this->chavePrimaria} = ? LIMIT 1";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado && $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /** UPDATE — Atualizar registro existente */
    public function update(int|string $id, array $dados): bool {
        $camposFiltrados = array_intersect_key($dados, array_flip($this->campos));

        $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($camposFiltrados)));
        $valores = array_values($camposFiltrados);
        $valores[] = $id;

        $sql = "UPDATE {$this->tabela} SET $setClause WHERE {$this->chavePrimaria} = ?";
        $stmt = $this->conexao->prepare($sql);
        $tipos = str_repeat('s', count($valores));
        $stmt->bind_param($tipos, ...$valores);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar {$this->tabela}: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }

    /** DELETE — Remover registro */
    public function delete(int|string $id): bool {
        $sql = "DELETE FROM {$this->tabela} WHERE {$this->chavePrimaria} = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param('s', $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    /** COUNT — Contar registros */
    public function contar(bool $ativoApenas = false): int {
        $sql = "SELECT COUNT(*) AS total FROM {$this->tabela}";
        if ($ativoApenas) $sql .= " WHERE ativo = 1";

        $resultado = $this->conexao->query($sql);
        $linha = $resultado->fetch_assoc();

        return (int) $linha['total'];
    }
}

// ============================================================
// TESTE AUTOMÁTICO — Executado se rodar diretamente o arquivo
// ============================================================
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"])) {
    echo "🔄 Testando CRUD genérico...<br>";

    $crud = new CRUD('admin');
    $crud->setCampos(['usuario', 'senha', 'criado_em', 'atualizado_em']);

    try {
        echo "📋 Total de admins: " . $crud->contar() . "<br>";
        $admins = $crud->read();
        echo "✅ Admins carregados:<pre>" . print_r($admins, true) . "</pre>";
    } catch (Exception $e) {
        echo "❌ Erro no teste: " . $e->getMessage();
    }

    Conexao::desconectar();
}
?>
