<?php
session_start();

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../models/Conexao.php';

class TimeController {

    public function listar($usuarioId) {
        $pdo = getConnection();
        $time = new Time($pdo);

        return [
            'disponiveis' => $time->listarDisponiveis(),
            'time' => $time->listarDoUsuario($usuarioId)
        ];
    }

    // lança TimeCheioException se o time estiver cheio
    public function adicionar($usuarioId, $pokemonId) {
        $pdo = getConnection();
        $time = new Time($pdo);
        $time->adicionar($usuarioId, $pokemonId);
    }

    public function remover($usuarioId, $pokemonId) {
        $pdo = getConnection();
        $time = new Time($pdo);
        $time->remover($usuarioId, $pokemonId);
    }
}

// Todas as respostas são JSON
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Faça login primeiro']);
    exit;
}

$controller = new TimeController();
$usuarioId = $_SESSION['usuario_id'];

$acao = '';
if (isset($_GET['acao'])) {
    $acao = $_GET['acao'];
}

// o JavaScript envia os dados em JSON
$dados = json_decode(file_get_contents('php://input'), true);

try {
    if ($acao == 'listar') {
        echo json_encode($controller->listar($usuarioId));
    }

    if ($acao == 'adicionar') {
        $controller->adicionar($usuarioId, $dados['pokemon_id']);
        echo json_encode(['ok' => true]);
    }

    if ($acao == 'remover') {
        $controller->remover($usuarioId, $dados['pokemon_id']);
        echo json_encode(['ok' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}