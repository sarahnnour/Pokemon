<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: usuario/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Menu - Pokémon</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>
    <div class="caixa">
        <h1>Olá, <?php echo $_SESSION['usuario_nome']; ?>!</h1>

        <a href="../index.php?pagina=time">Team Builder</a>
        <a href="../index.php?pagina=batalha">Batalha</a>
        <a href="../index.php?pagina=loja">Poké Mart</a>
        <a href="../src/controllers/UsuarioController.php?acao=sair">Sair</a>
    </div>
</body>
</html>