
-- ==============================================================================
-- 1. DDL (Data Definition Language)
-- ==============================================================================

DROP DATABASE IF EXISTS jogo_pokemon;
CREATE DATABASE jogo_pokemon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jogo_pokemon;

-- Perfis dos jogadores (Usuario.php). O dinheiro é em pokédollar.
CREATE TABLE usuario (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    dinheiro INT NOT NULL DEFAULT 1000,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Catálogo com todos os Pokémons do jogo (Pokemon.php e pasta pokemons/)
CREATE TABLE pokemon (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    estagio INT NOT NULL,
    hp_maximo INT NOT NULL,
    ataque INT NOT NULL,
    defesa INT NOT NULL,
    velocidade INT NOT NULL,
    prioridade INT NOT NULL DEFAULT 0,
    proxima_evolucao_id INT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_pokemon_evolucao
        FOREIGN KEY (proxima_evolucao_id) REFERENCES pokemon(id)
) ENGINE=InnoDB;

-- Itens do Poké Mart (Item.php)
CREATE TABLE loja (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(150) NOT NULL,
    preco INT NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Time de cada usuário (Time.php / Team Builder)
-- Guarda o estado do Pokémon para aquele jogador (HP atual e condição)
CREATE TABLE time (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    pokemon_id INT NOT NULL,
    posicao INT NOT NULL DEFAULT 1,
    hp_atual INT NOT NULL,
    status_condicao VARCHAR(20) NOT NULL DEFAULT 'Normal',
    PRIMARY KEY (id),
    CONSTRAINT fk_time_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuario(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_time_pokemon
        FOREIGN KEY (pokemon_id) REFERENCES pokemon(id)
) ENGINE=InnoDB;

-- DDL de alteração: o mesmo Pokémon não pode aparecer duas vezes no time do usuário
ALTER TABLE time ADD UNIQUE KEY uk_time_usuario_pokemon (usuario_id, pokemon_id);

-- Log das batalhas (Batalha.php) - o resultado deve ser 'vitoria' ou 'derrota'
CREATE TABLE log_batalha (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    pokemon_id INT NOT NULL,
    adversario VARCHAR(50) NOT NULL,
    resultado VARCHAR(10) NOT NULL,
    dinheiro_ganho INT NOT NULL DEFAULT 0,
    data_batalha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_log_batalha_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuario(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_log_batalha_pokemon
        FOREIGN KEY (pokemon_id) REFERENCES pokemon(id)
) ENGINE=InnoDB;


-- ==============================================================================
-- 2. DML (Data Manipulation Language) - Dados fixos do jogo
-- ==============================================================================


INSERT INTO pokemon (id, nome, tipo, estagio, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES
(9,  'Venossauro', 'Planta', 3, 80, 82, 83, 80, 0, NULL),
(10, 'Charizard',  'Fogo',   3, 78, 84, 78, 100, 0, NULL),
(11, 'Blastoise',  'Agua',   3, 79, 83, 100, 78, 0, NULL);

-- Estágio 2 (Evolucao2)
INSERT INTO pokemon (id, nome, tipo, estagio, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES
(5, 'Ivyssauro',  'Planta', 2, 60, 62, 63, 60, 0, 9),
(6, 'Charmeleon', 'Fogo',   2, 58, 64, 58, 80, 0, 10),
(7, 'Wartortle',  'Agua',   2, 59, 63, 80, 58, 0, 11),
(8, 'Raichu',     'Raio',   2, 60, 90, 55, 110, 0, NULL);

-- Estágio 1 (Evolucao1)
INSERT INTO pokemon (id, nome, tipo, estagio, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES
(1, 'Bulbassauro', 'Planta', 1, 45, 49, 49, 45, 0, 5),
(2, 'Charmander',  'Fogo',   1, 39, 52, 43, 65, 0, 6),
(3, 'Squirtle',    'Agua',   1, 44, 48, 65, 43, 0, 7),
(4, 'Pikachu',     'Raio',   1, 35, 55, 40, 90, 0, 8);

-- Itens obrigatórios do Poké Mart
INSERT INTO loja (nome, descricao, preco, tipo) VALUES
-- Restauradores de HP
('Potion', 'Recupera 20 HP', 200, 'restaurador'),
('Super Potion', 'Recupera 50 HP', 700, 'restaurador'),
('Hyper Potion', 'Recupera 200 HP', 1200, 'restaurador'),
('Max Potion', 'Recupera 100% do HP', 2500, 'restaurador'),
('Full Restore', 'Recupera 100% do HP e cura todos os status', 3000, 'restaurador'),
-- Cura de condições
('Antidote', 'Cura envenenamento', 100, 'cura'),
('Burn Heal', 'Cura queimadura', 250, 'cura'),
('Ice Heal', 'Cura congelamento', 250, 'cura'),
('Awakening', 'Cura sono', 250, 'cura'),
('Paralyze Heal', 'Cura paralisia', 200, 'cura'),
('Full Heal', 'Cura todos os status', 600, 'cura'),
-- Reviver
('Revive', 'Revive um Pokémon desmaiado com metade da vida', 1500, 'reviver'),
-- Evolução
('Pedra de Evolução', 'Evolui um Pokémon para o próximo estágio', 2100, 'evolucao');


-- ==============================================================================
-- 3. VIEWS (3 views)
-- ==============================================================================

-- View 1: time de cada usuário (Team Builder e Batalha)
CREATE VIEW view_time_usuario AS
SELECT
    t.id AS time_id,
    t.usuario_id,
    u.nome AS usuario,
    t.posicao,
    p.id AS pokemon_id,
    p.nome AS pokemon,
    p.tipo,
    p.estagio,
    t.hp_atual,
    p.hp_maximo,
    p.ataque,
    p.defesa,
    p.velocidade,
    p.prioridade,
    t.status_condicao,
    p.proxima_evolucao_id
FROM time t
INNER JOIN usuario u ON u.id = t.usuario_id
INNER JOIN pokemon p ON p.id = t.pokemon_id;

-- View 2: histórico de batalhas
CREATE VIEW view_historico_batalhas AS
SELECT
    l.id AS log_id,
    l.usuario_id,
    u.nome AS usuario,
    p.nome AS pokemon,
    l.adversario,
    l.resultado,
    l.dinheiro_ganho,
    l.data_batalha
FROM log_batalha l
INNER JOIN usuario u ON u.id = l.usuario_id
INNER JOIN pokemon p ON p.id = l.pokemon_id;

-- View 3: vitórias e dinheiro ganho por usuário
CREATE VIEW view_vitorias_usuario AS
SELECT
    u.id AS usuario_id,
    u.nome AS usuario,
    COUNT(l.id) AS vitorias,
    SUM(l.dinheiro_ganho) AS total_ganho
FROM usuario u
INNER JOIN log_batalha l ON l.usuario_id = u.id
WHERE l.resultado = 'vitoria'
GROUP BY u.id, u.nome;


-- ==============================================================================
-- 4. TRIGGERS
-- ==============================================================================

DELIMITER //

-- Trigger 1: ao registrar uma vitória no log, o dinheiro vai para o usuário
CREATE TRIGGER trigger_dinheiro_vitoria
AFTER INSERT ON log_batalha
FOR EACH ROW
BEGIN
    IF NEW.resultado = 'vitoria' THEN
        UPDATE usuario
        SET dinheiro = dinheiro + NEW.dinheiro_ganho
        WHERE id = NEW.usuario_id;
    END IF;
END //

-- Trigger 2: o HP atual nunca passa do máximo do Pokémon e nunca fica abaixo de 0
CREATE TRIGGER trigger_limite_hp
BEFORE UPDATE ON time
FOR EACH ROW
BEGIN
    DECLARE maximo INT;

    SELECT hp_maximo INTO maximo FROM pokemon WHERE id = NEW.pokemon_id;

    IF NEW.hp_atual > maximo THEN
        SET NEW.hp_atual = maximo;
    END IF;
    IF NEW.hp_atual < 0 THEN
        SET NEW.hp_atual = 0;
    END IF;
END //

DELIMITER;