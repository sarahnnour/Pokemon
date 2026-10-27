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

    public function cadastrar($nome, $email, $senha, $confirmarSenha) {
        if ($nome == '' || $email == '' || $senha == '') {
            throw new Exception("Preencha todos os campos");
        }

        if ($senha != $confirmarSenha) {
            throw new Exception("As senhas não conferem");
        }

        $pdo = getConnection();
        $usuario = new Usuario($pdo);

        if ($usuario->nomeExiste($nome)) {
            throw new UsuarioJaExisteException("Esse nome de usuário já existe");
        }

        if ($usuario->emailExiste($email)) {
            throw new UsuarioJaExisteException("Esse e-mail já está cadastrado");
        }
        
        // o dinheiro inicial (1000) vem do DEFAULT da tabela
        $usuario->create($nome, $senha, $email);
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
        header('Location: ../../views/usuario/login.php?erro=1');
    }
    exit;
}

if ($acao == 'cadastrar') {
    try {
        $controller->cadastrar(
            trim($_POST['nome']),
            trim($_POST['email']),
            $_POST['senha'],
            $_POST['confirmar_senha']
        );
        header('Location: ../../views/usuario/login.php?cadastro=1');
    } catch (Exception $e) {
        header('Location: ../../views/usuario/cadastro.php?erro=' . urlencode($e->getMessage()));
    }
    exit;
}

if ($acao == 'sair') {
    $controller->sair();
    header('Location: ../../views/usuario/login.php');
    exit;
}