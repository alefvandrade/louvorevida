import bcrypt from "bcrypt";
import CRUD from "./CRUD.js";

class Carrossel extends CRUD {
     constructor(dbConnection) {
          super(); // Adicionei super() para chamar o construtor da classe pai
          this.tabela = "carrossel";
          this.chavePrimaria = "id";
          this.campos = ["titulo", "subtitulo", "fundo", "botao_texto", "botao_link", "mostrar_botao", "ordem", "ativo"];
          this.conexao = dbConnection;

          // Atributos do modelo
          this.id = null;
          this.titulo = null;
          this.subtitulo = null;
          this.fundo = null;
          this.botao_texto = null;
          this.botao_link = null;
          this.mostrar_botao = null;
          this.ordem = null;
          this.ativo = null;
     }

     async hashTitulo() {
          if (this.titulo) {
               const saltRounds = 10;
               this.titulo = await bcrypt.hash(this.titulo, saltRounds);
          }
     }

     // CREATE
     async inserir() {
          await this.hashTitulo(); // Hash do título antes de inserir
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
          const [result] = await this.conexao.execute(sql, values);
          return result.affectedRows > 0;
     }

     // UPDATE
     async atualizar() {
          if (!this.id) return false;

          await this.hashTitulo(); // Hash do título antes de atualizar

          const sql = `
               UPDATE ${this.tabela} SET
               titulo = ?, subtitulo = ?, fundo = ?, botao_texto = ?, botao_link = ?, 
               mostrar_botao = ?, ordem = ?, ativo = ?
               WHERE ${this.chavePrimaria} = ?
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
          const [result] = await this.conexao.execute(sql, values);
          return result.affectedRows > 0;
     }
}
