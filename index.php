<?php
session_start();

// Sem login, vai para a tela de login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: views/usuario/login.html');
    exit;
}

// Com login, redireciona para a página pedida (padrão: menu)
$pagina = 'menu';
if (isset($_GET['pagina'])) {
    $pagina = $_GET['pagina'];
}

switch ($pagina) {
    case 'perfis':
        header('Location: views/usuario/perfis.php');
        break;
    case 'time':
        header('Location: views/time/montar_time.php');
        break;
    case 'batalha':
        header('Location: views/batalha/batalha.php');
        break;
    case 'loja':
        header('Location: views/loja/loja.php');
        break;
    default:
        header('Location: views/menu.php');
}
exit;