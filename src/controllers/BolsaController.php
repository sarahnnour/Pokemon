<?php
session_start();

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../models/Conexao.php';

class BolsaController {

    public function listar($usuarioId) {
        $pdo = getConnection();
        $bolsa = new Bolsa($pdo);
        $time = new Time($pdo);

        return [
            'itens' => $bolsa->listar($usuarioId),
            'time' => $time->listarDoUsuario($usuarioId)
        ];
    }

    public function usar($usuarioId, $itemId, $pokemonId) {
        $pdo = getConnection();
        $item = new Item($pdo);
        $resultado = $item->usar($usuarioId, $itemId, $pokemonId);

        $log = new Log($pdo);
        $log->registrar($usuarioId, 'bolsa', $resultado);
    }
}

// Todas as respostas são JSON
header('Content-Type: application/json');

// Log: tudo o que o usuário faz (e os erros) fica registrado
$log = new Log(getConnection());

if (!isset($_SESSION['usuario_id'])) {
    $log->registrar(null, 'seguranca', 'Acesso sem login ao BolsaController');
    echo json_encode(['erro' => 'Faça login primeiro']);
    exit;
}

$controller = new BolsaController();
$usuarioId = $_SESSION['usuario_id'];

$acao = '';
if (isset($_GET['acao'])) {
    $acao = $_GET['acao'];
}

$dados = json_decode(file_get_contents('php://input'), true);

try {
    if ($acao == 'listar') {
        echo json_encode($controller->listar($usuarioId));
    }

    if ($acao == 'usar') {
        $controller->usar($usuarioId, $dados['item_id'], $dados['pokemon_id']);
        echo json_encode(['ok' => true]);
    }
} catch (Exception $e) {
    $log->registrar($usuarioId, 'erro', 'Bolsa (' . $acao . '): ' . $e->getMessage());
    echo json_encode(['erro' => $e->getMessage()]);
}