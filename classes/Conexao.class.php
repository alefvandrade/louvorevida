<?php
/**
 * Classe Conexao
 * Gerencia conexão com o banco MySQL (local + Infinity)
 */

class Conexao {
    private static ?mysqli $instancia = null;
    private static ?array $configLocal = null;
    private static ?array $configInfinity = null;

    private function __construct() {}

    private static function carregarConfig(): void {
        if (self::$configLocal === null || self::$configInfinity === null) {
            $arquivo = __DIR__ . "/../config.ini";
            if (!file_exists($arquivo)) {
                die("❌ Arquivo de configuração 'config.ini' não encontrado em: $arquivo");
            }
            $ini = parse_ini_file($arquivo, true);
            self::$configLocal = $ini['local'] ?? null;
            self::$configInfinity = $ini['infinity'] ?? null;

            if (!self::$configLocal || !self::$configInfinity) {
                die("❌ Configurações 'local' e 'infinity' devem existir no config.ini");
            }
        }
    }

    public static function conectar(): mysqli {
        self::carregarConfig();

        if (self::$instancia !== null) {
            return self::$instancia;
        }

        // Tenta conectar no banco local
        $cfg = self::$configLocal;
        $mysqli = @new mysqli($cfg['host'], $cfg['username'], $cfg['password'], $cfg['dbname'], intval($cfg['port']));
        if ($mysqli->connect_errno) {
            // Se falhar, tenta conectar no banco Infinity
            $cfg = self::$configInfinity;
            $mysqli = @new mysqli($cfg['host'], $cfg['username'], $cfg['password'], $cfg['dbname'], intval($cfg['port']));
            if ($mysqli->connect_errno) {
                die("❌ Não foi possível conectar a nenhum banco de dados. Erro: " . $mysqli->connect_error);
            } else {
                echo "✅ Conectado ao banco Infinity ({$cfg['dbname']}) com sucesso!<br>";
            }
        } else {
            echo "✅ Conectado ao banco local ({$cfg['dbname']}) com sucesso!<br>";
        }

        $mysqli->set_charset($cfg['charset']);
        self::$instancia = $mysqli;
        return self::$instancia;
    }

    public static function desconectar(): void {
        if (self::$instancia !== null) {
            self::$instancia->close();
            self::$instancia = null;
            echo "🔌 Conexão encerrada com sucesso.<br>";
        }
    }

    public static function listarTabelas(): void {
        $conn = self::conectar();
        $resultado = $conn->query("SHOW TABLES");
        if ($resultado) {
            echo "📋 Tabelas encontradas:<br>";
            while ($linha = $resultado->fetch_array()) {
                echo " - " . $linha[0] . "<br>";
            }
        } else {
            echo "⚠️ Nenhuma tabela encontrada ou erro na consulta.<br>";
        }
    }
}

// Teste automático
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"])) {
    echo "🔄 Testando conexão...<br>";
    Conexao::listarTabelas();
    Conexao::desconectar();
}
?>
