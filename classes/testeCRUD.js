import CRUD from "./CRUD.js";

(async () => {
  const usuarios = new CRUD("integrantes");
  usuarios.setCampos(["nome", "nome_user", "senha", "funcao", "foto", "ativo"]);

  console.log("Criando registro...");
  const id = await usuarios.create({
    nome: "Teste Node",
    nome_user: "node_user",
    senha: "12345",
    funcao: "Cantor",
    foto: "foto.png",
    ativo: 1,
  });

  console.log("Novo ID:", id);
  console.log("Buscando registro:", await usuarios.find(id));
  console.log("Total de registros:", await usuarios.contar());
})();
