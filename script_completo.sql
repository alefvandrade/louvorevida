-- ============================================
-- BANCO DE DADOS: vocal_vida
-- SISTEMA DE GERENCIAMENTO DO SITE DO VOCAL
-- ============================================

DROP DATABASE IF EXISTS vocal_vida;
CREATE DATABASE IF NOT EXISTS vocal_vida CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE vocal_vida;

-- ==================================================
-- 1. ADMIN
-- ==================================================
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insere admin padrão
INSERT INTO admin (usuario, senha)
VALUES ('vocal.l0uvor&vida', '$2y$10$e5jUQTBKnxRzGQ9b4m4GweaKXzWQ9H3W7SYj2IuSnv/YT5PAVVZNu'); 
-- senha 251023 (hash via password_hash)

-- ==================================================
-- 2. INTEGRANTES
-- ==================================================
CREATE TABLE IF NOT EXISTS integrantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    nome_user VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    funcao VARCHAR(100),
    foto VARCHAR(255),
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==================================================
-- 3. CABEÇALHO
-- ==================================================
CREATE TABLE IF NOT EXISTS cabecalho (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    subtitulo VARCHAR(255) DEFAULT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    fundo VARCHAR(255) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==================================================
-- 4. CARROSSEL
-- ==================================================
CREATE TABLE IF NOT EXISTS carrossel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    subtitulo VARCHAR(255) DEFAULT NULL,
    fundo VARCHAR(255) DEFAULT NULL,
    botao_texto VARCHAR(100) DEFAULT NULL,
    botao_link VARCHAR(255) DEFAULT NULL,
    mostrar_botao TINYINT(1) DEFAULT 0,
    ordem INT DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==================================================
-- 5. ÍCONES E RODAPÉ
-- ==================================================
CREATE TABLE IF NOT EXISTS icones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL,
    classe VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS rodape (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('telefone','email','facebook','instagram','whatsapp','outro') NOT NULL,
    valor VARCHAR(255) NOT NULL,
    icone_id INT DEFAULT NULL,
    link VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (icone_id) REFERENCES icones(id) ON DELETE SET NULL
);

-- ==================================================
-- 6. VÍDEOS
-- ==================================================
CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo_video VARCHAR(255) NOT NULL,
    data_gravacao DATE DEFAULT NULL,
    capa_video VARCHAR(255) DEFAULT NULL,
    video VARCHAR(255) DEFAULT NULL,
    exibir_no_index TINYINT(1) DEFAULT 0,
    orientacao ENUM('horizontal','vertical','auto') DEFAULT 'auto',
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==================================================
-- 7. TÓPICOS
-- ==================================================
CREATE TABLE IF NOT EXISTS topicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    botao_texto VARCHAR(100) DEFAULT NULL,
    botao_link VARCHAR(255) DEFAULT NULL,
    tipo_midia ENUM('imagem','video','nenhum') DEFAULT 'nenhum',
    arquivo_midia VARCHAR(255) DEFAULT NULL,
    lado ENUM('esquerda','direita') DEFAULT 'direita',
    ativo TINYINT(1) DEFAULT 1,
    ordem INT DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==================================================
-- 8. LOGS DE ATIVIDADE
-- ==================================================
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabela_afetada VARCHAR(100),
    acao ENUM('INSERT','UPDATE','DELETE'),
    registro_id INT,
    usuario VARCHAR(100),
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==================================================
-- TRIGGERS
-- ==================================================
DELIMITER $$

CREATE TRIGGER trg_log_insert
AFTER INSERT ON topicos
FOR EACH ROW
BEGIN
    INSERT INTO logs (tabela_afetada, acao, registro_id, usuario)
    VALUES ('topicos', 'INSERT', NEW.id, CURRENT_USER());
END$$

CREATE TRIGGER trg_log_update
AFTER UPDATE ON topicos
FOR EACH ROW
BEGIN
    INSERT INTO logs (tabela_afetada, acao, registro_id, usuario)
    VALUES ('topicos', 'UPDATE', NEW.id, CURRENT_USER());
END$$

CREATE TRIGGER trg_log_delete
AFTER DELETE ON topicos
FOR EACH ROW
BEGIN
    INSERT INTO logs (tabela_afetada, acao, registro_id, usuario)
    VALUES ('topicos', 'DELETE', OLD.id, CURRENT_USER());
END$$

CREATE TRIGGER trg_reordena_topicos
BEFORE INSERT ON topicos
FOR EACH ROW
BEGIN
    DECLARE max_ordem INT;
    SELECT IFNULL(MAX(ordem),0) INTO max_ordem FROM topicos;
    SET NEW.ordem = max_ordem + 1;
END$$

DELIMITER ;

-- ==================================================
-- PROCEDURES
-- ==================================================
DELIMITER $$

CREATE PROCEDURE sp_login_admin(IN p_usuario VARCHAR(100))
BEGIN
    SELECT id, usuario, senha FROM admin WHERE usuario = p_usuario LIMIT 1;
END$$

CREATE PROCEDURE sp_login_integrante(IN p_user VARCHAR(100))
BEGIN
    SELECT id, nome, nome_user, senha, funcao, foto FROM integrantes WHERE nome_user = p_user AND ativo = 1;
END$$

CREATE PROCEDURE sp_listar_topicos()
BEGIN
    SELECT * FROM topicos WHERE ativo = 1 ORDER BY ordem ASC;
END$$

CREATE PROCEDURE sp_listar_videos_index()
BEGIN
    SELECT * FROM videos WHERE ativo = 1 AND exibir_no_index = 1 ORDER BY id DESC;
END$$

CREATE PROCEDURE sp_listar_videos_pag()
BEGIN
    SELECT * FROM videos WHERE ativo = 1 ORDER BY id DESC;
END$$

CREATE PROCEDURE sp_atualiza_senha_integrante(IN p_id INT, IN p_nova_senha VARCHAR(255))
BEGIN
    UPDATE integrantes SET senha = p_nova_senha WHERE id = p_id;
END$$


-- ✅ VERSÃO FUNCIONAL: contador genérico (usando CASE, sem SQL dinâmico)
CREATE FUNCTION fn_conta_registros(tabela VARCHAR(50))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE resultado INT DEFAULT 0;

    CASE tabela
        WHEN 'admin' THEN SELECT COUNT(*) INTO resultado FROM admin WHERE 1;
        WHEN 'integrantes' THEN SELECT COUNT(*) INTO resultado FROM integrantes WHERE ativo = 1;
        WHEN 'cabecalho' THEN SELECT COUNT(*) INTO resultado FROM cabecalho WHERE 1;
        WHEN 'carrossel' THEN SELECT COUNT(*) INTO resultado FROM carrossel WHERE ativo = 1;
        WHEN 'icones' THEN SELECT COUNT(*) INTO resultado FROM icones;
        WHEN 'rodape' THEN SELECT COUNT(*) INTO resultado FROM rodape;
        WHEN 'videos' THEN SELECT COUNT(*) INTO resultado FROM videos WHERE ativo = 1;
        WHEN 'topicos' THEN SELECT COUNT(*) INTO resultado FROM topicos WHERE ativo = 1;
        ELSE SET resultado = 0;
    END CASE;

    RETURN resultado;
END$$

DELIMITER ;

-- ==================================================
-- VIEWS
-- ==================================================
CREATE OR REPLACE VIEW vw_topicos_completos AS
SELECT 
    t.*, 
    CASE 
        WHEN t.tipo_midia = 'imagem' THEN t.arquivo_midia
        WHEN t.tipo_midia = 'video' THEN t.arquivo_midia
        ELSE NULL
    END AS midia
FROM topicos t
WHERE t.ativo = 1
ORDER BY t.ordem ASC;

CREATE OR REPLACE VIEW vw_videos_index AS
SELECT id, titulo_video, capa_video, video, orientacao
FROM videos
WHERE ativo = 1 AND exibir_no_index = 1
ORDER BY id DESC;

COMMIT;
