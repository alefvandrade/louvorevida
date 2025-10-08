<?php
/**
 * Classe genérica para operações CRUD (Create, Read, Update, Delete)
 * Sistema: Vocal Louvor & Vida
 */

require_once 'Conexao.class.php';

class CRUD
{
    protected $conexao;
    protected $tabela;
    protected $campos = [];
    protected $chavePrimaria = 'id';

    /**
     * Construtor — conecta e define a tabela
     */
    public function __construct($tabela)
    {
        $this->conexao = Conexao::conectar();
        $this->tabela = $tabela;
    }

    /**
     * Define os campos permitidos para inserir/atualizar
     */
    public function setCampos(array $campos)
    {
        $this->campos = $campos;
    }

    /**
     * Cria (INSERT) um novo registro
     */
    public function create(array $dados)
    {
        try {
            $campos = array_intersect_key($dados, array_flip($this->campos));
            $colunas = implode(", ", array_keys($campos));
            $placeholders = ":" . implode(", :", array_keys($campos));

            $sql = "INSERT INTO {$this->tabela} ($colunas) VALUES ($placeholders)";
            $stmt = $this->conexao->prepare($sql);

            foreach ($campos as $chave => $valor) {
                $stmt->bindValue(":$chave", $valor);
            }

            $stmt->execute();
            return $this->conexao->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao inserir em {$this->tabela}: " . $e->getMessage());
        }
    }

    /**
     * Lê (SELECT) registros — com filtro opcional
     */
    public function read($condicao = '', $parametros = [], $ordem = '')
    {
        try {
            $sql = "SELECT * FROM {$this->tabela}";
            if ($condicao) $sql .= " WHERE $condicao";
            if ($ordem) $sql .= " ORDER BY $ordem";

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($parametros);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao ler {$this->tabela}: " . $e->getMessage());
        }
    }

    /**
     * Busca um único registro por ID
     */
    public function find($id)
    {
        try {
            $sql = "SELECT * FROM {$this->tabela} WHERE {$this->chavePrimaria} = :id LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar registro em {$this->tabela}: " . $e->getMessage());
        }
    }

    /**
     * Atualiza (UPDATE) um registro existente
     */
    public function update($id, array $dados)
    {
        try {
            $campos = array_intersect_key($dados, array_flip($this->campos));
            $set = implode(", ", array_map(fn($c) => "$c = :$c", array_keys($campos)));

            $sql = "UPDATE {$this->tabela} SET $set WHERE {$this->chavePrimaria} = :id";
            $stmt = $this->conexao->prepare($sql);

            foreach ($campos as $chave => $valor) {
                $stmt->bindValue(":$chave", $valor);
            }

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar {$this->tabela}: " . $e->getMessage());
        }
    }

    /**
     * Deleta (DELETE) um registro
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM {$this->tabela} WHERE {$this->chavePrimaria} = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao deletar de {$this->tabela}: " . $e->getMessage());
        }
    }

    /**
     * Contar registros ativos ou todos
     */
    public function contar($ativoApenas = false)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM {$this->tabela}";
            if ($ativoApenas) $sql .= " WHERE ativo = 1";

            $stmt = $this->conexao->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
        } catch (PDOException $e) {
            throw new Exception("Erro ao contar registros: " . $e->getMessage());
        }
    }
}

