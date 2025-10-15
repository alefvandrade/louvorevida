<?php
require_once __DIR__ . '/CRUD.class.php';

class Agenda extends CRUD
{
     public ?int $id = null;
     public string $titulo = '';
     public ?string $descricao = null;
     public ?string $local = null;
     public ?string $dia = null; // formato YYYY-MM-DD
     public ?string $hora = null; // formato HH:MM:SS
     public int $ativo = 1;
     public int $ordem = 0;

     public function __construct()
     {
          parent::__construct('agenda');
          $this->setCampos(['titulo', 'descricao', 'local', 'dia', 'hora', 'ativo', 'ordem']);
          $this->chavePrimaria = 'id';
     }

     /* =====================
        CREATE
     ===================== */
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

     /* =====================
        READ
     ===================== */
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

     /* =====================
        UPDATE
     ===================== */
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

     /* =====================
        DELETE
     ===================== */
     public function excluir(int $id): bool
     {
          return $this->delete($id);
     }

     /* =====================
        Próxima ordem automática
     ===================== */
     public function proximaOrdem(): int
     {
          $sql = "SELECT COALESCE(MAX(ordem), -1) + 1 AS prox FROM {$this->tabela}";
          $resultado = $this->conexao->query($sql);
          $linha = $resultado->fetch_assoc();
          return (int) ($linha['prox'] ?? 0);
     }
}
