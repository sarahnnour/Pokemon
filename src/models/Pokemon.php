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
    public function atacar($numero, $alvo) {
        if (!isset($this->ataques[$numero])) {
            throw new Exception("Ataque inválido");
        }
        $poder = $this->ataques[$numero]['poder'];
        $dano = intval(($this->ataque + $poder) / 3 - $alvo->getDefesa() / 4);
        if ($dano < 1) {
            $dano = 1;
        }
        $alvo->receberDano($dano);
        return $dano;
    }

    public function receberDano($dano) {
        $this->hp_atual = $this->hp_atual - $dano;
        if ($this->hp_atual < 0) {
            $this->hp_atual = 0;
        }
    }

    public function estaDesmaiado() {
        return $this->hp_atual == 0;
    }
}