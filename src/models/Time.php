<?php

class Time {

    private $pdo;
    private $limite = 3;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Pokémon que o jogador pode escolher: só os iniciais (estágio 1, nível 5).
    // As evoluções vêm pela loja.

    // Usa a view_time_usuario
    public function listarDoUsuario($usuarioId) {
        $sql = "SELECT * FROM view_time_usuario WHERE usuario_id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Disponíveis = Pokémon guardados (com a evolução que já tinham)
// + iniciais que o usuário ainda não tem
public function listarDisponiveis($usuarioId) {
    $sql = "SELECT p.* FROM pokemon p
            INNER JOIN time t ON t.pokemon_id = p.id
            WHERE t.usuario_id = :usuario_id AND t.no_time = 0";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuarioId]);
    $guardados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT p.* FROM pokemon p
            LEFT JOIN time t ON t.pokemon_id = p.id AND t.usuario_id = :usuario_id
            WHERE p.nivel = 5 AND t.id IS NULL";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuarioId]);
    $iniciais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_merge($guardados, $iniciais);
}

public function contar($usuarioId) {
    $sql = "SELECT COUNT(*) FROM time WHERE usuario_id = :usuario_id AND no_time = 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuarioId]);
    return $stmt->fetchColumn();
}

public function adicionar($usuarioId, $pokemonId) {
    if ($this->contar($usuarioId) >= $this->limite) {
        throw new TimeCheioException("O time já tem " . $this->limite . " Pokémon");
    }

    // procura o Pokémon do usuário (no time ou guardado)
    $sql = "SELECT no_time FROM time WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuarioId, 'pokemon_id' => $pokemonId]);
    $linha = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($linha) {
        if ($linha['no_time'] == 1) {
            throw new Exception("Esse Pokémon já está no time");
        }

        // estava guardado: volta com o HP, a condição e a evolução que tinha
        $sql = "UPDATE time SET no_time = 1 WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'pokemon_id' => $pokemonId]);
        return;
    }

    // Pokémon novo: carrega (lança Exception se não existir) para saber o HP máximo
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

// Não apaga: só guarda o Pokémon (mantém HP, condição e evolução)
public function remover($usuarioId, $pokemonId) {
    $sql = "UPDATE time SET no_time = 0 WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuarioId, 'pokemon_id' => $pokemonId]);
}
}