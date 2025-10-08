<?php
/**
 * Classe Admin
 * Gerencia autenticação e atualização do administrador
 * Herda conexão e métodos da classe CRUD
 */

require_once 'CRUD.class.php';

class Admin extends CRUD
{
    // ==========================================================
    // ATRIBUTOS PRIVADOS
    // ==========================================================
    private $id;
    private $usuario;
    private $senha;
    private $criado_em;
    private $atualizado_em;

    protected $tabela = "admin";

    // ==========================================================
    // CONSTRUTOR
    // ==========================================================
    public function __construct()
    {
        parent::__construct($this->tabela);
    }

    // ==========================================================
    // GETTERS E SETTERS
    // ==========================================================
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }
    public function setUsuario($usuario)
    {
        $this->usuario = trim($usuario);
    }

    public function getSenha()
    {
        return $this->senha;
    }
    public function setSenha($senha)
    {
        // Armazena hash diretamente para segurança
        $this->senha = password_hash($senha, PASSWORD_DEFAULT);
    }

    public function getCriadoEm()
    {
        return $this->criado_em;
    }
    public function setCriadoEm($data)
    {
        $this->criado_em = $data;
    }

    public function getAtualizadoEm()
    {
        return $this->atualizado_em;
    }
    public function setAtualizadoEm($data)
    {
        $this->atualizado_em = $data;
    }

    // ==========================================================
    // LOGIN
    // ==========================================================
    public function login($usuario, $senha)
    {
        $sql = "SELECT * FROM {$this->tabela} WHERE usuario = :usuario LIMIT 1";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($senha, $dados['senha'])) {
                $this->id = $dados['id'];
                $this->usuario = $dados['usuario'];
                $this->senha = $dados['senha'];
                $this->criado_em = $dados['criado_em'];
                $this->atualizado_em = $dados['atualizado_em'];

                $_SESSION['admin_id'] = $this->id;
                $_SESSION['admin_usuario'] = $this->usuario;

                return true;
            }
        }
        return false;
    }

    // ==========================================================
    // LOGOUT
    // ==========================================================
    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // ==========================================================
    // VERIFICA SESSÃO
    // ==========================================================
    public function verificaSessao()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: login.php");
            exit;
        }
    }

    // ==========================================================
    // ATUALIZAR USUÁRIO E SENHA
    // ==========================================================
    public function atualizar()
    {
        if (empty($this->id)) {
            throw new Exception("ID do admin não definido para atualização.");
        }

        if (!empty($this->senha)) {
            $sql = "UPDATE {$this->tabela}
                    SET usuario = :usuario, senha = :senha, atualizado_em = NOW()
                    WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':senha', $this->senha);
        } else {
            $sql = "UPDATE {$this->tabela}
                    SET usuario = :usuario, atualizado_em = NOW()
                    WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
        }

        $stmt->bindParam(':usuario', $this->usuario);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // ==========================================================
    // CARREGA DADOS DO ADMIN
    // ==========================================================
    public function carregarPorId($id)
    {
        $sql = "SELECT * FROM {$this->tabela} WHERE id = :id LIMIT 1";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $dados['id'];
            $this->usuario = $dados['usuario'];
            $this->senha = $dados['senha'];
            $this->criado_em = $dados['criado_em'];
            $this->atualizado_em = $dados['atualizado_em'];
            return true;
        }

        return false;
    }
}
?>