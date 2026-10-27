<?php
session_start();

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../models/Conexao.php';

class BatalhaController {

    public function listar($usuarioId) {
        $pdo = getConnection();
        $time = new Time($pdo);

        return ['time' => $time->listarDoUsuario($usuarioId)];
    }

    public function iniciar($usuarioId, $pokemonId) {
        $pdo = getConnection();
        $batalha = new Batalha($pdo);
        $batalha->iniciar($usuarioId, $pokemonId);

        // a batalha fica guardada na sessão entre as rodadas
        $_SESSION['batalha'] = $batalha->paraArray();

        return $batalha->getEstado();
    }

    public function atacar($numero) {
        if (!isset($_SESSION['batalha'])) {
            throw new Exception("Nenhuma batalha em andamento");
        }

        $pdo = getConnection();
        $batalha = new Batalha($pdo);
        $batalha->restaurar($_SESSION['batalha']);
        $batalha->rodada($numero);

        if ($batalha->terminou()) {
            unset($_SESSION['batalha']);
        } else {
            $_SESSION['batalha'] = $batalha->paraArray();
        }

        return $batalha->getEstado();
    }
}

// Todas as respostas são JSON
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Faça login primeiro']);
    exit;
}

$controller = new BatalhaController();
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

    if ($acao == 'iniciar') {
        echo json_encode($controller->iniciar($usuarioId, $dados['pokemon_id']));
    }

    if ($acao == 'atacar') {
        echo json_encode($controller->atacar(intval($dados['ataque'])));
    }
} catch (Exception $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}