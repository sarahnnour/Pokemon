<?php

// Log: guarda na tabela log_acao tudo o que acontece no jogo
// (cada evento da batalha e cada ação do usuário), com data e IP.
class Log {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // $usuarioId pode ser null (ex: tentativa de login que falhou)
    public function registrar($usuarioId, $tipo, $descricao) {
        $sql = "INSERT INTO log_acao (usuario_id, tipo, descricao, ip) VALUES (:usuario_id, :tipo, :descricao, :ip)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'descricao' => $descricao,
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }

    // Resultado de cada batalha (usa a view_historico_batalhas, que lê a tabela log_batalha)
    public function listarResultados($usuarioId) {
        $sql = "SELECT * FROM view_historico_batalhas WHERE usuario_id = :usuario_id ORDER BY data_batalha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Logs das batalhas do usuário, do mais novo para o mais antigo
    public function listarBatalhas($usuarioId) {
        $sql = "SELECT tipo, descricao, data_acao FROM log_acao WHERE usuario_id = :usuario_id AND tipo = 'batalha' ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Logs do usuário fora da batalha (login, compras, time, bolsa e erros)
    public function listarUsuario($usuarioId) {
        $sql = "SELECT tipo, descricao, data_acao FROM log_acao WHERE usuario_id = :usuario_id AND tipo <> 'batalha' ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
