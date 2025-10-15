<?php
require_once __DIR__ . '/CRUD.class.php';

class Agenda extends CRUD
{
     private ?int $id = null;
     private string $titulo = '';
     private ?string $descricao = null;
     private ?string $local = null;
     private ?string $dia = null; // formato YYYY-MM-DD
     private ?string $hora = null; // formato HH:MM:SS
     private int $ativo = 1;
     private int $ordem = 0;

     public function __construct()
     {
          parent::__construct('agenda');
          $this->setCampos(['titulo', 'descricao', 'local', 'dia', 'hora', 'ativo', 'ordem']);
          $this->chavePrimaria = 'id';
     }

     /* =======================
        GETTERS e SETTERS
     ======================= */
     public function getId(): ?int
     {
          return $this->id;
     }
     public function setId(int $id)
     {
          $this->id = $id;
     }

     public function getTitulo(): string
     {
          return $this->titulo;
     }
     public function setTitulo(string $titulo)
     {
          $this->titulo = $titulo;
     }

     public function getDescricao(): ?string
     {
          return $this->descricao;
     }
     public function setDescricao(?string $descricao)
     {
          $this->descricao = $descricao;
     }

     public function getLocal(): ?string
     {
          return $this->local;
     }
     public function setLocal(?string $local)
     {
          $this->local = $local;
     }

     public function getDia(): ?string
     {
          return $this->dia;
     }
     public function setDia(?string $dia)
     {
          $this->dia = $dia;
     }

     public function getHora(): ?string
     {
          return $this->hora;
     }
     public function setHora(?string $hora)
     {
          $this->hora = $hora;
     }

     public function getAtivo(): int
     {
          return $this->ativo;
     }
     public function setAtivo(int $ativo)
     {
          $this->ativo = $ativo;
     }

     public function getOrdem(): int
     {
          return $this->ordem;
     }
     public function setOrdem(int $ordem)
     {
          $this->ordem = $ordem;
     }

     /* =======================
        CREATE
     ======================= */
     public function inserir(): int
     {
          if ($this->ordem === 0) {
               $this->ordem = $this->proximaOrdem();
          }

          return $this->create([
               'titulo' => $this->titulo,
               'descricao' => $this->descricao,
               'local' => $this->local,
               'dia' => $this->dia,
               'hora' => $this->hora,
               'ativo' => $this->ativo,
               'ordem' => $this->ordem
          ]);
     }

     /* =======================
        READ
     ======================= */
     public function listarTodos(): array
     {
          return $this->read('', [], 'ordem ASC, dia ASC, hora ASC');
     }

     public function listarAtivos(): array
     {
          return $this->read('ativo = 1', [], 'ordem ASC, dia ASC, hora ASC');
     }

     public function buscarPorId(int $id): ?array
     {
          return $this->find($id);
     }

     /* =======================
        UPDATE
     ======================= */
     public function atualizar(): bool
     {
          if (!$this->id)
               return false;

          return $this->update($this->id, [
               'titulo' => $this->titulo,
               'descricao' => $this->descricao,
               'local' => $this->local,
               'dia' => $this->dia,
               'hora' => $this->hora,
               'ativo' => $this->ativo,
               'ordem' => $this->ordem
          ]);
     }

     /* =======================
        DELETE
     ======================= */
     public function excluir(int $id): bool
     {
          return $this->delete($id);
     }

     /* =======================
        Próxima ordem automática
     ======================= */
     public function proximaOrdem(): int
     {
          $sql = "SELECT COALESCE(MAX(ordem), -1) + 1 AS prox FROM {$this->tabela}";
          $resultado = $this->conexao->query($sql);
          $linha = $resultado->fetch_assoc();
          return (int) ($linha['prox'] ?? 0);
     }
}
?>