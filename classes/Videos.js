import CRUD from './CRUD.js';

export default class Videos extends CRUD {
    constructor() {
        super('videos');
        this.id = null;
        this.titulo_video = '';
        this.data_gravacao = null;
        this.capa_video = null;
        this.video = null;
        this.exibir_no_index = 0;
        this.orientacao = 'auto';
        this.ativo = 1;
    }

    /* ===========================
       GETTERS e SETTERS
       =========================== */
    setId(id) { this.id = parseInt(id) || null; }
    getId() { return this.id; }

    setTituloVideo(titulo) { this.titulo_video = titulo?.trim() || ''; }
    getTituloVideo() { return this.titulo_video; }

    setDataGravacao(data) { this.data_gravacao = data || null; }
    getDataGravacao() { return this.data_gravacao; }

    setCapaVideo(capa) { this.capa_video = capa || null; }
    getCapaVideo() { return this.capa_video; }

    setVideo(video) { this.video = video || null; }
    getVideo() { return this.video; }

    setExibirNoIndex(valor) { this.exibir_no_index = valor === 1 ? 1 : 0; }
    getExibirNoIndex() { return this.exibir_no_index; }

    setOrientacao(orientacao) {
        const permitidos = ['horizontal', 'vertical', 'auto'];
        this.orientacao = permitidos.includes(orientacao) ? orientacao : 'auto';
    }
    getOrientacao() { return this.orientacao; }

    setAtivo(ativo) { this.ativo = ativo === 1 ? 1 : 0; }
    getAtivo() { return this.ativo; }

    /* ===========================
       CREATE
       =========================== */
    async inserir() {
        const sql = `
            INSERT INTO ${this.tabela}
            (titulo_video, data_gravacao, capa_video, video, exibir_no_index, orientacao, ativo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        `;

        const params = [
            this.titulo_video,
            this.data_gravacao,
            this.capa_video,
            this.video,
            this.exibir_no_index,
            this.orientacao,
            this.ativo
        ];

        return await this.executar(sql, params);
    }

    /* ===========================
       READ
       =========================== */
    async listarTodos() {
        const sql = `SELECT * FROM ${this.tabela} ORDER BY criado_em DESC`;
        return await this.executar(sql);
    }

    async listarAtivos() {
        const sql = `SELECT * FROM ${this.tabela} WHERE ativo = 1 ORDER BY criado_em DESC`;
        return await this.executar(sql);
    }

    async listarIndex() {
        const sql = `
            SELECT * FROM ${this.tabela}
            WHERE ativo = 1 AND exibir_no_index = 1
            ORDER BY criado_em DESC
        `;
        return await this.executar(sql);
    }

    async buscarPorId(id) {
        const sql = `SELECT * FROM ${this.tabela} WHERE id = ? LIMIT 1`;
        const resultados = await this.executar(sql, [id]);
        return resultados.length ? resultados[0] : null;
    }

    /* ===========================
       UPDATE
       =========================== */
    async atualizar() {
        if (!this.id) return false;

        const sql = `
            UPDATE ${this.tabela} SET
                titulo_video = ?,
                data_gravacao = ?,
                capa_video = ?,
                video = ?,
                exibir_no_index = ?,
                orientacao = ?,
                ativo = ?
            WHERE id = ?
        `;

        const params = [
            this.titulo_video,
            this.data_gravacao,
            this.capa_video,
            this.video,
            this.exibir_no_index,
            this.orientacao,
            this.ativo,
            this.id
        ];

        return await this.executar(sql, params);
    }

    /* ===========================
       DELETE
       =========================== */
    async excluir(id) {
        const sql = `DELETE FROM ${this.tabela} WHERE id = ?`;
        return await this.executar(sql, [id]);
    }
}
