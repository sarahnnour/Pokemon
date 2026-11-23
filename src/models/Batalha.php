<?php

// Batalha: o Pokémon do jogador contra uma fila de adversários (1 a 3 Pokémon).
// A dificuldade (quantidade e evolução dos adversários) depende das vitórias do usuário.
// A Batalha TEM Pokémon (composição).
class Batalha {

    private $pdo;
    private $usuarioId;
    private $jogador;
    private $inimigo;
    private $fila = [];      // nomes dos próximos adversários (ainda não lutaram)
    private $acoes = [];     // ataques feitos na rodada, na ordem em que aconteceram
    private $terminou = false;
    private $resultado = '';
    private $dinheiroGanho = 0;
    private $recompensa = 250;
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

    // Busca um Pokémon do time do usuário (usa a view_time_usuario)
    private function buscarNoTime($pokemonId) {
        $sql = "SELECT * FROM view_time_usuario WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $this->usuarioId, 'pokemon_id' => $pokemonId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Começa a batalha com um Pokémon do time do usuário
    public function iniciar($usuarioId, $pokemonId) {
        $this->usuarioId = $usuarioId;

        $linha = $this->buscarNoTime($pokemonId);

        if (!$linha) {
            throw new Exception("Escolha um Pokémon do seu time");
        }
        if ($linha['hp_atual'] == 0) {
            throw new Exception("Pokémon desmaiado. Use um Revive na loja");
        }

        $this->jogador = $this->criarPokemon($linha['pokemon']);
        $this->jogador->restaurarEstado($linha['hp_atual'], $linha['status_condicao']);

        $this->montarAdversarios();

        $this->mensagens[] = $this->jogador->getNome() . ' enfrenta ' . $this->inimigo->getNome() . '!';
    }

    // Dificuldade = vitórias / 5 (máximo 5). Número = estágio de evolução de cada adversário:
    // 0: [1]    1: [1,1]    2: [1,1,1]
    // 3: [2,1,1]    4: [2,2,1]    5: [2,2,2]
    private function montarAdversarios() {
        // vitórias do usuário (usa a view_vitorias_usuario; sem vitórias não tem linha)
        $sql = "SELECT vitorias FROM view_vitorias_usuario WHERE usuario_id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $this->usuarioId]);
        $vitorias = intval($stmt->fetchColumn());

        $nivel = intval($vitorias / 5);
        if ($nivel > 5) {
            $nivel = 5;
        }

        // quantidade de adversários: 1, 2 ou 3
        $quantidade = $nivel + 1;
        if ($quantidade > 3) {
            $quantidade = 3;
        }

        // todos começam no estágio 1
        $estagios = [];
        for ($i = 0; $i < $quantidade; $i++) {
            $estagios[] = 1;
        }

        // a partir do nível 3, um estágio é somado por vez (1º, 2º, 3º)
        $evolucoes = $nivel - 2;
        for ($i = 0; $i < $evolucoes; $i++) {
            $estagios[$i % 3] = $estagios[$i % 3] + 1;
        }

        $nomes = [];
        for ($i = 0; $i < count($estagios); $i++) {
            $nomes[] = $this->sortearAdversario($estagios[$i]);
        }

        $this->inimigo = $this->criarPokemon(array_shift($nomes));
        $this->fila = $nomes;

        // prêmio: 250 por adversário + 50 por nível (250 até 1000)
        $this->recompensa = 250 * $quantidade + $nivel * 50;
    }

    // Sorteia um Pokémon inicial (nível 5) e evolui até o estágio pedido.
    // Se ele não tiver tantas evoluções, fica na última que existe.
    private function sortearAdversario($estagio) {
        $sql = "SELECT nome, proxima_evolucao_id FROM pokemon WHERE nivel = 5 ORDER BY RAND() LIMIT 1";
        $stmt = $this->pdo->query($sql);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);

        for ($i = 1; $i < $estagio; $i++) {
            if ($p['proxima_evolucao_id'] == null) {
                break;
            }
            $sql = "SELECT nome, proxima_evolucao_id FROM pokemon WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $p['proxima_evolucao_id']]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $p['nome'];
    }

    // Uma rodada: o jogador escolhe o ataque, o adversário sorteia o dele
    public function rodada($numero) {
        if ($this->terminou) {
            throw new Exception("A batalha já terminou");
        }
        if ($this->jogador->estaDesmaiado()) {
            throw new Exception("Troque de Pokémon");
        }

        $this->mensagens = [];
        $this->acoes = [];
        $this->validarAtaque($numero);
        $numeroInimigo = $this->escolherAtaqueInimigo();

        // quem tem maior prioridade ataca primeiro; empate: maior velocidade.
        // Quem foi derrotado antes de atacar não ataca.
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

        $this->fimDeRodada();
    }

    // Fim da rodada (ataque, troca ou item): status, salvar o HP e ver quem desmaiou
    private function fimDeRodada() {
        $this->aplicarStatus($this->jogador);
        $this->aplicarStatus($this->inimigo);

        // o HP do jogador fica sempre salvo no banco (os itens usam o banco)
        $this->salvarPokemon();

        if ($this->inimigo->estaDesmaiado()) {
            $this->proximoAdversario();
        }
        if (!$this->terminou && $this->jogador->estaDesmaiado()) {
            $this->verificarDesmaio();
        }
    }

    // O adversário desmaiou: entra o próximo da fila. Se não tem mais, é vitória.
    private function proximoAdversario() {
        if (count($this->fila) == 0) {
            $this->terminar('vitoria');
            return;
        }

        $this->inimigo = $this->criarPokemon(array_shift($this->fila));
        $this->mensagens[] = 'Entrou o adversário ' . $this->inimigo->getNome() . '!';
    }

    // Pokémon do jogador desmaiou: se ainda houver outro vivo no time, a batalha continua
    // e o jogador escolhe quem entra. Se não houver, é derrota.
    private function verificarDesmaio() {
        $sql = "SELECT COUNT(*) FROM time WHERE usuario_id = :usuario_id AND hp_atual > 0 AND no_time = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $this->usuarioId]);

        if ($stmt->fetchColumn() == 0) {
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

        // o JavaScript usa esta lista para mostrar quem atacou, na ordem certa
        $quem = 'jogador';
        if ($atacante === $this->inimigo) {
            $quem = 'inimigo';
        }
        $this->acoes[] = ['quem' => $quem, 'pokemon' => $atacante->getNome(), 'golpe' => $golpe['nome']];

        if (isset($golpe['efeito']) && $alvo->getStatus() != 'Normal') {
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

    private function salvarPokemon() {
        $sql = "UPDATE time SET hp_atual = :hp, status_condicao = :status
                WHERE usuario_id = :usuario_id AND pokemon_id = :pokemon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'hp' => $this->jogador->getHpAtual(),
            'status' => $this->jogador->getStatus(),
            'usuario_id' => $this->usuarioId,
            'pokemon_id' => $this->jogador->getId()
        ]);
    }

    // Transação: salva o HP/condição do Pokémon e registra o log.
    // O dinheiro da vitória é somado pelo trigger_dinheiro_vitoria (não somar aqui!)
    private function salvar() {
        $this->pdo->beginTransaction();

        try {

            $this->salvarPokemon();

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

    // Troca o Pokémon em campo.
    // Troca normal: gasta o turno e o adversário ataca.
    // Troca depois de desmaiar: não gasta turno.
    public function trocar($pokemonId) {
        if ($this->terminou) {
            throw new Exception("A batalha já terminou");
        }
        if ($pokemonId == $this->jogador->getId()) {
            throw new Exception("Esse Pokémon já está em campo");
        }

        $linha = $this->buscarNoTime($pokemonId);

        if (!$linha) {
            throw new Exception("Escolha um Pokémon do seu time");
        }
        if ($linha['hp_atual'] == 0) {
            throw new Exception("Pokémon desmaiado");
        }

        $this->mensagens = [];
        $this->acoes = [];
        $estavaDesmaiado = $this->jogador->estaDesmaiado();

        // guarda o HP e a condição do Pokémon que está saindo
        $this->salvarPokemon();

        $this->jogador = $this->criarPokemon($linha['pokemon']);
        $this->jogador->restaurarEstado($linha['hp_atual'], $linha['status_condicao']);
        $this->mensagens[] = 'Você trocou para ' . $this->jogador->getNome() . '!';

        if (!$estavaDesmaiado) {
            $this->turnoDoInimigo();
        }
    }

    // Usa um item da bolsa em um Pokémon do time. Só itens de batalha (HP, cura e reviver).
    // Gasta o turno (o adversário ataca), a não ser que o Pokémon em campo esteja desmaiado.
    public function usarItem($itemId, $pokemonId) {
        if ($this->terminou) {
            throw new Exception("A batalha já terminou");
        }

        $item = new Item($this->pdo);
        $dados = $item->carregar($itemId);

        if ($dados['tipo'] == 'evolucao') {
            throw new Exception("Esse item não pode ser usado em batalha");
        }

        $this->mensagens = [];
        $this->acoes = [];
        $estavaDesmaiado = $this->jogador->estaDesmaiado();

        // o Item mexe no banco (o HP do jogador já está salvo lá). Se falhar, a unidade volta para a bolsa.
        $item->usar($this->usuarioId, $itemId, $pokemonId);
        $this->mensagens[] = 'Você usou ' . $dados['nome'] . '!';

        // se o item foi no Pokémon em campo, atualiza o HP e a condição dele
        if ($pokemonId == $this->jogador->getId()) {
            $linha = $this->buscarNoTime($pokemonId);
            $this->jogador->setHpAtual($linha['hp_atual']);
            $this->jogador->setStatus($linha['status_condicao']);

            // curou o congelamento: a velocidade volta ao normal
            if ($linha['status_condicao'] == 'Normal') {
                $this->jogador->setVelocidade($linha['velocidade']);
            }
        }

        if (!$estavaDesmaiado) {
            $this->turnoDoInimigo();
        }
    }

    // O adversário ataca (usado quando o jogador gasta o turno com troca ou item)
    private function turnoDoInimigo() {
        $this->turno($this->inimigo, $this->jogador, $this->escolherAtaqueInimigo());
        $this->fimDeRodada();
    }

    // Guardar / recuperar a batalha na sessão entre uma requisição e outra
    public function paraArray() {
        return [
            'usuario_id' => $this->usuarioId,
            'jogador' => $this->pokemonParaArray($this->jogador),
            'inimigo' => $this->pokemonParaArray($this->inimigo),
            'fila' => $this->fila,
            'recompensa' => $this->recompensa
        ];
    }

    public function restaurar($dados) {
        $this->usuarioId = $dados['usuario_id'];
        $this->jogador = $this->pokemonDeArray($dados['jogador']);
        $this->inimigo = $this->pokemonDeArray($dados['inimigo']);
        $this->fila = $dados['fila'];
        $this->recompensa = $dados['recompensa'];
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
            'inimigos_restantes' => count($this->fila),
            'mensagens' => $this->mensagens,
            'terminou' => $this->terminou,
            'resultado' => $this->resultado,
            'dinheiro_ganho' => $this->dinheiroGanho,
            'acoes' => $this->acoes,
            'precisa_trocar' => !$this->terminou && $this->jogador->estaDesmaiado()
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