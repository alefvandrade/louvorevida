<?php
/**
 * Classe Integrantes
 * Responsável pela lógica dos integrantes do Vocal (herda CRUD)
 *
 * Requisitos:
 *  - Herda de CRUD (que fornece conexão e métodos genéricos)
 *  - Valida/hasheia senhas ao cadastrar/atualizar
 *  - Métodos para login (integrante), listar, buscar, atualizar foto, soft-delete (desativar)
 *
 * Tabela: integrantes
 * Campos relevantes: id, nome, nome_user, senha, funcao, foto, ativo, criado_em, atualizado_em
 */

require_once __DIR__ . '/CRUD.class.php';

class Integrantes extends CRUD
{
     // === Define tabela e campos permitidos para insert/update ===
     protected $tabela = 'integrantes';

     public function __construct()
     {
          // chama o construtor base informando a tabela (o CRUD espera isso)
          parent::__construct($this->tabela);

          // define os campos que podem ser inseridos/atualizados pelo CRUD genérico
          $this->setCampos(['nome', 'nome_user', 'senha', 'funcao', 'foto', 'ativo']);
     }

     // ---------------------------
     // GETTERS / SETTERS (opcionales)
     // ---------------------------
     // Você pode expandir aqui se quiser encapsular atributos locais
     // Exemplo simples apenas para demonstração:
     private $id;
     private $nome;
     private $nome_user;
     private $senha;
     private $funcao;
     private $foto;
     private $ativo;

     public function getId()
     {
          return $this->id;
     }
     public function setId($id)
     {
          $this->id = (int) $id;
     }

     public function getNome()
     {
          return $this->nome;
     }
     public function setNome($nome)
     {
          $this->nome = trim($nome);
     }

     public function getNomeUser()
     {
          return $this->nome_user;
     }
     public function setNomeUser($u)
     {
          $this->nome_user = trim($u);
     }

     /**
      * seta senha (recebe senha em texto puro e armazena já em hash localmente)
      */
     public function setSenha($senhaPlain)
     {
          if (!empty($senhaPlain)) {
               $this->senha = password_hash($senhaPlain, PASSWORD_DEFAULT);
          }
     }

     public function getFuncao()
     {
          return $this->funcao;
     }
     public function setFuncao($f)
     {
          $this->funcao = trim($f);
     }

     public function getFoto()
     {
          return $this->foto;
     }
     public function setFoto($p)
     {
          $this->foto = trim($p);
     }

     public function isAtivo()
     {
          return (int) $this->ativo === 1;
     }
     public function setAtivo($a)
     {
          $this->ativo = (int) $a;
     }

     // ---------------------------
     // MÉTODOS ESPECÍFICOS
     // ---------------------------

     /**
      * Cadastrar um novo integrante
      * $dados: array contendo nome, nome_user, senha (texto), funcao, foto (opcional), ativo (opcional)
      * Retorna o ID inserido ou lança Exception em caso de erro
      */
     public function cadastrar(array $dados)
     {
          // valida campos mínimos
          if (empty($dados['nome']) || empty($dados['nome_user']) || empty($dados['senha'])) {
               throw new Exception("Campos obrigatórios: nome, nome_user e senha.");
          }

          // verifica unicidade do nome_user
          $exist = $this->read('nome_user = :user', ['user' => $dados['nome_user']]);
          if (!empty($exist)) {
               throw new Exception("Nome de usuário já em uso.");
          }

          // prepara dados para inserir (aplica hash de senha)
          $dadosInserir = $dados;
          $dadosInserir['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

          // garante que 'ativo' exista
          if (!isset($dadosInserir['ativo'])) {
               $dadosInserir['ativo'] = 1;
          }

          // delega ao CRUD (create retorna lastInsertId)
          return $this->create($dadosInserir);
     }

     /**
      * Atualizar dados do integrante
      * $id: int
      * $dados: array com campos permitidos (se senha presente, será hasheada)
      */
     public function atualizarIntegrante(int $id, array $dados)
     {
          if (isset($dados['senha']) && !empty($dados['senha'])) {
               // transformar senha em hash
               $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
          } else {
               // remove senha do array para não sobrescrever com vazio
               unset($dados['senha']);
          }

          return $this->update($id, $dados);
     }

     /**
      * Desativar integrante (soft delete)
      */
     public function desativarIntegrante(int $id)
     {
          return $this->update($id, ['ativo' => 0]);
     }

     /**
      * Ativar integrante
      */
     public function ativarIntegrante(int $id)
     {
          return $this->update($id, ['ativo' => 1]);
     }

     /**
      * Remover fisicamente (opcional) - usa o delete do CRUD
      */
     public function removerFisico(int $id)
     {
          return $this->delete($id);
     }

     /**
      * Atualizar foto do integrante
      */
     public function atualizarFoto(int $id, string $caminhoFoto)
     {
          return $this->update($id, ['foto' => $caminhoFoto]);
     }

     /**
      * Buscar integrante por nome_user
      * Retorna array associativo do integrante ou null
      */
     public function buscarPorNomeUser(string $nomeUser)
     {
          $res = $this->read('nome_user = :user LIMIT 1', ['user' => $nomeUser]);
          if (!empty($res))
               return $res[0];
          return null;
     }

     /**
      * Login do integrante
      * Se sucesso: inicia sessão (se necessário) e retorna o array do integrante (sem enviar hash)
      * Se falha: retorna false
      */
     public function login(string $nomeUser, string $senha)
     {
          $usuario = $this->buscarPorNomeUser($nomeUser);
          if (!$usuario || !is_array($usuario)) {
               return false;
          }

          if (!isset($usuario['senha']))
               return false;

          if (password_verify($senha, $usuario['senha'])) {
               // inicia sessão se necessário
               if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
               }
               // grava dados essenciais na sessão (não armazene hash)
               $_SESSION['integrante_id'] = (int) $usuario['id'];
               $_SESSION['integrante_nome'] = $usuario['nome'];
               $_SESSION['integrante_user'] = $usuario['nome_user'];

               // remove senha antes de retornar dados
               unset($usuario['senha']);
               return $usuario;
          }

          return false;
     }

     /**
      * Lista integrantes (ativos por padrão)
      * $apenasAtivos = true -> retorna apenas ativo = 1
      */
     public function listar($apenasAtivos = true, $ordem = 'nome ASC')
     {
          if ($apenasAtivos) {
               return $this->read('ativo = 1', [], $ordem);
          }
          return $this->read('', [], $ordem);
     }
}
