<?php
/**
 * Classe Conexao
 * Gerencia conexão com o banco MySQL
 * Sistema: Vocal Louvor & Vida (versão PHP)
 */

class Conexao {
    private static ?mysqli $instancia = null;
    private static ?array $config = null;

    private function __construct() {
        // Evita instanciar diretamente
    }

    /**
     * Carrega as configurações do arquivo config.ini
     */
    private static function carregarConfig(): void {
        if (self::$config === null) {
            $arquivo = __DIR__ . "/../config.ini";
            if (!file_exists($arquivo)) {
                die("❌ Arquivo de configuração 'config.ini' não encontrado em: $arquivo");
            }

            self::$config = parse_ini_file($arquivo, true)['database'];
        }
    }

    /**
     * Conecta ao banco de dados (singleton)
     */
    public static function conectar(): mysqli {
        self::carregarConfig();

        $host = trim(self::$config['host'], '"');
        $user = trim(self::$config['username'], '"');
        $password = trim(self::$config['password'], '"');
        $dbname = trim(self::$config['dbname'], '"');
        $port = intval(self::$config['port']);
        $charset = trim(self::$config['charset'], '"');

        if (self::$instancia === null) {
            self::$instancia = new mysqli($host, $user, $password, $dbname, $port);

            if (self::$instancia->connect_error) {
                die("❌ Erro ao conectar ao banco de dados: " . self::$instancia->connect_error);
            }

            self::$instancia->set_charset($charset);
            echo "✅ Conexão com o banco de dados '{$dbname}' realizada com sucesso!<br>";
        }

        return self::$instancia;
    }

    /**
     * Desconecta do banco de dados
     */
    public static function desconectar(): void {
        if (self::$instancia !== null) {
            self::$instancia->close();
            self::$instancia = null;
            echo "🔌 Conexão encerrada com sucesso.<br>";
        }
    }

    /**
     * Mostra as tabelas existentes (teste)
     */
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

// ============================================================
// TESTE AUTOMÁTICO — Executado se rodar diretamente o arquivo
// ============================================================
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"])) {
    echo "🔄 Testando conexão...<br>";
    Conexao::listarTabelas();
    Conexao::desconectar();
}
?>
