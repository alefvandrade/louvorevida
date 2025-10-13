const bcrypt = require('bcrypt');
const CRUD = require('./CRUD');

class Rodape extends CRUD {
     constructor() {
          super('rodape');
          this.tabela = 'rodape';
          this.campos = ['tipo', 'valor', 'icone_id', 'link'];
          this.chavePrimaria = 'id';
          this.id = null;
          this.tipo = null;
          this.valor = null;
          this.icone_id = null;
          this.link = null;

          this.inserirIconesPadrao(); // 🔥 garante que os ícones existam
     }

     /* ==========================================================
        ÍCONES PADRÕES E GERENCIAMENTO INTERNO
     ========================================================== */
     async inserirIconesPadrao() {
          const sql = "SELECT COUNT(*) as total FROM icones";
          const result = await this.query(sql);
          const total = result[0].total;

          if (total === 0) {
               const sqlInsert = `
                    INSERT INTO icones (tipo, classe, descricao) VALUES
                    ('facebook', 'bi bi-facebook', 'Ícone do Facebook'),
                    ('instagram', 'bi bi-instagram', 'Ícone do Instagram'),
                    ('twitter', 'bi bi-twitter', 'Ícone do Twitter'),
                    ('linkedin', 'bi bi-linkedin', 'Ícone do LinkedIn'),
                    ('youtube', 'bi bi-youtube', 'Ícone do YouTube'),
                    ('whatsapp', 'bi bi-whatsapp', 'Ícone do WhatsApp'),
                    ('telefone', 'bi bi-telephone', 'Ícone de Telefone'),
                    ('email', 'bi bi-envelope', 'Ícone de Email'),
                    ('localizacao', 'bi bi-geo-alt', 'Ícone de Localização/Endereço'),
                    ('site', 'bi bi-globe', 'Ícone de Site/Globo');
               `;
               await this.query(sqlInsert);
          }
     }

     async listarIcones() {
          const sql = "SELECT * FROM icones ORDER BY tipo ASC";
          return await this.query(sql);
     }

     async adicionarIcone(tipo, classe, descricao = null) {
          const sql = "INSERT INTO icones (tipo, classe, descricao) VALUES (?, ?, ?)";
          return await this.query(sql, [tipo, classe, descricao]);
     }

     async editarIcone(id, tipo, classe, descricao = null) {
          const sql = `
               UPDATE icones 
               SET tipo = ?, classe = ?, descricao = ?
               WHERE id = ?
          `;
          return await this.query(sql, [tipo, classe, descricao, id]);
     }

     async excluirIcone(id) {
          const sql = "DELETE FROM icones WHERE id = ?";
          return await this.query(sql, [id]);
     }

     /* ==========================================================
        CRUD COMPLETO DO RODAPÉ
     ========================================================== */

     // CREATE
     async adicionar() {
          const sql = `
               INSERT INTO ${this.tabela} (tipo, valor, icone_id, link)
               VALUES (?, ?, ?, ?)
          `;
          return await this.query(sql, [this.tipo, this.valor, this.icone_id, this.link]);
     }

     // UPDATE
     async editar() {
          const sql = `
               UPDATE ${this.tabela} 
               SET tipo = ?, valor = ?, icone_id = ?, link = ?
               WHERE id = ?
          `;
          return await this.query(sql, [this.tipo, this.valor, this.icone_id, this.link, this.id]);
     }

     // DELETE
     async excluir() {
          const sql = `DELETE FROM ${this.tabela} WHERE id = ?`;
          return await this.query(sql, [this.id]);
     }

     // READ - listar todos os itens com ícones
     async listarTodos() {
          const sql = `
               SELECT r.*, i.tipo AS icone_tipo, i.classe AS icone_classe
               FROM ${this.tabela} r
               LEFT JOIN icones i ON r.icone_id = i.id
               ORDER BY r.id ASC
          `;
          return await this.query(sql);
     }

     // READ - buscar um por ID
     async buscarPorId(id) {
          const sql = `
               SELECT r.*, i.tipo AS icone_tipo, i.classe AS icone_classe
               FROM ${this.tabela} r
               LEFT JOIN icones i ON r.icone_id = i.id
               WHERE r.id = ?
          `;
          const result = await this.query(sql, [id]);

          if (result.length > 0) {
               const dados = result[0];
               this.id = dados.id;
               this.tipo = dados.tipo;
               this.valor = dados.valor;
               this.icone_id = dados.icone_id;
               this.link = dados.link;
               return dados;
          }

          return null;
     }

     // READ - lista para exibição pública (index.js)
     async listarAtivosComIcones() {
          const sql = `
               SELECT r.*, i.tipo AS icone_tipo, i.classe AS icone_classe
               FROM ${this.tabela} r
               LEFT JOIN icones i ON r.icone_id = i.id
               ORDER BY r.id ASC
          `;
          const dados = await this.query(sql);

          return dados.map(item => ({
               ...item,
               icone_html: item.icone_classe
                    ? `<i class="${item.icone_classe}"></i>`
                    : ''
          }));
     }
}

module.exports = Rodape;
