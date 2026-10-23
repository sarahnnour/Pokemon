<?php

class Time {

    private $pdo;
    private $limite = 3;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Pokémon que o jogador pode escolher: só os iniciais (estágio 1, nível 5).
    // As evoluções vêm pela loja.
    public function listarDisponiveis() {
        $sql = "SELECT * FROM pokemon WHERE nivel = 5";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Usa a view_time_usuario
    public function listarDoUsuario($usuarioId) {
        $sql = "SELECT * FROM view_time_usuario WHERE usuario_id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar($usuarioId) {
        $sql = "SELECT COUNT(*) FROM time WHERE usuario_id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchColumn();
    }

    public function adicionar($usuarioId, $pokemonId) {
        if ($this->contar($usuarioId) >= $this->limite) {
            throw new TimeCheioException("O time já tem " . $this->limite . " Pokémon");
        }

        // o mesmo Pokémon não pode entrar duas vezes
        $sql = "SELECT COUNT(*) FROM time WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'pokemon_id' => $pokemonId]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Esse Pokémon já está no time");
        }

        // carrega o Pokémon (lança Exception se não existir) para saber o HP máximo
        $pokemon = new Pokemon($this->pdo);
        $pokemon->carregar($pokemonId);

        $sql = "INSERT INTO time (usuario_id, pokemon_id, hp_atual) VALUES (:usuario_id, :pokemon_id, :hp_atual)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'pokemon_id' => $pokemonId,
            'hp_atual' => $pokemon->getHpMaximo()
        ]);
    }

    public function remover($usuarioId, $pokemonId) {
        $sql = "DELETE FROM time WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'pokemon_id' => $pokemonId]);
    }
}