<?php
session_start();

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../models/Conexao.php';

class LojaController {

    public function listar($usuarioId) {
        $pdo = getConnection();
        $item = new Item($pdo);
        $usuario = new Usuario($pdo);
        $usuario->carregar($usuarioId);

        return [
            'dinheiro' => $usuario->getDinheiro(),
            'itens' => $item->listar()
        ];
    }

    // lança SaldoInsuficienteException se faltar dinheiro
    public function comprar($usuarioId, $itemId) {
        $pdo = getConnection();
        $item = new Item($pdo);
        $item->comprar($usuarioId, $itemId);
    }
}

// Todas as respostas são JSON
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Faça login primeiro']);
    exit;
}

$controller = new LojaController();
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

    if ($acao == 'comprar') {
        $controller->comprar($usuarioId, $dados['item_id']);
        echo json_encode(['ok' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}