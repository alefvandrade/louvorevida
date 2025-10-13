/**
 * Classe Admin
 * Gerencia autenticação e atualização do administrador
 * Sistema: Vocal Louvor & Vida (versão Node.js)
 */

import bcrypt from "bcrypt";
import CRUD from "./CRUD.js";
import Conexao from "./Conexao.js";

export default class Admin extends CRUD {
    #id;
    #usuario;
    #senha;
    #criado_em;
    #atualizado_em;

    constructor() {
        super("admin");
        this.conexao = null; // conexão ainda não carregada
    }

    /** Inicializa a conexão (deve ser chamada antes de usar) */
    async init() {
        this.conexao = await Conexao.conectar();
    }

    // ==========================================================
    // GETTERS e SETTERS
    // ==========================================================
    get id() {
        return this.#id;
    }
    set id(valor) {
        this.#id = valor;
    }

    get usuario() {
        return this.#usuario;
    }
    set usuario(valor) {
        this.#usuario = valor.trim();
    }

    get senha() {
        return this.#senha;
    }
    async setSenha(senha) {
        this.#senha = await bcrypt.hash(senha, 10);
    }

    get criado_em() {
        return this.#criado_em;
    }
    set criado_em(data) {
        this.#criado_em = data;
    }

    get atualizado_em() {
        return this.#atualizado_em;
    }
    set atualizado_em(data) {
        this.#atualizado_em = data;
    }

    // ==========================================================
    // LOGIN
    // ==========================================================
    async login(usuario, senha) {
        try {
            const [rows] = await this.conexao.execute(
                `SELECT * FROM ${this.tabela} WHERE usuario = ? LIMIT 1`,
                [usuario]
            );

            if (rows.length === 0) return false;

            const admin = rows[0];
            const senhaCorreta = await bcrypt.compare(senha, admin.senha);

            if (!senhaCorreta) return false;

            this.#id = admin.id;
            this.#usuario = admin.usuario;
            this.#senha = admin.senha;
            this.#criado_em = admin.criado_em;
            this.#atualizado_em = admin.atualizado_em;

            global.sessaoAdmin = {
                id: this.#id,
                usuario: this.#usuario
            };

            return true;
        } catch (error) {
            console.error("Erro no login:", error);
            throw error;
        }
    }

    // ==========================================================
    // LOGOUT
    // ==========================================================
    logout() {
        global.sessaoAdmin = null;
        console.log("Sessão encerrada. Admin deslogado.");
    }

    // ==========================================================
    // VERIFICA SESSÃO
    // ==========================================================
    verificaSessao() {
        if (!global.sessaoAdmin) {
            throw new Error("Sessão expirada ou inexistente. Faça login novamente.");
        }
        return true;
    }

    // ==========================================================
    // ATUALIZAR USUÁRIO E SENHA
    // ==========================================================
    async atualizar() {
        if (!this.#id) {
            throw new Error("ID do admin não definido para atualização.");
        }

        let sql, params;

        if (this.#senha) {
            sql = `
                UPDATE ${this.tabela}
                SET usuario = ?, senha = ?, atualizado_em = NOW()
                WHERE id = ?
            `;
            params = [this.#usuario, this.#senha, this.#id];
        } else {
            sql = `
                UPDATE ${this.tabela}
                SET usuario = ?, atualizado_em = NOW()
                WHERE id = ?
            `;
            params = [this.#usuario, this.#id];
        }

        const [result] = await this.conexao.execute(sql, params);
        return result.affectedRows > 0;
    }

    // ==========================================================
    // CARREGA DADOS DO ADMIN
    // ==========================================================
    async carregarPorId(id) {
        const [rows] = await this.conexao.execute(
            `SELECT * FROM ${this.tabela} WHERE id = ? LIMIT 1`,
            [id]
        );

        if (rows.length === 0) return false;

        const admin = rows[0];
        this.#id = admin.id;
        this.#usuario = admin.usuario;
        this.#senha = admin.senha;
        this.#criado_em = admin.criado_em;
        this.#atualizado_em = admin.atualizado_em;

        return true;
    }
}