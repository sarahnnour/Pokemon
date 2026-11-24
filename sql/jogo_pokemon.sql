-- ==============================================================================
-- 1. DDL (Data Definition Language)
-- ==============================================================================

DROP DATABASE IF EXISTS jogo_pokemon;
CREATE DATABASE jogo_pokemon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jogo_pokemon;

-- Perfis dos jogadores (Usuario.php). O dinheiro é em pokédollar.
CREATE TABLE usuario (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    dinheiro INT NOT NULL DEFAULT 1000,
    PRIMARY KEY (id)
);

-- Catálogo com todos os Pokémons do jogo (Pokemon.php e pasta pokemons/)
CREATE TABLE pokemon (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    nivel INT NOT NULL,
    hp_maximo INT NOT NULL,
    ataque INT NOT NULL,
    defesa INT NOT NULL,
    velocidade INT NOT NULL,
    prioridade INT NOT NULL DEFAULT 0,
    proxima_evolucao_id INT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_pokemon_evolucao
        FOREIGN KEY (proxima_evolucao_id) REFERENCES pokemon(id)
);

-- Itens do Poké Mart (Item.php)
CREATE TABLE item (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(150) NOT NULL,
    preco INT NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    PRIMARY KEY (id)
);

-- Itens que cada usuário comprou e ainda não usou (Bolsa)
CREATE TABLE bolsa (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    item_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    CONSTRAINT fk_bolsa_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuario(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_bolsa_item
        FOREIGN KEY (item_id) REFERENCES item(id),
    UNIQUE KEY uk_bolsa_usuario_item (usuario_id, item_id)
);

-- Time de cada usuário (Time.php / Team Builder)
-- Guarda o estado do Pokémon para aquele jogador (HP atual e condição)
CREATE TABLE time (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    pokemon_id INT NOT NULL,
    hp_atual INT NOT NULL,
    status_condicao VARCHAR(20) NOT NULL DEFAULT 'Normal',
    no_time TINYINT(1) NOT NULL DEFAULT 1, -- 1 = está no time, 0 = guardado
    PRIMARY KEY (id),
    CONSTRAINT fk_time_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuario(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_time_pokemon
        FOREIGN KEY (pokemon_id) REFERENCES pokemon(id)
);
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
);

-- Log de tudo o que acontece no jogo (Log.php): cada evento da batalha e cada ação do usuário.
-- tipo: 'usuario', 'batalha', 'loja', 'bolsa', 'time', 'erro' ou 'seguranca'
-- usuario_id fica NULL quando ninguém está logado (ex: login com senha errada)
CREATE TABLE log_acao (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NULL,
    tipo VARCHAR(20) NOT NULL,
    descricao TEXT NOT NULL,
    ip VARCHAR(45) NOT NULL,
    data_acao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_log_acao_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuario(id)
        ON DELETE CASCADE
);


-- ==============================================================================
-- 2. DML (Data Manipulation Language) - Dados fixos do jogo
-- ==============================================================================

-- Usuários de teste (para o login e a troca de perfis)
INSERT INTO usuario (nome, senha, email) VALUES
('Ash', '123', 'ash@email.com'),
('Misty', '123', 'misty@email.com');

-- Estágio 3 (Evolucao3)
INSERT INTO pokemon (id, nome, tipo, nivel, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES
(9,  'Venossauro', 'Planta', 30, 190, 90, 90,  80,  0, NULL),
(10, 'Charizard',  'Fogo',   30, 180, 92, 85,  100, 0, NULL),
(11, 'Blastoise',  'Agua',   30, 185, 88, 100, 78,  0, NULL);

-- Estágio 2 (Evolucao2)
INSERT INTO pokemon (id, nome, tipo, nivel, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES
(5, 'Ivyssauro',  'Planta', 15, 140, 65, 65, 60,  0, 9),
(6, 'Charmeleon', 'Fogo',   15, 130, 68, 60, 80,  0, 10),
(7, 'Wartortle',  'Agua',   15, 135, 65, 80, 58,  0, 11),
(8, 'Raichu',     'Raio',   20, 125, 75, 55, 105, 0, NULL);

INSERT INTO pokemon (id, nome, tipo, nivel, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES
(12, 'Weezing',   'Veneno', 20, 135, 70, 85, 55, 0, NULL),
(13, 'Beartic',   'Gelo',   20, 150, 78, 65, 50, 0, NULL),
(14, 'Exeggutor', 'Planta', 20, 155, 72, 75, 50, 0, NULL),
(15, 'Gyarados',  'Agua',   20, 150, 80, 70, 75, 0, NULL),
(16, 'Arcanine',  'Fogo',   20, 150, 78, 70, 85, 0, NULL);

-- Estágio 1 (Evolucao1)
INSERT INTO pokemon (id, nome, tipo, nivel, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES
(1, 'Bulbassauro', 'Planta', 5, 105, 50, 50, 45, 0, 5),
(2, 'Charmander',  'Fogo',   5, 95,  52, 45, 65, 0, 6),
(3, 'Squirtle',    'Agua',   5, 100, 48, 60, 45, 0, 7),
(4, 'Pikachu',     'Raio',   5, 85,  55, 40, 90, 0, 8);

INSERT INTO pokemon (id, nome, tipo, nivel, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES
(17, 'Koffing',   'Veneno', 5, 95,  55, 65, 40, 0, 12),
(18, 'Cubchoo',   'Gelo',   5, 100, 52, 45, 40, 0, 13),
(19, 'Zeraora',   'Raio',   5, 125, 65, 55, 95, 0, NULL),
(20, 'Exeggcute', 'Planta', 5, 110, 45, 70, 35, 0, 14),
(21, 'Magikarp',  'Agua',   5, 80,  30, 45, 60, 0, 15),
(22, 'Growlithe', 'Fogo',   5, 100, 55, 45, 60, 0, 16);
-- Itens obrigatórios do Poké Mart
INSERT INTO item (nome, descricao, preco, tipo) VALUES
-- Restauradores de HP
('Potion',        'Recupera 20 HP',                                50,  'restaurador'),
('Super Potion',  'Recupera 50 HP',                                100, 'restaurador'),
('Hyper Potion',  'Recupera 200 HP',                               250, 'restaurador'),
('Max Potion',    'Recupera 100% do HP',                           300, 'restaurador'),
('Full Restore',  'Recupera 100% do HP e cura todas as condições', 400, 'restaurador'),
-- Cura de condições
('Antidote',      'Cura envenenamento',          50,  'cura'),
('Burn Heal',     'Cura queimadura',             50,  'cura'),
('Ice Heal',      'Cura congelamento',           50,  'cura'),
('Awakening',     'Cura sono',                   50,  'cura'),
('Paralyze Heal', 'Cura paralisia',              50,  'cura'),
('Full Heal',     'Cura todas as condições',     150, 'cura'),
-- Reviver
('Revive',        'Revive um Pokémon desmaiado com metade da vida', 300, 'reviver'),
('Full Revive',        'Revive um Pokémon desmaiado com a vida completa', 500, 'reviver'),
-- Evolução
('Pedra de Evolução', 'Evolui um Pokémon para o próximo estágio',   1200, 'evolucao');


-- ==============================================================================
-- 3. VIEWS (3 views)
-- ==============================================================================

-- View 1: time de cada usuário (Team Builder e Batalha)
CREATE VIEW view_time_usuario AS
SELECT
    t.id AS time_id,
    t.usuario_id,
    u.nome AS usuario,
    p.id AS pokemon_id,
    p.nome AS pokemon,
    p.tipo,
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
INNER JOIN pokemon p ON p.id = t.pokemon_id
WHERE t.no_time = 1;

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
