import express from "express";
import Admin from "../classes/Admin.js";

const app = express();
app.use(express.json());

// 🔹 Endpoint para carregar dados atuais do admin
app.get("/api/admin/dados", async (req, res) => {
  try {
    const admin = new Admin();
    await admin.init();

    const ok = await admin.carregarPorId(1); // id fixo do admin
    if (!ok) {
      return res.json({ sucesso: false, mensagem: "Administrador não encontrado." });
    }

    res.json({
      sucesso: true,
      admin: {
        id: admin.id,
        usuario: admin.usuario,
        criado_em: admin.criado_em,
        atualizado_em: admin.atualizado_em
      }
    });
  } catch (erro) {
    console.error("Erro ao buscar admin:", erro);
    res.status(500).json({ sucesso: false, mensagem: "Erro ao carregar dados do administrador." });
  }
});

// 🔹 Endpoint para atualizar o admin
app.post("/api/admin/atualizar", async (req, res) => {
  try {
    const { usuario, senha } = req.body;

    const admin = new Admin();
    await admin.init();
    await admin.carregarPorId(1);

    admin.usuario = usuario;
    if (senha && senha.trim() !== "") {
      await admin.setSenha(senha);
    }

    const atualizado = await admin.atualizar();

    if (atualizado) {
      res.json({ sucesso: true, mensagem: "Administrador atualizado com sucesso!" });
    } else {
      res.json({ sucesso: false, mensagem: "Nenhuma alteração realizada." });
    }
  } catch (erro) {
    console.error("Erro ao atualizar admin:", erro);
    res.status(500).json({ sucesso: false, mensagem: "Erro ao atualizar administrador." });
  }
});

// 🔹 Inicia o servidor (se ainda não estiver rodando)
const PORT = 3001;
app.listen(PORT, () => {
  console.log(`✅ Servidor edit_admin.js rodando na porta ${PORT}`);
});
