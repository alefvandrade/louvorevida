import Admin from "./Admin.js";

export default class EditarAdmin extends Admin {
    constructor() {
        super();
        this.adminAtual = null;
    }

    async init() {
        await super.init();
        await this.carregarAdmin();
    }

    // Carrega os dados do único admin
    async carregarAdmin() {
        try {
            // Usamos ID fixo, assumindo que só existe um admin
            const [rows] = await this.conexao.execute(
                `SELECT * FROM ${this.tabela} LIMIT 1`
            );

            if (rows.length === 0) throw new Error("Nenhum admin encontrado");

            this.adminAtual = rows[0];
            return this.adminAtual;
        } catch (error) {
            console.error("Erro ao carregar admin:", error);
        }
    }

    // Atualiza o admin com dados do formulário
    async atualizarAdmin(dados) {
        try {
            this.id = this.adminAtual.id;
            this.usuario = dados.usuario;
            if (dados.senha) await this.setSenha(dados.senha);
            const sucesso = await this.atualizar();
            return sucesso;
        } catch (error) {
            console.error("Erro ao atualizar admin:", error);
            return false;
        }
    }
}
