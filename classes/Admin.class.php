<?php
/**
 * Admin.class.php
 * Versão robusta e instrumentada para debug (mysqli)
 * Herdando CRUD.class.php
 *
 * Coloque em: classes/Admin.class.php
 * Requer: classes/Conexao.class.php e classes/CRUD.class.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/Conexao.class.php';
require_once __DIR__ . '/CRUD.class.php';

class Admin extends CRUD
{
    private $id = null;
    private $usuario = null;
    private $senha = null;
    private $criado_em = null;
    private $atualizado_em = null;

    public function __construct()
    {
        parent::__construct("admin"); // Define a tabela
        // Não chamar $this->init(), em PHP CRUD não existe
        if (!isset($this->conexao) || !$this->conexao) {
            $this->conexao = Conexao::conectar(); // garante conexão
        }
        $this->setCampos(['usuario', 'senha', 'criado_em', 'atualizado_em']);
    }

    /* ---------------------
       GETTERS / SETTERS
    --------------------- */
    public function getId()
    {
        return $this->id;
    }
    public function setId($v)
    {
        $this->id = $v;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }
    public function setUsuario($v)
    {
        $this->usuario = trim($v);
    }

    public function getSenha()
    {
        return $this->senha;
    }
    public function setSenha($plain)
    {
        $this->senha = password_hash($plain, PASSWORD_BCRYPT);
    }

    public function getCriadoEm()
    {
        return $this->criado_em;
    }
    public function setCriadoEm($v)
    {
        $this->criado_em = $v;
    }

    public function getAtualizadoEm()
    {
        return $this->atualizado_em;
    }
    public function setAtualizadoEm($v)
    {
        $this->atualizado_em = $v;
    }

    /* ---------------------
       HELPERS
    --------------------- */
    private function fetchAssocFromStmt($stmt)
    {
        if (method_exists($stmt, 'get_result')) {
            $res = $stmt->get_result();
            return $res ? $res->fetch_assoc() : null;
        }
        $meta = $stmt->result_metadata();
        if (!$meta)
            return null;
        $fields = [];
        $row = [];
        while ($field = $meta->fetch_field()) {
            $fields[] = &$row[$field->name];
        }
        call_user_func_array([$stmt, 'bind_result'], $fields);
        if ($stmt->fetch()) {
            $result = [];
            foreach ($row as $k => $v)
                $result[$k] = $v;
            return $result;
        }
        return null;
    }

    /* ---------------------
       LOGIN
    --------------------- */
    public function login($usuario, $senhaPlain)
    {
        if (!$this->conexao)
            throw new Exception("Conexão MySQL não disponível no objeto Admin.");

        $sql = "SELECT * FROM {$this->tabela} WHERE usuario = ? LIMIT 1";
        $stmt = $this->conexao->prepare($sql);
        if (!$stmt)
            throw new Exception("Prepare falhou: " . $this->conexao->error);

        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $admin = $this->fetchAssocFromStmt($stmt);
        $stmt->close();

        if (!$admin)
            return false;
        if (!password_verify($senhaPlain, $admin['senha']))
            return false;

        $this->id = (int) $admin['id'];
        $this->usuario = $admin['usuario'];
        $this->senha = $admin['senha'];
        $this->criado_em = $admin['criado_em'];
        $this->atualizado_em = $admin['atualizado_em'];

        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        $_SESSION['admin'] = ['id' => $this->id, 'usuario' => $this->usuario];

        return true;
    }

    /* ---------------------
       LOGOUT / SESSÃO
    --------------------- */
    public function logout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        unset($_SESSION['admin']);
        return true;
    }

    public function verificaSessao()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (!isset($_SESSION['admin']))
            throw new Exception("Sessão expirada ou inexistente.");
        return true;
    }

    /* ---------------------
       ATUALIZAR
    --------------------- */
    public function atualizar()
    {
        if (!$this->id)
            throw new Exception("ID do admin não definido.");

        if (!empty($this->senha)) {
            $sql = "UPDATE {$this->tabela} SET usuario=?, senha=?, atualizado_em=NOW() WHERE id=?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param('ssi', $this->usuario, $this->senha, $this->id);
        } else {
            $sql = "UPDATE {$this->tabela} SET usuario=?, atualizado_em=NOW() WHERE id=?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param('si', $this->usuario, $this->id);
        }

        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected > 0;
    }

    /* ---------------------
       CARREGAR POR ID
    --------------------- */
    public function carregarPorId($id)
    {
        if (!$this->conexao)
            throw new Exception("Conexão MySQL não disponível.");

        $sql = "SELECT * FROM {$this->tabela} WHERE id=? LIMIT 1";
        $stmt = $this->conexao->prepare($sql);
        if (is_int($id) || ctype_digit((string) $id))
            $stmt->bind_param('i', $id);
        else
            $stmt->bind_param('s', $id);

        $stmt->execute();
        $admin = $this->fetchAssocFromStmt($stmt);
        $stmt->close();

        if (!$admin)
            return false;

        $this->id = (int) $admin['id'];
        $this->usuario = $admin['usuario'];
        $this->senha = $admin['senha'];
        $this->criado_em = $admin['criado_em'];
        $this->atualizado_em = $admin['atualizado_em'];

        return true;
    }
}

/* ---------------------
   AUTO-TESTE
--------------------- */
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    session_start();
    echo "<pre>🔄 Auto-teste Admin.class.php\n\n";

    try {
        echo "PHP version: " . PHP_VERSION . "\n";
        echo "MySQLi client: " . (function_exists('mysqli_get_client_info') ? mysqli_get_client_info() : 'n/a') . "\n";

        $admin = new Admin();
        echo "✅ Instância Admin criada\n";

        echo "\n🔹 Carregar admin id=1\n";
        $ok = $admin->carregarPorId(1);
        echo $ok ? "✅ Carregado: usuario=" . $admin->getUsuario() . "\n" : "⚠️ Nenhum admin com id=1\n";

        echo "\n🔹 Teste de login (defina \$testeSenha)\n";
        $testeSenha = 'SENHA_ATUAL_DO_ADMIN';
        if ($testeSenha !== '') {
            $login = $admin->login($admin->getUsuario(), $testeSenha);
            echo $login ? "✅ Login OK\n" : "❌ Login falhou\n";
        } else {
            echo "ℹ️ Login não testado (defina \$testeSenha)\n";
        }

        echo "\n🔹 Teste de atualização (aplica sufixo _tmp e restaura)\n";
        $orig = $admin->getUsuario();
        $admin->setUsuario($orig . "_tmp");
        $changed = $admin->atualizar();
        echo $changed ? "✅ Atualizou (verifique no DB)\n" : "⚠️ Nenhuma alteração aplicada\n";
        $admin->setUsuario($orig);
        $admin->atualizar();

        $admin->logout();
        echo "\n🔹 Logout executado\n";

    } catch (Exception $e) {
        echo "❌ Exceção: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    } finally {
        try {
            Conexao::desconectar();
            echo "\n🔌 Conexão encerrada\n";
        } catch (Exception $ex) {
        }
    }

    echo "\n🎉 Fim do autoteste\n</pre>";
}
