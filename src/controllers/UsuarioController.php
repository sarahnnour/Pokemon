<?php
session_start();

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../models/Conexao.php';

class UsuarioController {

    public function entrar($nome, $senha) {
        $pdo = getConnection();
        $usuario = new Usuario($pdo);

        // lança UsuarioNaoEncontradoException se o login estiver errado
        $dados = $usuario->buscarLogin($nome, $senha);

        $_SESSION['usuario_id'] = $dados['id'];
        $_SESSION['usuario_nome'] = $dados['nome'];
    }

    public function sair() {
        session_destroy();
    }
}

$controller = new UsuarioController();
$acao = '';
if (isset($_GET['acao'])) {
    $acao = $_GET['acao'];
}

if ($acao == 'entrar') {
    try {
        $controller->entrar($_POST['nome'], $_POST['senha']);
        header('Location: ../../index.php');
    } catch (UsuarioNaoEncontradoException $e) {
        header('Location: ../../views/usuario/login.html?erro=1');
    }
    exit;
}

if ($acao == 'sair') {
    $controller->sair();
    header('Location: ../../views/usuario/login.html');
    exit;
}