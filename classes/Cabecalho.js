import CRUD from "./CRUD.js";

export default class Cabecalho extends CRUD {
  constructor() {
    super("cabecalho");
    this.id = null;
    this.nome = null;
    this.descricao = null;
    this.logo = null;
    this.fundo = null;
  }

  async buscar() {
    const sql = `SELECT * FROM ${this.tabela} LIMIT 1`;
    const result = await this.executar(sql);
    if (result.length > 0) {
      Object.assign(this, result[0]);
      return result[0];
    }
    return null;
  }

  async salvar() {
    if (this.id) {
      const sql = `
        UPDATE ${this.tabela}
        SET nome = ?, descricao = ?, logo = ?, fundo = ?
        WHERE id = ?
      `;
      return await this.executar(sql, [
        this.nome,
        this.descricao,
        this.logo,
        this.fundo,
        this.id
      ]);
    } else {
      const sql = `
        INSERT INTO ${this.tabela} (nome, descricao, logo, fundo)
        VALUES (?, ?, ?, ?)
      `;
      const result = await this.executar(sql, [
        this.nome,
        this.descricao,
        this.logo,
        this.fundo
      ]);
      this.id = result.insertId;
      return result;
    }
  }

  async excluir() {
    if (!this.id) return false;
    const sql = `DELETE FROM ${this.tabela} WHERE id = ?`;
    return await this.executar(sql, [this.id]);
  }
}
