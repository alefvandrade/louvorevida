import Conexao from "./Conexao.js";

/**
 * Classe genérica para operações CRUD (Create, Read, Update, Delete)
 * Sistema: Vocal Louvor & Vida
 */
export default class CRUD {
  constructor(tabela) {
    this.tabela = tabela;
    this.conexao = null;
    this.campos = [];
    this.chavePrimaria = "id";
  }

  async init() {
    // Garante conexão ativa
    if (!this.conexao) {
      this.conexao = await Conexao.conectar();
    }
  }

  setCampos(campos) {
    this.campos = campos;
  }

  /** CREATE — Inserir novo registro */
  async create(dados) {
    await this.init();
    try {
      const camposFiltrados = Object.keys(dados)
        .filter((key) => this.campos.includes(key))
        .reduce((obj, key) => {
          obj[key] = dados[key];
          return obj;
        }, {});

      const colunas = Object.keys(camposFiltrados).join(", ");
      const placeholders = Object.keys(camposFiltrados)
        .map(() => "?")
        .join(", ");
      const valores = Object.values(camposFiltrados);

      const sql = `INSERT INTO ${this.tabela} (${colunas}) VALUES (${placeholders})`;
      const [resultado] = await this.conexao.execute(sql, valores);

      return resultado.insertId;
    } catch (erro) {
      throw new Error(`Erro ao inserir em ${this.tabela}: ${erro.message}`);
    }
  }

  /** READ — Buscar registros */
  async read(condicao = "", parametros = [], ordem = "") {
    await this.init();
    try {
      let sql = `SELECT * FROM ${this.tabela}`;
      if (condicao) sql += ` WHERE ${condicao}`;
      if (ordem) sql += ` ORDER BY ${ordem}`;

      const [linhas] = await this.conexao.execute(sql, parametros);
      return linhas;
    } catch (erro) {
      throw new Error(`Erro ao ler ${this.tabela}: ${erro.message}`);
    }
  }

  /** FIND — Buscar um registro por ID */
  async find(id) {
    await this.init();
    try {
      const sql = `SELECT * FROM ${this.tabela} WHERE ${this.chavePrimaria} = ? LIMIT 1`;
      const [linhas] = await this.conexao.execute(sql, [id]);
      return linhas.length > 0 ? linhas[0] : null;
    } catch (erro) {
      throw new Error(`Erro ao buscar em ${this.tabela}: ${erro.message}`);
    }
  }

  /** UPDATE — Atualizar registro existente */
  async update(id, dados) {
    await this.init();
    try {
      const camposFiltrados = Object.keys(dados)
        .filter((key) => this.campos.includes(key))
        .reduce((obj, key) => {
          obj[key] = dados[key];
          return obj;
        }, {});

      const setClause = Object.keys(camposFiltrados)
        .map((key) => `${key} = ?`)
        .join(", ");
      const valores = [...Object.values(camposFiltrados), id];

      const sql = `UPDATE ${this.tabela} SET ${setClause} WHERE ${this.chavePrimaria} = ?`;
      const [resultado] = await this.conexao.execute(sql, valores);

      return resultado.affectedRows > 0;
    } catch (erro) {
      throw new Error(`Erro ao atualizar ${this.tabela}: ${erro.message}`);
    }
  }

  /** DELETE — Remover registro */
  async delete(id) {
    await this.init();
    try {
      const sql = `DELETE FROM ${this.tabela} WHERE ${this.chavePrimaria} = ?`;
      const [resultado] = await this.conexao.execute(sql, [id]);
      return resultado.affectedRows > 0;
    } catch (erro) {
      throw new Error(`Erro ao deletar de ${this.tabela}: ${erro.message}`);
    }
  }

  /** COUNT — Contar registros */
  async contar(ativoApenas = false) {
    await this.init();
    try {
      let sql = `SELECT COUNT(*) AS total FROM ${this.tabela}`;
      if (ativoApenas) sql += " WHERE ativo = 1";

      const [linhas] = await this.conexao.execute(sql);
      return linhas[0].total;
    } catch (erro) {
      throw new Error(`Erro ao contar registros de ${this.tabela}: ${erro.message}`);
    }
  }
}
