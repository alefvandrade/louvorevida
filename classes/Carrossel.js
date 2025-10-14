import CRUD from "./CRUD.js";

export default class Carrossel extends CRUD {
  constructor() {
    super("carrossel");
    this.campos = [
      "titulo",
      "subtitulo",
      "fundo",
      "botao_texto",
      "botao_link",
      "mostrar_botao",
      "ordem",
      "ativo"
    ];
    this.chavePrimaria = "id";
  }

  async proximaOrdem() {
    const sql = `SELECT MAX(ordem) AS max FROM ${this.tabela}`;
    const [result] = await this.executar(sql);
    return (result?.max || 0) + 1;
  }

  async inserir() {
    if (!this.ordem) {
      this.ordem = await this.proximaOrdem();
    }

    const sql = `
      INSERT INTO ${this.tabela} 
      (titulo, subtitulo, fundo, botao_texto, botao_link, mostrar_botao, ordem, ativo)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `;
    const values = [
      this.titulo,
      this.subtitulo,
      this.fundo,
      this.botao_texto,
      this.botao_link,
      this.mostrar_botao,
      this.ordem,
      this.ativo ?? 1
    ];
    return await this.executar(sql, values);
  }

  async atualizar() {
    const sql = `
      UPDATE ${this.tabela} SET
      titulo = ?, subtitulo = ?, fundo = ?, botao_texto = ?, botao_link = ?, 
      mostrar_botao = ?, ordem = ?, ativo = ?
      WHERE id = ?
    `;
    const values = [
      this.titulo,
      this.subtitulo,
      this.fundo,
      this.botao_texto,
      this.botao_link,
      this.mostrar_botao,
      this.ordem,
      this.ativo,
      this.id
    ];
    return await this.executar(sql, values);
  }
}
