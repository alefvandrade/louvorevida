const mysql = require('mysql2/promise');
import bcrypt from "bcrypt";
import CRUD from "./CRUD.js";

class Cabecalho extends CRUD {
     constructor() {
          super(); // Chama o construtor da classe pai
          this.conn = null;
          this.id = null;
          this.nome = null;
          this.descricao = null;
          this.logo = null;
          this.fundo = null;
     }

     async conectar() {
          if (!this.conn) {
               this.conn = await mysql.createConnection({
                    host: 'localhost',
                    password: '',
                    database: 'seu_banco_de_dados'
               });
          }
     }

     // Getters e Setters
     getId() {
          return this.id;
     }
     setId(id) {
          this.id = id;
     }

     getNome() {
          return this.nome;
     }
     setNome(nome) {
          this.nome = nome;
     }

     getDescricao() {
          return this.descricao;
     }
     async setDescricao(descricao) {
          // Hash da descrição ao definir
          const saltRounds = 10;
          this.descricao = await bcrypt.hash(descricao, saltRounds);
     }

     getLogo() {
          return this.logo;
     }
     setLogo(logo) {
          this.logo = logo;
     }

     getFundo() {
          return this.fundo;
     }
     setFundo(fundo) {
          this.fundo = fundo;
     }

     // Busca o cabeçalho atual (só existe um)
     async buscar() {
          try {
               await this.conectar();
               const [rows] = await this.conn.query('SELECT * FROM cabecalho LIMIT 1');
               if (rows.length > 0) {
                    const dados = rows[0];
                    this.id = dados.id;
                    this.nome = dados.nome;
                    this.descricao = dados.descricao; // Hash será retornado
                    this.logo = dados.logo;
                    this.fundo = dados.fundo;
                    return dados;
               }
               return null;
          } catch (error) {
               console.error('Erro ao buscar cabeçalho:', error.message);
               return null;
          }
     }

     // Insere ou atualiza o cabeçalho
     async salvar() {
          try {
               await this.conectar();
               if (this.id) {
                    const sql = `UPDATE cabecalho 
                                    SET nome = ?, descricao = ?, logo = ?, fundo = ? 
                                    WHERE id = ?`;
                    const [result] = await this.conn.execute(sql, [
                         this.nome,
                         this.descricao,
                         this.logo,
                         this.fundo,
                         this.id
                    ]);
                    return result.affectedRows > 0;
               } else {
                    const sql = `INSERT INTO cabecalho (nome, descricao, logo, fundo) 
                                    VALUES (?, ?, ?, ?)`;
                    const [result] = await this.conn.execute(sql, [
                         this.nome,
                         this.descricao,
                         this.logo,
                         this.fundo
                    ]);
                    this.id = result.insertId;
                    return result.affectedRows > 0;
               }
          } catch (error) {
               console.error('Erro ao salvar cabeçalho:', error.message);
               return false;
          }
     }

     // Remove o cabeçalho atual (opcional)
     async excluir() {
          if (!this.id) return false;

          try {
               await this.conectar();
               const sql = 'DELETE FROM cabecalho WHERE id = ?';
               const [result] = await this.conn.execute(sql, [this.id]);
               return result.affectedRows > 0;
          } catch (error) {
               console.error('Erro ao excluir cabeçalho:', error.message);
               return false;
          }
     }
}

module.exports = Cabecalho;
