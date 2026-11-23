<?php
session_start();

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../models/Conexao.php';

class LogController {

    public function listar($usuarioId) {
        $log = new Log(getConnection());
        return [
            'resultados' => $log->listarResultados($usuarioId),
            'batalha' => $log->listarBatalhas($usuarioId),
            'usuario' => $log->listarUsuario($usuarioId)
        ];
    }
}

// Todas as respostas são JSON
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Faça login primeiro']);
    exit;
}

$controller = new LogController();

$acao = '';
if (isset($_GET['acao'])) {
    $acao = $_GET['acao'];
}

if ($acao == 'listar') {
    echo json_encode($controller->listar($_SESSION['usuario_id']));
}
