import CRUD from './CRUD.js';

export default class Topicos extends CRUD {
    constructor() {
        super('topicos');
        this.id = null;
        this.titulo = '';
        this.texto = '';
        this.botao_texto = null;
        this.botao_link = null;
        this.tipo_midia = 'nenhum';
        this.arquivo_midia = null;
        this.lado = 'direita';
        this.ativo = 1;
        this.ordem = 0;
    }

    /* =====================
       GETTERS E SETTERS
       ===================== */
    setId(id) { this.id = parseInt(id) || null; }
    getId() { return this.id; }

    setTitulo(titulo) { this.titulo = titulo?.trim() || ''; }
    getTitulo() { return this.titulo; }

    setTexto(texto) { this.texto = texto?.trim() || ''; }
    getTexto() { return this.texto; }

    setBotaoTexto(texto) { this.botao_texto = texto || null; }
    getBotaoTexto() { return this.botao_texto; }

    setBotaoLink(link) { this.botao_link = link || null; }
    getBotaoLink() { return this.botao_link; }

    setTipoMidia(tipo) {
        const permitidos = ['imagem', 'video', 'nenhum'];
        this.tipo_midia = permitidos.includes(tipo) ? tipo : 'nenhum';
    }
    getTipoMidia() { return this.tipo_midia; }

    setArquivoMidia(arquivo) {
        this.arquivo_midia = arquivo || null;
        this.detectarTipoMidia();
    }
    getArquivoMidia() { return this.arquivo_midia; }

    setLado(lado) {
        const permitidos = ['esquerda', 'direita'];
        this.lado = permitidos.includes(lado) ? lado : 'direita';
    }
    getLado() { return this.lado; }

    setAtivo(ativo) { this.ativo = ativo === 1 ? 1 : 0; }
    getAtivo() { return this.ativo; }

    setOrdem(ordem) { this.ordem = parseInt(ordem) || 0; }
    getOrdem() { return this.ordem; }

    /* =====================
       DETECÇÃO AUTOMÁTICA
       ===================== */
    detectarTipoMidia() {
        if (!this.arquivo_midia) {
            this.tipo_midia = 'nenhum';
            return;
        }

        const extensao = this.arquivo_midia.split('.').pop().toLowerCase();
        const imagens = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        const videos = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];

        if (imagens.includes(extensao)) {
            this.tipo_midia = 'imagem';
        } else if (videos.includes(extensao)) {
            this.tipo_midia = 'video';
        } else {
            this.tipo_midia = 'nenhum';
        }
    }

    /* =====================
       CREATE
       ===================== */
    async inserir() {
        if (this.ordem === undefined || this.ordem === null)
            this.ordem = await this.proximaOrdem();

        const sql = `
            INSERT INTO ${this.tabela} 
            (titulo, texto, botao_texto, botao_link, tipo_midia, arquivo_midia, lado, ativo, ordem)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        `;

        const params = [
            this.titulo,
            this.texto,
            this.botao_texto,
            this.botao_link,
            this.tipo_midia,
            this.arquivo_midia,
            this.lado,
            this.ativo,
            this.ordem
        ];

        return await this.executar(sql, params);
    }

    /* =====================
       READ
       ===================== */
    async listarTodos() {
        const sql = `SELECT * FROM ${this.tabela} ORDER BY ordem ASC`;
        return await this.executar(sql);
    }

    async listarAtivos() {
        const sql = `SELECT * FROM ${this.tabela} WHERE ativo = 1 ORDER BY ordem ASC`;
        return await this.executar(sql);
    }

    async buscarPorId(id) {
        const sql = `SELECT * FROM ${this.tabela} WHERE id = ? LIMIT 1`;
        const resultados = await this.executar(sql, [id]);
        return resultados.length ? resultados[0] : null;
    }

    /* =====================
       UPDATE
       ===================== */
    async atualizar() {
        if (!this.id) return false;

        const sql = `
            UPDATE ${this.tabela} SET
                titulo = ?, texto = ?, botao_texto = ?, botao_link = ?,
                tipo_midia = ?, arquivo_midia = ?, lado = ?, ativo = ?, ordem = ?
            WHERE ${this.chavePrimaria || 'id'} = ?
        `;

        const params = [
            this.titulo,
            this.texto,
            this.botao_texto,
            this.botao_link,
            this.tipo_midia,
            this.arquivo_midia,
            this.lado,
            this.ativo,
            this.ordem,
            this.id
        ];

        return await this.executar(sql, params);
    }

    /* =====================
       DELETE
       ===================== */
    async excluir(id) {
        const sql = `DELETE FROM ${this.tabela} WHERE ${this.chavePrimaria || 'id'} = ?`;
        return await this.executar(sql, [id]);
    }

    /* =====================
       EXTRAS
       ===================== */
    async proximaOrdem() {
        const sql = `SELECT COALESCE(MAX(ordem), -1) + 1 AS prox FROM ${this.tabela}`;
        const resultado = await this.executar(sql);
        return resultado[0]?.prox ?? 0;
    }
}
