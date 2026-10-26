<?php

class Item {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listar() {
        $sql = "SELECT * FROM item";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function carregar($id) {
        $sql = "SELECT * FROM item WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new Exception("Item não encontrado");
        }
        return $dados;
    }

    // Transação: se qualquer passo falhar, nada é cobrado nem aplicado
    public function comprar($usuarioId, $itemId, $pokemonId) {
        $this->pdo->beginTransaction();

        try {
            $item = $this->carregar($itemId);
            $this->cobrar($usuarioId, $item['preco']);
            $this->aplicar($item, $usuarioId, $pokemonId);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function cobrar($usuarioId, $preco) {
        $sql = "SELECT dinheiro FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $usuarioId]);
        $dinheiro = $stmt->fetchColumn();

        if ($dinheiro < $preco) {
            throw new SaldoInsuficienteException("Saldo insuficiente");
        }

        $sql = "UPDATE usuario SET dinheiro = dinheiro - :preco WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['preco' => $preco, 'id' => $usuarioId]);
    }

    // Aplica o efeito do item no Pokémon escolhido (usa a view_time_usuario)
    private function aplicar($item, $usuarioId, $pokemonId) {
        $sql = "SELECT * FROM view_time_usuario WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'pokemon_id' => $pokemonId]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$p) {
            throw new Exception("Escolha um Pokémon do seu time");
        }

        if ($item['tipo'] == 'restaurador') {
            $this->usarRestaurador($item, $p);
        }
        if ($item['tipo'] == 'cura') {
            $this->usarCura($item, $p);
        }
        if ($item['tipo'] == 'reviver') {
            $this->usarReviver($p);
        }
        if ($item['tipo'] == 'evolucao') {
            $this->usarEvolucao($p);
        }
    }

    private function usarRestaurador($item, $p) {
        if ($p['hp_atual'] == 0) {
            throw new Exception("Pokémon desmaiado. Use um Revive");
        }
        if ($p['hp_atual'] >= $p['hp_maximo']) {
            throw new Exception("O HP já está cheio");
        }

        // Max Potion e Full Restore recuperam tudo
        $hp = $p['hp_maximo'];
        if ($item['nome'] == 'Potion') {
            $hp = $p['hp_atual'] + 20;
        }
        if ($item['nome'] == 'Super Potion') {
            $hp = $p['hp_atual'] + 50;
        }
        if ($item['nome'] == 'Hyper Potion') {
            $hp = $p['hp_atual'] + 200;
        }

        $status = $p['status_condicao'];
        if ($item['nome'] == 'Full Restore') {
            $status = 'Normal';
        }

        // o trigger trigger_limite_hp não deixa passar do HP máximo
        $this->atualizarTime($p, $hp, $status);
    }

    private function usarCura($item, $p) {
        // Full Heal não está nesta lista: cura qualquer condição
        $curas = [
            'Antidote'      => 'Envenenado',
            'Burn Heal'     => 'Queimado',
            'Ice Heal'      => 'Congelado',
            'Awakening'     => 'Dormindo',
            'Paralyze Heal' => 'Paralisado'
        ];

        if ($p['status_condicao'] == 'Normal') {
            throw new Exception("O Pokémon não tem nenhuma condição");
        }
        if (isset($curas[$item['nome']]) && $curas[$item['nome']] != $p['status_condicao']) {
            throw new Exception("Esse item não cura a condição do Pokémon");
        }

        $this->atualizarTime($p, $p['hp_atual'], 'Normal');
    }

    private function usarReviver($p) {
        if ($p['hp_atual'] > 0) {
            throw new Exception("Esse Pokémon não está desmaiado");
        }

        $hp = intval($p['hp_maximo'] / 2);
        $this->atualizarTime($p, $hp, $p['status_condicao']);
    }

    private function usarEvolucao($p) {
        if ($p['proxima_evolucao_id'] == null) {
            throw new Exception("Esse Pokémon não pode evoluir");
        }

        // o mesmo Pokémon não pode aparecer duas vezes no time
        $sql = "SELECT COUNT(*) FROM time WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $p['usuario_id'], 'pokemon_id' => $p['proxima_evolucao_id']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("A evolução já está no time");
        }

        // o HP atual ganha a diferença entre o HP máximo novo e o antigo
        $sql = "SELECT hp_maximo FROM pokemon WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $p['proxima_evolucao_id']]);
        $novoMaximo = $stmt->fetchColumn();
        $hp = $p['hp_atual'] + ($novoMaximo - $p['hp_maximo']);

        $sql = "UPDATE time SET pokemon_id = :novo_id, hp_atual = :hp
                WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'novo_id' => $p['proxima_evolucao_id'],
            'hp' => $hp,
            'usuario_id' => $p['usuario_id'],
            'pokemon_id' => $p['pokemon_id']
        ]);
    }

    private function atualizarTime($p, $hp, $status) {
        $sql = "UPDATE time SET hp_atual = :hp, status_condicao = :status
                WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'hp' => $hp,
            'status' => $status,
            'usuario_id' => $p['usuario_id'],
            'pokemon_id' => $p['pokemon_id']
        ]);
    }
}