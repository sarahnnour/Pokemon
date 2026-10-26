<?php

class Pokemon {

    private $pdo;
    private $id;
    private $nome;
    private $tipo;
    private $nivel;
    private $hp_maximo;
    private $hp_atual;
    private $ataque;
    private $defesa;
    private $velocidade;
    private $prioridade;
    private $proxima_evolucao_id;
    private $ataques = [];
    // Normal, Envenenado, Queimado ou Congelado (mesmos nomes da coluna status_condicao)
    private $status = 'Normal';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function getNivel() {
        return $this->nivel;
    }

    public function setNivel($nivel) {
        $this->nivel = $nivel;
    }

    public function getHpMaximo() {
        return $this->hp_maximo;
    }

    public function setHpMaximo($hp_maximo) {
        $this->hp_maximo = $hp_maximo;
        $this->hp_atual = $hp_maximo;
    }

    public function getHpAtual() {
        return $this->hp_atual;
    }

    public function setHpAtual($hp_atual) {
        $this->hp_atual = $hp_atual;
    }

    public function getAtaque() {
        return $this->ataque;
    }

    public function setAtaque($ataque) {
        $this->ataque = $ataque;
    }

    public function getDefesa() {
        return $this->defesa;
    }

    public function setDefesa($defesa) {
        $this->defesa = $defesa;
    }

    public function getVelocidade() {
        return $this->velocidade;
    }

    public function setVelocidade($velocidade) {
        $this->velocidade = $velocidade;
    }

    public function getPrioridade() {
        return $this->prioridade;
    }

    public function setPrioridade($prioridade) {
        $this->prioridade = $prioridade;
    }

    public function getProximaEvolucaoId() {
        return $this->proxima_evolucao_id;
    }

    public function setProximaEvolucaoId($proxima_evolucao_id) {
        $this->proxima_evolucao_id = $proxima_evolucao_id;
    }

    public function getAtaques() {
        return $this->ataques;
    }

    public function setAtaques($ataques) {
        $this->ataques = $ataques;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }


    public function create($id, $nome, $tipo, $nivel, $hp_maximo, $ataque, $defesa, $velocidade, $prioridade, $proxima_evolucao_id) {
        $sql = "INSERT INTO pokemon (id, nome, tipo, nivel, hp_maximo, ataque, defesa, velocidade, prioridade, proxima_evolucao_id) VALUES (:id, :nome, :tipo, :nivel, :hp_maximo, :ataque, :defesa, :velocidade, :prioridade, :proxima_evolucao_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'tipo' => $tipo,
            'nivel' => $nivel,
            'hp_maximo' => $hp_maximo,
            'ataque' => $ataque,
            'defesa' => $defesa,
            'velocidade' => $velocidade,
            'prioridade' => $prioridade,
            'proxima_evolucao_id' => $proxima_evolucao_id
        ]);
    }

    public function read() {
        $sql = "SELECT * FROM pokemon";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $nome, $tipo, $nivel, $hp_maximo, $ataque, $defesa, $velocidade, $prioridade, $proxima_evolucao_id) {
        $sql = "UPDATE pokemon SET nome = :nome, tipo = :tipo, nivel = :nivel, hp_maximo = :hp_maximo, ataque = :ataque, defesa = :defesa, velocidade = :velocidade, prioridade = :prioridade, proxima_evolucao_id = :proxima_evolucao_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'tipo' => $tipo,
            'nivel' => $nivel,
            'hp_maximo' => $hp_maximo,
            'ataque' => $ataque,
            'defesa' => $defesa,
            'velocidade' => $velocidade,
            'prioridade' => $prioridade,
            'proxima_evolucao_id' => $proxima_evolucao_id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM pokemon WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function carregar($id) {
        $sql = "SELECT * FROM pokemon WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new Exception("Pokémon não encontrado");
        }

        $this->id = $dados['id'];
        $this->nome = $dados['nome'];
        $this->tipo = $dados['tipo'];
        $this->nivel = $dados['nivel'];
        $this->setHpMaximo($dados['hp_maximo']);
        $this->ataque = $dados['ataque'];
        $this->defesa = $dados['defesa'];
        $this->velocidade = $dados['velocidade'];
        $this->prioridade = $dados['prioridade'];
        $this->proxima_evolucao_id = $dados['proxima_evolucao_id'];
    }

    // Batalha

    // Retorna quanto o tipo do ataque multiplica o dano contra o tipo do defensor
    // 2 = super efetivo, 0.5 = pouco efetivo, 1 = normal
    // Usa getForteContra() e getFracoContra() do trait do tipo do Pokémon que ataca
    public function calcularMultiplicador($tipoAtaque, $tipoDefensor) {
        // Ataque Normal não tem vantagem nem desvantagem
        if ($tipoAtaque == 'Normal') {
            return 1;
        }

        if (in_array($tipoDefensor, $this->getForteContra())) {
            return 2;
        }
        if (in_array($tipoDefensor, $this->getFracoContra())) {
            return 0.5;
        }

        return 1;
    }

    public function atacar($numero, $alvo) {
        if (!isset($this->ataques[$numero])) {
            throw new Exception("Ataque inválido");
        }

        $golpe = $this->ataques[$numero];

        // usos = null é ilimitado; senão precisa ter usos restantes
        if ($golpe['usos'] !== null && $golpe['usos'] <= 0) {
            throw new Exception("Esse ataque não tem mais usos");
        }

        $multiplicador = $this->calcularMultiplicador($golpe['tipo'], $alvo->getTipo());

        $dano = intval((($this->ataque + $golpe['poder']) / 3 - $alvo->getDefesa() / 4) * $multiplicador);
        if ($dano < 1) {
            $dano = 1;
        }

        // Gasta um uso do ataque (se for limitado)
        if ($golpe['usos'] !== null) {
            $this->ataques[$numero]['usos'] = $golpe['usos'] - 1;
        }

        $alvo->receberDano($dano);

        // Alguns ataques têm efeito: 'congelar', 'envenenar' ou 'queimar'
        if (isset($golpe['efeito'])) {
            $alvo->receberEfeito($golpe['efeito']);
        }

        return $dano;
    }

    public function receberDano($dano) {
        $this->hp_atual = $this->hp_atual - $dano;
        if ($this->hp_atual < 0) {
            $this->hp_atual = 0;
        }
    }

    // Aplica o efeito de um ataque no Pokémon
    public function receberEfeito($efeito) {
        // só uma condição por vez
        if ($this->status != 'Normal') {
            return;
        }

        if ($efeito == 'congelar') {
            $this->status = 'Congelado';
            // reduz a velocidade pela metade (mínimo 1)
            $this->velocidade = intval($this->velocidade / 2);
            if ($this->velocidade < 1) {
                $this->velocidade = 1;
            }
        }
        if ($efeito == 'envenenar') {
            $this->status = 'Envenenado';
        }
        if ($efeito == 'queimar') {
            $this->status = 'Queimado';
        }
    }

    // A Batalha chama este método no fim de cada rodada, só para o Pokémon que está em campo.
    // Envenenado e Queimado perdem 1/8 do HP máximo. Retorna o dano causado.
    //Mudar depois para cada evolução do pokemon causar um dano diferente de envenenamento (também deve estar conforme o nível)
    public function aplicarDanoDeStatus() {
        $dano = 0;

        if ($this->status == 'Envenenado' || $this->status == 'Queimado') {
            $dano = intval($this->hp_maximo / 8);
            if ($dano < 1) {
                $dano = 1;
            }
            $this->receberDano($dano);
        }

        return $dano;
    }

    public function estaDesmaiado() {
        return $this->hp_atual == 0;
    }
}
