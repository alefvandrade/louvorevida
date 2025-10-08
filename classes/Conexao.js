import fs from "fs";
import mysql from "mysql2/promise";

export default class Conexao {
  static instancia = null;
  static config = null;

  constructor() {
    throw new Error("Esta classe não pode ser instanciada diretamente.");
  }

  static carregarConfig() {
    if (this.config === null) {
      const arquivo = "config.ini";
      if (!fs.existsSync(arquivo)) {
        console.error("❌ Arquivo de configuração 'config.ini' não encontrado!");
        process.exit(1);
      }

      // Leitura simples do config.ini
      const conteudo = fs.readFileSync(arquivo, "utf-8");
      this.config = this.parseINI(conteudo)["database"];
    }
  }

  static parseINI(texto) {
    const linhas = texto.split(/\r?\n/);
    const resultado = {};
    let secaoAtual = null;

    for (let linha of linhas) {
      linha = linha.trim();
      if (!linha || linha.startsWith(";") || linha.startsWith("#")) continue;
      if (linha.startsWith("[")) {
        secaoAtual = linha.replace(/\[|\]/g, "");
        resultado[secaoAtual] = {};
      } else if (secaoAtual) {
        const [chave, valor] = linha.split("=");
        resultado[secaoAtual][chave.trim()] = valor.trim();
      }
    }
    return resultado;
  }

  static async conectar() {
    this.carregarConfig();

    const { host, dbname, user, password, charset } = this.config;

    try {
      if (this.instancia === null) {
        this.instancia = await mysql.createConnection({
          host,
          user,
          password,
          database: dbname,
          charset,
        });

        console.log(`✅ Conexão com o banco de dados '${dbname}' realizada com sucesso!`);
      }
      return this.instancia;
    } catch (erro) {
      console.error(`❌ Erro ao conectar ao banco de dados: ${erro.message}`);
      process.exit(1);
    }
  }

  static async desconectar() {
    if (this.instancia) {
      await this.instancia.end();
      this.instancia = null;
      console.log("🔌 Conexão encerrada com sucesso.");
    }
  }
}

// Teste rápido se rodar diretamente o arquivo
if (import.meta.url === `file://${process.argv[1]}`) {
  (async () => {
    const conexao = await Conexao.conectar();
    await Conexao.desconectar();
  })();
}
