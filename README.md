# vocal.louvorevida
Site pra mosstrar o vocal louvor e vida

config.ini:
[database]
host = localhost
user = root
password =
dbname = vocal_vida
charset = utf8mb4

🎵 Louvor e Vida — Sistema Administrativo
📘 Sumário

1. Visão Geral

2. Estrutura do Projeto

3. Tecnologias Utilizadas

4. Regras de Negócio

5. Funcionalidades do Sistema

6. Banco de Dados

7. Estrutura de Pastas

8. Instruções de Execução

9. Fluxo do Sistema

10. Segurança e Autenticação

11. Manutenção e Padrões

12. Melhorias Futuras

1. Visão Geral

O Louvor e Vida é um sistema web administrativo voltado para a gestão de um ministério de louvor.
Ele permite gerenciar vídeos, músicas, eventos, e o painel de administração, com interface moderna em Bootstrap e integração total com banco de dados MySQL.

O sistema possui:

Login e autenticação de administrador.

Painel de controle com cabeçalho e rodapé dinâmicos.

CRUDs para vídeos, músicas, repertórios, e dados institucionais.

Edição de perfil do administrador com atualização no banco.

2. Estrutura do Projeto

O projeto é baseado em Node.js (Express) no backend e HTML/Bootstrap/JS no frontend.

Cada módulo possui sua classe de controle (/classes) com métodos CRUD:

inserir()

listar()

buscarPorId()

atualizar()

excluir()

E há uma camada visual (/dashboard) que consome essas classes.

3. Tecnologias Utilizadas
Tecnologia	Uso principal
Node.js (v18+)	Backend
Express.js	Servidor web
MySQL2	Conexão com banco
bcrypt	Criptografia de senha
Bootstrap 5	Layout e estilo
Font Awesome	Ícones
Vanilla JS	Interações e AJAX
HTML5 / CSS3	Estrutura visual
EJS / Includes	Importação de header/footer
4. Regras de Negócio

Login de Administrador

O administrador se autentica com usuário e senha criptografada no banco.

Somente usuários válidos acessam o painel (dashboard).

Senhas são armazenadas com bcrypt.

Administração

O nome do administrador é exibido no header, puxado do banco.

Ao clicar no nome, aparecem duas opções:

Editar perfil

Sair do sistema

Cabeçalho e Rodapé

Ambos vêm da classe Cabecalho, com dados dinâmicos:

Título do ministério (ex: “Vocal Louvor e Vida”)

Logo

Cores padrão do tema (padrão: tema fixo, sem alternância claro/escuro)

São incluídos automaticamente em todas as páginas com:

{% include "dashboard/_header.html" %}
{% include "dashboard/_footer.html" %}


Edição do Administrador

O arquivo edit_admin.html carrega os dados atuais do admin (nome, usuário, e-mail, senha).

O admin pode alterar qualquer campo.

Ao salvar:

O sistema chama Admin.atualizar() no backend.

Mostra mensagem de sucesso ou erro conforme o resultado.

As alterações são refletidas imediatamente no banco.

CRUD de Vídeos e Músicas

Cada vídeo tem título, URL e descrição.

Cada música tem título, compositor, letra e status (ativo/inativo).

O sistema valida campos obrigatórios e impede duplicações.

Segurança

Nenhum CRUD é acessível sem login válido.

Sessões são mantidas com tokens.

O sistema nunca exibe senhas em texto puro.

5. Funcionalidades do Sistema
Módulo	Função	Regras principais
Login	Autenticação e controle de sessão	Senha com bcrypt
Dashboard	Tela inicial de administração	Exibe resumo e atalhos
Administração	Editar perfil, trocar senha	Usa Admin.atualizar()
Vídeos	Inserção, listagem, exclusão, edição	Valida campos obrigatórios
Músicas	Cadastro e atualização de repertório	Controla status ativo/inativo
Cabeçalho	Exibição de logo e nome do ministério	Dados vêm da tabela cabecalho
Tema visual	Tema fixo (sem alternância claro/escuro)	Cores configuráveis via Bootstrap
6. Banco de Dados
Tabelas Principais
🧑‍💻 admin
Campo	Tipo	Descrição
id	INT (PK, AI)	Identificador
nome	VARCHAR(100)	Nome completo
usuario	VARCHAR(50)	Login
senha	VARCHAR(255)	Criptografada (bcrypt)
email	VARCHAR(100)	Opcional
atualizado_em	DATETIME	Última modificação
🎬 videos
Campo	Tipo	Descrição
id	INT (PK, AI)	Identificador
titulo	VARCHAR(255)	Título do vídeo
url	TEXT	Link do vídeo (YouTube, etc.)
descricao	TEXT	Detalhes
criado_em	DATETIME	Data de inserção
🎵 musicas
Campo	Tipo	Descrição
id	INT (PK, AI)	Identificador
titulo	VARCHAR(255)	Nome da música
compositor	VARCHAR(255)	Autor
letra	TEXT	Letra completa
status	ENUM('ativo', 'inativo')	Estado atual
🏷️ cabecalho
Campo	Tipo	Descrição
id	INT (PK, AI)	Identificador
titulo	VARCHAR(255)	Ex: “Vocal Louvor e Vida”
logo	VARCHAR(255)	Caminho do logo
cor_principal	VARCHAR(7)	Ex: #1d4ed8
cor_secundaria	VARCHAR(7)	Ex: #ffffff
7. Estrutura de Pastas
(veja no git hub )

8. Instruções de Execução
🔧 Instalar dependências
npm install

💾 Configurar Banco

Atualize Conexao.js com suas credenciais:

{
  host: "localhost",
  user: "root",
  password: "SENHA_DO_BANCO",
  database: "louvorevida"
}

▶️ Rodar o servidor
node server.js


Acesse em:

http://localhost:3000

9. Fluxo do Sistema

Usuário acessa /login

Faz autenticação → redireciona para /dashboard

Header mostra nome e foto do admin (dinâmico)

Clique em Admin → Editar Perfil abre edit_admin.html

Alterações são salvas no banco via AJAX e Admin.atualizar()

Mensagem de sucesso é exibida sem recarregar a página

10. Segurança e Autenticação

Senhas criptografadas com bcrypt

Sessões armazenadas em cookies seguros

Prevenção contra SQL Injection com prepared statements

Rotas protegidas por middleware de autenticação

11. Manutenção e Padrões

Código formatado com ESLint + Prettier

Comentários obrigatórios em todas as classes

Nomes de métodos em camelCase

Layout 100% responsivo (Bootstrap grid system)

12. Melhorias Futuras

Implementar sistema de permissões (multiadmin)

Criar módulo de eventos e agenda

Adicionar upload de arquivos direto do painel

Dashboard com estatísticas gráficas

API REST pública para integração