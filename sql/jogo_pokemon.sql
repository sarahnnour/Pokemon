-- =====================================================
-- Banco de dados: jogo_pokemon (MySQL)
-- =====================================================


-- =====================================================
-- 1. DDL - Criação do banco e das tabelas
-- =====================================================

CREATE DATABASE jogo_pokemon CHARACTER SET utf8mb4;
USE jogo_pokemon;

-- Perfis dos jogadores 
CREATE TABLE usuario (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    pokecoins INT NOT NULL DEFAULT 1000,
);

-- Pokémon de cada jogador
CREATE TABLE pokemon (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    nome VARCHAR(50) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    nivel INT NOT NULL DEFAULT ,
    hp_maximo INT NOT NULL,
    hp_atual INT NOT NULL,
    ataque INT NOT NULL,
    defesa INT NOT NULL,
    velocidade INT NOT NULL,
    prioridade INT NOT NULL DEFAULT 0,
    status_condicao VARCHAR(20) NOT NULL DEFAULT,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);

-- Itens vendidos no Poké Mart
CREATE TABLE loja (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(100) NOT NULL,
    preco INT NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    PRIMARY KEY (id)
);

-- Time de cada jogador (quais Pokémon ele escolheu)
CREATE TABLE time (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    pokemon_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (pokemon_id) REFERENCES pokemon(id)
);

-- Um Pokémon não pode aparecer duas vezes no time (DDL de alteração)
ALTER TABLE time ADD UNIQUE (pokemon_id);

-- Registro de todas as batalhas
CREATE TABLE log_batalha (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    pokemon_id INT NOT NULL,
    adversario VARCHAR(50) NOT NULL,
    resultado VARCHAR(10) NOT NULL,
    dinheiro_ganho INT NOT NULL DEFAULT 0,
    data_batalha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (pokemon_id) REFERENCES pokemon(id)
);


-- =====================================================
-- 2. DML - Inserção de dados iniciais
-- =====================================================

-- Itens da loja
INSERT INTO loja (nome, descricao, preco, tipo) VALUES
('Potion', 'Recupera 20 HP', 200, 'restaurador'),
('Super Potion', 'Recupera 50 HP', 700, 'restaurador'),
('Hyper Potion', 'Recupera 200 HP', 1200, 'restaurador'),
('Max Potion', 'Recupera 100% do HP', 2500, 'restaurador'),
('Full Restore', 'Recupera 100% do HP e cura todos os status', 3000, 'restaurador'),
('Antidote', 'Cura envenenamento', 100, 'cura'),
('Burn Heal', 'Cura queimadura', 250, 'cura'),
('Ice Heal', 'Cura congelamento', 250, 'cura'),
('Awakening', 'Cura sono', 250, 'cura'),
('Paralyze Heal', 'Cura paralisia', 200, 'cura'),
('Full Heal', 'Cura todos os status', 600, 'cura'),
('Revive', 'Revive um Pokémon desmaiado com metade da vida', 1500, 'reviver'),
('Pedra de Evolução', 'Evolui um Pokémon', 2100, 'evolucao');

-- Usuários
INSERT INTO usuario (nome, dinheiro) VALUES
('Ash', 1000),
('Misty', 1000),
('Brock', 1000);

-- Pokémon iniciais
-- (usuario_id, nome, tipo, nivel, hp_maximo, hp_atual, ataque, defesa, velocidade, prioridade)
INSERT INTO pokemon (usuario_id, nome, tipo, nivel, hp_maximo, hp_atual, ataque, defesa, velocidade, prioridade) VALUES
(1, 'Charmander', 'Fogo', 5, 39, 39, 52, 43, 65, 0),
(1, 'Squirtle', 'Agua', 5, 44, 44, 48, 65, 43, 0),
(2, 'Squirtle', 'Agua', 5, 44, 44, 48, 65, 43, 0),
(3, 'Geodude', 'Terra', 5, 40, 40, 80, 100, 20, 0);

-- Time inicial do Ash
INSERT INTO time (usuario_id, pokemon_id) VALUES
(1, 1),
(1, 2);


-- =====================================================
-- 3. VIEWS
-- =====================================================

-- View 1: time de cada usuário com os dados dos Pokémon
CREATE VIEW view_time_usuario AS
SELECT u.nome AS usuario, p.id AS pokemon_id, p.nome AS pokemon, p.tipo,
       p.nivel, p.hp_atual, p.hp_maximo, p.status_condicao
FROM time t
INNER JOIN usuario u ON u.id = t.usuario_id
INNER JOIN pokemon p ON p.id = t.pokemon_id;

-- View 2: histórico das batalhas
CREATE VIEW view_historico_batalhas AS
SELECT l.id, u.nome AS usuario, p.nome AS pokemon, l.adversario,
       l.resultado, l.dinheiro_ganho, l.data_batalha
FROM log_batalha l
INNER JOIN usuario u ON u.id = l.usuario_id
INNER JOIN pokemon p ON p.id = l.pokemon_id;

-- View 3: itens da loja ordenados pelo preço
CREATE VIEW view_itens_loja AS
SELECT id, nome, descricao, preco, tipo
FROM loja
ORDER BY preco;


-- =====================================================
-- 4. TRIGGERS
-- =====================================================
DELIMITER //

-- Trigger 1: quando uma batalha é vencida, soma o dinheiro ao usuário
CREATE TRIGGER trigger_dinheiro_vitoria
AFTER INSERT ON log_batalha
FOR EACH ROW
BEGIN
    IF NEW.resultado = 'vitoria' THEN
        UPDATE usuario
        SET dinheiro = dinheiro + NEW.dinheiro_ganho
        WHERE id = NEW.usuario_id;
    END IF;
END//

-- Trigger 2: o HP nunca passa do máximo nem fica abaixo de zero
CREATE TRIGGER trigger_limite_hp
BEFORE UPDATE ON pokemon
FOR EACH ROW
BEGIN
    IF NEW.hp_atual > NEW.hp_maximo THEN
        SET NEW.hp_atual = NEW.hp_maximo;
    END IF;
    IF NEW.hp_atual < 0 THEN
        SET NEW.hp_atual = 0;
    END IF;
END//

DELIMITER ;


-- =====================================================
-- 5. DML (alteração) + DTL - Transações da compra na loja
-- =====================================================

-- Deixa o Charmander machucado para o teste
UPDATE pokemon SET hp_atual = 10 WHERE id = 1;

-- Compra com sucesso: Ash compra uma Potion (200) e usa no Charmander
START TRANSACTION;
UPDATE usuario SET dinheiro = dinheiro - 200 WHERE id = 1;
UPDATE pokemon SET hp_atual = hp_atual + 20 WHERE id = 1;
COMMIT;

-- Compra cancelada: Ash desiste de comprar uma Super Potion (700)
START TRANSACTION;
UPDATE usuario SET dinheiro = dinheiro - 700 WHERE id = 1;
UPDATE pokemon SET hp_atual = hp_atual + 50 WHERE id = 1;
ROLLBACK;


-- =====================================================
-- 6. DQL - Testes (SELECT)
-- =====================================================

-- Dinheiro do Ash: deve ser 800 (a compra foi confirmada e a outra desfeita)
-- HP do Charmander: deve ser 30 (10 + 20)
SELECT nome, dinheiro FROM usuario WHERE id = 1;
SELECT nome, hp_atual, hp_maximo FROM pokemon WHERE id = 1;

-- Testa o trigger do limite de HP: tenta colocar 999, deve ficar 39
UPDATE pokemon SET hp_atual = 999 WHERE id = 1;
SELECT nome, hp_atual, hp_maximo FROM pokemon WHERE id = 1;

-- Testa o trigger do dinheiro: Ash vence uma batalha e ganha 300
INSERT INTO log_batalha (usuario_id, pokemon_id, adversario, resultado, dinheiro_ganho)
VALUES (1, 1, 'Geodude', 'vitoria', 300);
SELECT nome, dinheiro FROM usuario WHERE id = 1;

-- Testa as 3 views
SELECT * FROM view_time_usuario;
SELECT * FROM view_historico_batalhas;
SELECT * FROM view_itens_loja;
