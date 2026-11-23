<?php
session_start();

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../models/Conexao.php';

class BatalhaController {

    // time do usuário + itens da bolsa que podem ser usados em batalha (sem a Pedra de Evolução)
    public function listar($usuarioId) {
        $pdo = getConnection();
        $time = new Time($pdo);
        $bolsa = new Bolsa($pdo);

        $itens = [];
        foreach ($bolsa->listar($usuarioId) as $item) {
            if ($item['tipo'] != 'evolucao') {
                $itens[] = $item;
            }
        }

        return [
            'time' => $time->listarDoUsuario($usuarioId),
            'itens' => $itens
        ];
    }

    public function iniciar($usuarioId, $pokemonId) {
        $pdo = getConnection();

        // começou outra batalha sem terminar a anterior (fechou a página, por exemplo)
        if (isset($_SESSION['batalha'])) {
            unset($_SESSION['batalha']);
            $log = new Log($pdo);
            $log->registrar($usuarioId, 'batalha', 'Abandonou a batalha anterior sem terminar');
        }

        $batalha = new Batalha($pdo);
        $batalha->iniciar($usuarioId, $pokemonId);

        // a batalha fica guardada na sessão entre as rodadas
        $_SESSION['batalha'] = $batalha->paraArray();

        return $batalha->getEstado();
    }

    public function atacar($numero) {
        $batalha = $this->batalhaDaSessao();
        $batalha->rodada($numero);
        return $this->guardarNaSessao($batalha);
    }

    public function trocar($pokemonId) {
        $batalha = $this->batalhaDaSessao();
        $batalha->trocar($pokemonId);
        return $this->guardarNaSessao($batalha);
    }

    public function usarItem($itemId, $pokemonId) {
        $batalha = $this->batalhaDaSessao();
        $batalha->usarItem($itemId, $pokemonId);
        return $this->guardarNaSessao($batalha);
    }

    // Botão "Fugir": a batalha acaba sem vitória e sem derrota
    public function fugir($usuarioId) {
        if (isset($_SESSION['batalha'])) {
            unset($_SESSION['batalha']);
            $log = new Log(getConnection());
            $log->registrar($usuarioId, 'batalha', 'Fugiu da batalha');
        }
    }

    // A batalha fica guardada na sessão entre uma rodada e outra
    private function batalhaDaSessao() {
        if (!isset($_SESSION['batalha'])) {
            throw new Exception("Nenhuma batalha em andamento");
        }

        $batalha = new Batalha(getConnection());
        $batalha->restaurar($_SESSION['batalha']);
        return $batalha;
    }

    // Se a batalha terminou, sai da sessão. Devolve o estado para o JavaScript.
    private function guardarNaSessao($batalha) {
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

// Log: tudo o que o usuário faz (e os erros) fica registrado
$log = new Log(getConnection());

if (!isset($_SESSION['usuario_id'])) {
    $log->registrar(null, 'seguranca', 'Acesso sem login ao BatalhaController');
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

    if ($acao == 'trocar') {
        echo json_encode($controller->trocar($dados['pokemon_id']));
    }

    if ($acao == 'item') {
        echo json_encode($controller->usarItem($dados['item_id'], $dados['pokemon_id']));
    }

    // Fugir é um link: registra e volta para o menu
    if ($acao == 'fugir') {
        $controller->fugir($usuarioId);
        header('Location: ../../views/menu.php');
        exit;
    }
} catch (Exception $e) {
    $log->registrar($usuarioId, 'erro', 'Batalha (' . $acao . '): ' . $e->getMessage());
    echo json_encode(['erro' => $e->getMessage()]);
}