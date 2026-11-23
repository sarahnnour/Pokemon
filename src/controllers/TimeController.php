<?php
session_start();

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../models/Conexao.php';

class TimeController {

    public function listar($usuarioId) {
        $pdo = getConnection();
        $time = new Time($pdo);

        return [
            'disponiveis' => $time->listarDisponiveis($usuarioId),
            'time' => $time->listarDoUsuario($usuarioId),
        ];
    }

    // lança TimeCheioException se o time estiver cheio
    public function adicionar($usuarioId, $pokemonId) {
        $pdo = getConnection();
        $time = new Time($pdo);
        $time->adicionar($usuarioId, $pokemonId);

        $pokemon = new Pokemon($pdo);
        $pokemon->carregar($pokemonId);
        $log = new Log($pdo);
        $log->registrar($usuarioId, 'time', 'Adicionou ' . $pokemon->getNome() . ' ao time');
    }

    public function remover($usuarioId, $pokemonId) {
        $pdo = getConnection();
        $time = new Time($pdo);
        $time->remover($usuarioId, $pokemonId);

        $pokemon = new Pokemon($pdo);
        $pokemon->carregar($pokemonId);
        $log = new Log($pdo);
        $log->registrar($usuarioId, 'time', 'Removeu ' . $pokemon->getNome() . ' do time');
    }
}

// Todas as respostas são JSON
header('Content-Type: application/json');

// Log: tudo o que o usuário faz (e os erros) fica registrado
$log = new Log(getConnection());

if (!isset($_SESSION['usuario_id'])) {
    $log->registrar(null, 'seguranca', 'Acesso sem login ao TimeController');
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
    $log->registrar($usuarioId, 'erro', 'Time (' . $acao . '): ' . $e->getMessage());
    echo json_encode(['erro' => $e->getMessage()]);
}