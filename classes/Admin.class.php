<?php
/**
 * Classe Admin
 * Gerencia autenticação e atualização do administrador
 * Sistema: Vocal Louvor & Vida (versão PHP)
 */

require_once "CRUD.class.php";
require_once "Conexao.class.php";

class Admin extends CRUD {
    private $id;
    private $usuario;
    private $senha;
    private $criado_em;
    private $atualizado_em;

    private $conexao;

    public function __construct() {
        parent::__construct("admin");
        $this->conexao = Conexao::conectar();
        $this->setCampos(["usuario", "senha", "criado_em", "atualizado_em"]);
    }

    // ==========================================================
    // GETTERS e SETTERS
    // ==========================================================
    public function getId() {
        return $this->id;
    }
    public function setId($valor) {
        $this->id = $valor;
    }

    public function getUsuario() {
        return $this->usuario;
    }
    public function setUsuario($valor) {
        $this->usuario = trim($valor);
    }

    public function getSenha() {
        return $this->senha;
    }
    public function setSenha($senha) {
        // Criptografa a senha antes de armazenar
        $this->senha = password_hash($senha, PASSWORD_BCRYPT);
    }

    public function getCriadoEm() {
        return $this->criado_em;
    }
    public function setCriadoEm($data) {
        $this->criado_em = $data;
    }

    public function getAtualizadoEm() {
        return $this->atualizado_em;
    }
    public function setAtualizadoEm($data) {
        $this->atualizado_em = $data;
    }

    // ==========================================================
    // LOGIN
    // ==========================================================
    public function login($usuario, $senha) {
        try {
            $sql = "SELECT * FROM {$this->tabela} WHERE usuario = ? LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([$usuario]);

            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$admin) {
                return false;
            }

            if (!password_verify($senha, $admin["senha"])) {
                return false;
            }

            // Armazena dados do admin atual
            $this->id = $admin["id"];
            $this->usuario = $admin["usuario"];
            $this->senha = $admin["senha"];
            $this->criado_em = $admin["criado_em"];
            $this->atualizado_em = $admin["atualizado_em"];

            // Cria sessão
            $_SESSION["admin"] = [
                "id" => $this->id,
                "usuario" => $this->usuario
            ];

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro no login: " . $e->getMessage());
        }
    }

    // ==========================================================
    // LOGOUT
    // ==========================================================
    public function logout() {
        if (isset($_SESSION["admin"])) {
            unset($_SESSION["admin"]);
        }
        session_destroy();
        return true;
    }

    // ==========================================================
    // VERIFICA SESSÃO
    // ==========================================================
    public function verificaSessao() {
        if (!isset($_SESSION["admin"])) {
            throw new Exception("Sessão expirada ou inexistente. Faça login novamente.");
        }
        return true;
    }

    // ==========================================================
    // ATUALIZAR USUÁRIO E SENHA
    // ==========================================================
    public function atualizar() {
        if (!$this->id) {
            throw new Exception("ID do admin não definido para atualização.");
        }

        try {
            if (!empty($this->senha)) {
                $sql = "UPDATE {$this->tabela}
                        SET usuario = ?, senha = ?, atualizado_em = NOW()
                        WHERE id = ?";
                $params = [$this->usuario, $this->senha, $this->id];
            } else {
                $sql = "UPDATE {$this->tabela}
                        SET usuario = ?, atualizado_em = NOW()
                        WHERE id = ?";
                $params = [$this->usuario, $this->id];
            }

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar admin: " . $e->getMessage());
        }
    }

    // ==========================================================
    // CARREGA DADOS DO ADMIN
    // ==========================================================
    public function carregarPorId($id) {
        try {
            $sql = "SELECT * FROM {$this->tabela} WHERE id = ? LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([$id]);

            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$admin) {
                return false;
            }

            $this->id = $admin["id"];
            $this->usuario = $admin["usuario"];
            $this->senha = $admin["senha"];
            $this->criado_em = $admin["criado_em"];
            $this->atualizado_em = $admin["atualizado_em"];

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar admin: " . $e->getMessage());
        }
    }
}
?>
