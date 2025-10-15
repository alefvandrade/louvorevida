<?php
/**
 * Admin.class.php
 * Versão compatível com Conexao.class.php (mysqli) e CRUD.class.php
 */

require_once __DIR__ . "\Conexao.class.php";
require_once __DIR__ . "\CRUD.class.php";

class Admin extends CRUD {
    private $id = null;
    private $usuario = null;
    private $senha = null;
    private $criado_em = null;
    private $atualizado_em = null;

    public function __construct() {
        parent::__construct("admin"); // o CRUD já chama Conexao::conectar() no constructor
        // garante que $this->conexao está disponível (definido em CRUD)
        $this->setCampos(["usuario", "senha", "criado_em", "atualizado_em"]);
    }

    // ============================
    // GETTERS / SETTERS simples
    // ============================
    public function getId() { return $this->id; }
    public function setId($v) { $this->id = $v; }

    public function getUsuario() { return $this->usuario; }
    public function setUsuario($v) { $this->usuario = trim($v); }

    public function getSenha() { return $this->senha; }
    public function setSenha($senhaPlain) {
        $this->senha = password_hash($senhaPlain, PASSWORD_BCRYPT);
    }

    public function getCriadoEm() { return $this->criado_em; }
    public function setCriadoEm($v) { $this->criado_em = $v; }

    public function getAtualizadoEm() { return $this->atualizado_em; }
    public function setAtualizadoEm($v) { $this->atualizado_em = $v; }

    // ============================
    // Helper: fetch assoc de mysqli_stmt
    // ============================
    private function fetchAssocFromStmt($stmt) {
        // tenta get_result (requer mysqlnd)
        if (method_exists($stmt, 'get_result')) {
            $res = $stmt->get_result();
            return $res ? $res->fetch_assoc() : null;
        }

        // fallback: bind_result
        $meta = $stmt->result_metadata();
        if (!$meta) return null;
        $fields = [];
        $row = [];
        while ($field = $meta->fetch_field()) {
            $fields[] = &$row[$field->name];
        }
        call_user_func_array([$stmt, 'bind_result'], $fields);
        if ($stmt->fetch()) {
            // copie valores
            $result = [];
            foreach ($row as $k => $v) $result[$k] = $v;
            return $result;
        }
        return null;
    }

    // ============================
    // LOGIN
    // ============================
    public function login($usuario, $senhaPlain) {
        try {
            $sql = "SELECT * FROM {$this->tabela} WHERE usuario = ? LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->conexao->error);

            $stmt->bind_param('s', $usuario);
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);

            $admin = $this->fetchAssocFromStmt($stmt);
            $stmt->close();

            if (!$admin) return false;

            // verifica senha
            if (!password_verify($senhaPlain, $admin['senha'])) return false;

            // popula o objeto
            $this->id = (int)$admin['id'];
            $this->usuario = $admin['usuario'];
            $this->senha = $admin['senha'];
            $this->criado_em = $admin['criado_em'];
            $this->atualizado_em = $admin['atualizado_em'];

            // inicia sessão se não estiver
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $_SESSION['admin'] = ['id' => $this->id, 'usuario' => $this->usuario];

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro no login: " . $e->getMessage());
        }
    }

    // ============================
    // LOGOUT
    // ============================
    public function logout() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (isset($_SESSION['admin'])) unset($_SESSION['admin']);
        // não destrói sessão globalmente (opcional)
        // session_destroy();
        return true;
    }

    // ============================
    // VERIFICA SESSÃO
    // ============================
    public function verificaSessao() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['admin'])) {
            throw new Exception("Sessão expirada ou inexistente. Faça login novamente.");
        }
        return true;
    }

    // ============================
    // ATUALIZAR
    // ============================
    public function atualizar() {
        if (!$this->id) throw new Exception("ID do admin não definido para atualização.");

        try {
            if (!empty($this->senha)) {
                $sql = "UPDATE {$this->tabela} SET usuario = ?, senha = ?, atualizado_em = NOW() WHERE id = ?";
                $stmt = $this->conexao->prepare($sql);
                if (!$stmt) throw new Exception("Prepare failed: " . $this->conexao->error);
                $stmt->bind_param('ssi', $this->usuario, $this->senha, $this->id);
            } else {
                $sql = "UPDATE {$this->tabela} SET usuario = ?, atualizado_em = NOW() WHERE id = ?";
                $stmt = $this->conexao->prepare($sql);
                if (!$stmt) throw new Exception("Prepare failed: " . $this->conexao->error);
                $stmt->bind_param('si', $this->usuario, $this->id);
            }

            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $affected = $stmt->affected_rows;
            $stmt->close();

            return $affected > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar admin: " . $e->getMessage());
        }
    }

    // ============================
    // CARREGAR POR ID
    // ============================
    public function carregarPorId($id) {
        try {
            $sql = "SELECT * FROM {$this->tabela} WHERE id = ? LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->conexao->error);

            // aceita id numérico ou string, usa i quando inteiro
            if (is_int($id) || ctype_digit((string)$id)) {
                $idInt = (int)$id;
                $stmt->bind_param('i', $idInt);
            } else {
                $stmt->bind_param('s', $id);
            }

            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $admin = $this->fetchAssocFromStmt($stmt);
            $stmt->close();

            if (!$admin) return false;

            $this->id = (int)$admin['id'];
            $this->usuario = $admin['usuario'];
            $this->senha = $admin['senha'];
            $this->criado_em = $admin['criado_em'];
            $this->atualizado_em = $admin['atualizado_em'];

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao carregar admin: " . $e->getMessage());
        }
    }
}

// ============================
// AUTO-TESTE (executa só se chamado diretamente)
// ============================
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    session_start();
    echo "🔄 Testando Admin.class.php<br>";

    try {
        $admin = new Admin();
        echo "✅ Instância criada, conexão estabelecida.<br>";

        // CARREGAR
        echo "🔹 Carregando admin id=1<br>";
        $ok = $admin->carregarPorId(1);
        if ($ok) {
            echo "✅ Carregado: usuario=" . htmlspecialchars($admin->getUsuario()) . "<br>";
        } else {
            echo "⚠️ Nenhum admin com id=1 encontrado.<br>";
        }

        // LOGIN - ATENÇÃO: use a senha real do seu DB para testar
        echo "🔹 Tentando login (use a senha real do DB)...<br>";
        $testeSenha = ''; // <-- coloque a senha atual aqui para testar, e remova depois
        if ($testeSenha !== '') {
            $login = $admin->login($admin->getUsuario(), $testeSenha);
            echo $login ? "✅ Login ok<br>" : "❌ Login falhou (senha incorreta)<br>";
        } else {
            echo "ℹ️ Login não testado (defina \$testeSenha para testar).<br>";
        }

        // ATUALIZAR (teste não destrutivo)
        echo "🔹 Teste de atualização (não aplicará se nenhuma alteração)<br>";
        $usuarioAntes = $admin->getUsuario();
        $admin->setUsuario($usuarioAntes . "_teste");
        // não altera senha no teste para não quebrar login
        $atualizou = $admin->atualizar();
        echo $atualizou ? "✅ Atualizou (verifique no DB)<br>" : "⚠️ Não houve mudança (provavelmente mesmo valor)<br>";
        // restaura usuario (opcional)
        $admin->setUsuario($usuarioAntes);
        $admin->atualizar();

        // LOGOUT
        $admin->logout();
        echo "🔹 Logout executado<br>";

    } catch (Exception $e) {
        echo "❌ Erro no autoteste: " . htmlspecialchars($e->getMessage()) . "<br>";
    }

    Conexao::desconectar();
    echo "🎉 Fim do teste.<br>";
}
?>
