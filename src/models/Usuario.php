<?php

class Usuario {

    private $pdo;
    private $id;
    private $nome;
    private $senha;
    private $email;
    private $dinheiro;

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

    public function getSenha() {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getDinheiro() {
        return $this->dinheiro;
    }

    public function setDinheiro($dinheiro) {
        $this->dinheiro = $dinheiro;
    }

    public function create($nome, $senha, $email) {
        $sql = "INSERT INTO usuario (nome, senha, email) VALUES (:nome, :senha, :email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nome' => $nome,
            'senha' => $senha,
            'email' => $email
        ]);
    }

    public function read() {
        $sql = "SELECT * FROM usuario";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $nome, $senha, $email, $dinheiro) {
        $sql = "UPDATE usuario SET nome = :nome, senha = :senha, email = :email, dinheiro = :dinheiro WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'senha' => $senha,
            'email' => $email,
            'dinheiro' => $dinheiro
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function carregar($id) {
        $sql = "SELECT * FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new UsuarioNaoEncontradoException("Usuário não encontrado");
        }

        $this->id = $dados['id'];
        $this->nome = $dados['nome'];
        $this->senha = $dados['senha'];
        $this->email = $dados['email'];
        $this->dinheiro = $dados['dinheiro'];
    }

    public function buscarLogin($nome, $senha) {
        $sql = "SELECT * FROM usuario WHERE nome = :nome AND senha = :senha";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nome' => $nome, 'senha' => $senha]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new UsuarioNaoEncontradoException("Nome ou senha incorretos");
        }
        return $dados;
    }
}