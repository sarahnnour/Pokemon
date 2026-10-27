<?php

// Batalha 1x1: o Pokémon do jogador contra um adversário sorteado do mesmo nível.
// A Batalha TEM dois Pokémon (composição).
class Batalha {

    private $pdo;
    private $usuarioId;
    private $jogador;
    private $inimigo;
    private $terminou = false;
    private $resultado = '';
    private $dinheiroGanho = 0;
    private $recompensa = 200;
    private $mensagens = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function terminou() {
        return $this->terminou;
    }

    // Cria o objeto da classe do Pokémon (Pikachu, Squirtle...) pelo nome
    private function criarPokemon($nome) {
        if (!class_exists($nome)) {
            throw new Exception("Pokémon sem classe: " . $nome);
        }
        return new $nome($this->pdo);
    }

    // Começa a batalha com um Pokémon do time do usuário (usa a view_time_usuario)
    public function iniciar($usuarioId, $pokemonId) {
        $sql = "SELECT * FROM view_time_usuario WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'pokemon_id' => $pokemonId]);
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$linha) {
            throw new Exception("Escolha um Pokémon do seu time");
        }
        if ($linha['hp_atual'] == 0) {
            throw new Exception("Pokémon desmaiado. Use um Revive na loja");
        }

        $this->usuarioId = $usuarioId;

        $this->jogador = $this->criarPokemon($linha['pokemon']);
        $this->jogador->setHpAtual($linha['hp_atual']);
        $this->jogador->setStatus($linha['status_condicao']);

        // adversário: sorteia um Pokémon do mesmo nível
        $sql = "SELECT nome FROM pokemon WHERE nivel = :nivel ORDER BY RAND() LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nivel' => $this->jogador->getNivel()]);
        $this->inimigo = $this->criarPokemon($stmt->fetchColumn());

        $this->mensagens[] = $this->jogador->getNome() . ' enfrenta ' . $this->inimigo->getNome() . '!';
    }

    // Uma rodada: o jogador escolhe o ataque, o adversário sorteia o dele
    public function rodada($numero) {
        if ($this->terminou) {
            throw new Exception("A batalha já terminou");
        }

        $this->mensagens = [];
        $this->validarAtaque($numero);
        $numeroInimigo = $this->escolherAtaqueInimigo();

        // quem tem maior prioridade ataca primeiro; empate: maior velocidade
        if ($this->jogadorPrimeiro()) {
            $this->turno($this->jogador, $this->inimigo, $numero);
            if (!$this->inimigo->estaDesmaiado()) {
                $this->turno($this->inimigo, $this->jogador, $numeroInimigo);
            }
        } else {
            $this->turno($this->inimigo, $this->jogador, $numeroInimigo);
            if (!$this->jogador->estaDesmaiado()) {
                $this->turno($this->jogador, $this->inimigo, $numero);
            }
        }

        $this->aplicarStatus($this->jogador);
        $this->aplicarStatus($this->inimigo);

        if ($this->inimigo->estaDesmaiado()) {
            $this->terminar('vitoria');
        } elseif ($this->jogador->estaDesmaiado()) {
            $this->terminar('derrota');
        }
    }

    // Confere o ataque do jogador ANTES de qualquer um atacar
    private function validarAtaque($numero) {
        $ataques = $this->jogador->getAtaques();

        if (!isset($ataques[$numero])) {
            throw new Exception("Ataque inválido");
        }
        if ($ataques[$numero]['usos'] !== null && $ataques[$numero]['usos'] <= 0) {
            throw new Exception("Esse ataque não tem mais usos");
        }
    }

    // O adversário sorteia entre os ataques que ainda têm usos
    private function escolherAtaqueInimigo() {
        $ataques = $this->inimigo->getAtaques();
        $disponiveis = [];

        for ($i = 0; $i < count($ataques); $i++) {
            if ($ataques[$i]['usos'] === null || $ataques[$i]['usos'] > 0) {
                $disponiveis[] = $i;
            }
        }

        return $disponiveis[rand(0, count($disponiveis) - 1)];
    }

    private function jogadorPrimeiro() {
        if ($this->jogador->getPrioridade() != $this->inimigo->getPrioridade()) {
            return $this->jogador->getPrioridade() > $this->inimigo->getPrioridade();
        }
        return $this->jogador->getVelocidade() >= $this->inimigo->getVelocidade();
    }

    private function turno($atacante, $alvo, $numero) {
        $ataques = $atacante->getAtaques();
        $golpe = $ataques[$numero];

        $dano = $atacante->atacar($numero, $alvo);
        $this->mensagens[] = $atacante->getNome() . ' usou ' . $golpe['nome'] . ' e causou ' . $dano . ' de dano.';

        if (isset($golpe['efeito'])) {
            $this->mensagens[] = 'Condição de ' . $alvo->getNome() . ': ' . $alvo->getStatus() . '.';
        }
        if ($alvo->estaDesmaiado()) {
            $this->mensagens[] = $alvo->getNome() . ' desmaiou!';
        }
    }

    // Fim da rodada: Envenenado e Queimado perdem HP
    private function aplicarStatus($pokemon) {
        if ($pokemon->estaDesmaiado()) {
            return;
        }

        $dano = $pokemon->aplicarDanoDeStatus();
        if ($dano > 0) {
            $this->mensagens[] = $pokemon->getNome() . ' perdeu ' . $dano . ' de HP por estar ' . $pokemon->getStatus() . '.';
            if ($pokemon->estaDesmaiado()) {
                $this->mensagens[] = $pokemon->getNome() . ' desmaiou!';
            }
        }
    }

    private function terminar($resultado) {
        $this->terminou = true;
        $this->resultado = $resultado;

        if ($resultado == 'vitoria') {
            $this->dinheiroGanho = $this->recompensa;
            $this->mensagens[] = 'Você venceu e ganhou ' . $this->recompensa . ' pokédollar!';
        } else {
            $this->mensagens[] = 'Você perdeu a batalha.';
        }

        $this->salvar();
    }

    // Transação: salva o HP/condição do Pokémon e registra o log.
    // O dinheiro da vitória é somado pelo trigger_dinheiro_vitoria (não somar aqui!)
    private function salvar() {
        $this->pdo->beginTransaction();

        try {
            $sql = "UPDATE time SET hp_atual = :hp, status_condicao = :status
                    WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'hp' => $this->jogador->getHpAtual(),
                'status' => $this->jogador->getStatus(),
                'usuario_id' => $this->usuarioId,
                'pokemon_id' => $this->jogador->getId()
            ]);

            $sql = "INSERT INTO log_batalha (usuario_id, pokemon_id, adversario, resultado, dinheiro_ganho)
                    VALUES (:usuario_id, :pokemon_id, :adversario, :resultado, :dinheiro_ganho)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'usuario_id' => $this->usuarioId,
                'pokemon_id' => $this->jogador->getId(),
                'adversario' => $this->inimigo->getNome(),
                'resultado' => $this->resultado,
                'dinheiro_ganho' => $this->dinheiroGanho
            ]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Guardar / recuperar a batalha na sessão entre uma requisição e outra
    public function paraArray() {
        return [
            'usuario_id' => $this->usuarioId,
            'jogador' => $this->pokemonParaArray($this->jogador),
            'inimigo' => $this->pokemonParaArray($this->inimigo)
        ];
    }

    public function restaurar($dados) {
        $this->usuarioId = $dados['usuario_id'];
        $this->jogador = $this->pokemonDeArray($dados['jogador']);
        $this->inimigo = $this->pokemonDeArray($dados['inimigo']);
    }

    private function pokemonParaArray($p) {
        return [
            'nome' => $p->getNome(),
            'hp' => $p->getHpAtual(),
            'velocidade' => $p->getVelocidade(),
            'status' => $p->getStatus(),
            'ataques' => $p->getAtaques()
        ];
    }

    private function pokemonDeArray($d) {
        $p = $this->criarPokemon($d['nome']);
        $p->setHpAtual($d['hp']);
        $p->setVelocidade($d['velocidade']);
        $p->setStatus($d['status']);
        $p->setAtaques($d['ataques']);
        return $p;
    }

    // O que o JavaScript recebe (JSON)
    public function getEstado() {
        return [
            'jogador' => $this->resumo($this->jogador),
            'inimigo' => $this->resumo($this->inimigo),
            'mensagens' => $this->mensagens,
            'terminou' => $this->terminou,
            'resultado' => $this->resultado,
            'dinheiro_ganho' => $this->dinheiroGanho
        ];
    }

    private function resumo($p) {
        return [
            'nome' => $p->getNome(),
            'tipo' => $p->getTipo(),
            'hp_atual' => $p->getHpAtual(),
            'hp_maximo' => $p->getHpMaximo(),
            'status' => $p->getStatus(),
            'ataques' => $p->getAtaques()
        ];
    }
}