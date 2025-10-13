/**
 * Classe Integrantes
 * Responsável pela lógica dos integrantes do Vocal Louvor & Vida
 * Versão Node.js — Herda de CRUD.js
 */

import bcrypt from "bcrypt";
import CRUD from "./CRUD.js";

export default class Integrantes extends CRUD {
  #id;
  #nome;
  #nome_user;
  #senha;
  #funcao;
  #foto;
  #ativo;

  constructor() {
    super("integrantes");
    this.setCampos(["nome", "nome_user", "senha", "funcao", "foto", "ativo"]);
  }

  // ==========================================================
  // GETTERS E SETTERS
  // ==========================================================
  get id() {
    return this.#id;
  }
  set id(valor) {
    this.#id = parseInt(valor);
  }

  get nome() {
    return this.#nome;
  }
  set nome(valor) {
    this.#nome = valor.trim();
  }

  get nome_user() {
    return this.#nome_user;
  }
  set nome_user(valor) {
    this.#nome_user = valor.trim();
  }

  get funcao() {
    return this.#funcao;
  }
  set funcao(valor) {
    this.#funcao = valor.trim();
  }

  get foto() {
    return this.#foto;
  }
  set foto(valor) {
    this.#foto = valor.trim();
  }

  get ativo() {
    return this.#ativo === 1;
  }
  set ativo(valor) {
    this.#ativo = valor ? 1 : 0;
  }

  async setSenha(senhaPlain) {
    if (senhaPlain && senhaPlain.trim() !== "") {
      this.#senha = await bcrypt.hash(senhaPlain, 10);
    }
  }

  // ==========================================================
  // MÉTODOS ESPECÍFICOS
  // ==========================================================

  /**
   * Cadastrar novo integrante
   */
  async cadastrar(dados) {
    if (!dados.nome || !dados.nome_user || !dados.senha) {
      throw new Error("Campos obrigatórios: nome, nome_user e senha.");
    }

    // Verifica se já existe o nome_user
    const existe = await this.read("nome_user = ?", [dados.nome_user]);
    if (existe.length > 0) {
      throw new Error("Nome de usuário já em uso.");
    }

    // Criptografa senha
    dados.senha = await bcrypt.hash(dados.senha, 10);
    if (typeof dados.ativo === "undefined") dados.ativo = 1;

    // Insere no banco (usa método genérico do CRUD)
    return await this.create(dados);
  }

  /**
   * Atualizar integrante
   */
  async atualizarIntegrante(id, dados) {
    if (dados.senha && dados.senha.trim() !== "") {
      dados.senha = await bcrypt.hash(dados.senha, 10);
    } else {
      delete dados.senha;
    }

    return await this.update(id, dados);
  }

  /**
   * Desativar integrante (soft delete)
   */
  async desativarIntegrante(id) {
    return await this.update(id, { ativo: 0 });
  }

  /**
   * Ativar integrante
   */
  async ativarIntegrante(id) {
    return await this.update(id, { ativo: 1 });
  }

  /**
   * Remover fisicamente (delete real)
   */
  async removerFisico(id) {
    return await this.delete(id);
  }

  /**
   * Atualizar foto
   */
  async atualizarFoto(id, caminhoFoto) {
    return await this.update(id, { foto: caminhoFoto });
  }

  /**
   * Buscar por nome_user
   */
  async buscarPorNomeUser(nomeUser) {
    const res = await this.read("nome_user = ? LIMIT 1", [nomeUser]);
    return res.length > 0 ? res[0] : null;
  }

  /**
   * Login de integrante
   */
  async login(nomeUser, senha) {
    const usuario = await this.buscarPorNomeUser(nomeUser);
    if (!usuario || !usuario.senha) return false;

    const senhaCorreta = await bcrypt.compare(senha, usuario.senha);
    if (!senhaCorreta) return false;

    // Simula sessão
    global.sessaoIntegrante = {
      id: usuario.id,
      nome: usuario.nome,
      nome_user: usuario.nome_user,
    };

    // Remove senha antes de retornar
    delete usuario.senha;
    return usuario;
  }

  /**
   * Listar integrantes (ativos por padrão)
   */
  async listar(apenasAtivos = true, ordem = "nome ASC") {
    if (apenasAtivos) {
      return await this.read("ativo = 1", [], ordem);
    }
    return await this.read("", [], ordem);
  }
}
