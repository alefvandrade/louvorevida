<?php
require_once __DIR__ . "/CRUD.class.php";

class Integrante extends CRUD
{
  private int $id;
  private string $nome;
  private string $nome_user;
  private string $senha; // hash
  private string $funcao;
  private int $ativo;
  private string $foto;

  public function __construct()
  {
    parent::__construct("integrantes");
    $this->setCampos(['nome', 'nome_user', 'senha', 'funcao', 'foto', 'ativo']);
  }

  /* =======================
     GETTERS & SETTERS
  ======================= */
  public function getId(): int
  {
    return $this->id;
  }
  public function setId(int $id)
  {
    $this->id = $id;
  }

  public function getNome(): string
  {
    return $this->nome;
  }
  public function setNome(string $nome)
  {
    $this->nome = trim($nome);
  }

  public function getNomeUser(): string
  {
    return $this->nome_user;
  }
  public function setNomeUser(string $nome_user)
  {
    $this->nome_user = trim($nome_user);
  }

  public function getFuncao(): string
  {
    return $this->funcao;
  }
  public function setFuncao(string $funcao)
  {
    $this->funcao = trim($funcao);
  }

  public function getFoto(): string
  {
    return $this->foto;
  }
  public function setFoto(string $foto)
  {
    $this->foto = trim($foto);
  }

  public function isAtivo(): bool
  {
    return $this->ativo === 1;
  }
  public function setAtivo(bool $ativo)
  {
    $this->ativo = $ativo ? 1 : 0;
  }

  public function setSenha(string $senhaPlain)
  {
    if (!empty($senhaPlain)) {
      $this->senha = password_hash($senhaPlain, PASSWORD_BCRYPT);
    }
  }

  /* =======================
     MÉTODOS CRUD / LOGIN
  ======================= */

  // Cadastrar
  public function cadastrar(): int
  {
    $dados = [
      'nome' => $this->nome,
      'nome_user' => $this->nome_user,
      'senha' => $this->senha,
      'funcao' => $this->funcao,
      'foto' => $this->foto,
      'ativo' => $this->ativo
    ];
    return $this->create($dados);
  }

  // Atualizar
  public function atualizar(): bool
  {
    $dados = [
      'nome' => $this->nome,
      'nome_user' => $this->nome_user,
      'funcao' => $this->funcao,
      'foto' => $this->foto,
      'ativo' => $this->ativo
    ];

    if (!empty($this->senha)) {
      $dados['senha'] = $this->senha;
    }

    return $this->update($this->id, $dados);
  }

  // Listar integrantes
  public function listar(bool $apenasAtivos = true): array
  {
    $condicao = $apenasAtivos ? "ativo = 1" : "";
    return $this->read($condicao, [], "nome ASC");
  }

  // Buscar por nome_user
  public function buscarPorNomeUser(string $nomeUser): ?array
  {
    $res = $this->read("nome_user = ?", [$nomeUser]);
    return $res[0] ?? null;
  }

  // Login: retorna array do usuário sem senha, ou null
  public function login(string $nomeUser, string $senha): ?array
  {
    $usuario = $this->buscarPorNomeUser($nomeUser);
    if (!$usuario)
      return null;
    if (!password_verify($senha, $usuario['senha']))
      return null;

    // Retorna array sem a senha
    unset($usuario['senha']);
    return $usuario;
  }
}
