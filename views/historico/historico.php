<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../usuario/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico - Pokémon</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap">
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <link rel="stylesheet" href="../../assets/css/historico.css">
</head>
<body>
    <div class="tela">

        <h1 class="titulo-historico">Histórico</h1>

        <hr class="linha">

        <div class="area-historico">
            <div id="historico"></div>
        </div>

        <a class="sair" href="../menu.php">
            <img src="../../assets/img/icone-sair.png" alt="">
            Sair
        </a>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/Historico.js"></script>
    <script>
        const historico = new Historico(new Requisicao());
        historico.carregar();
    </script>
</body>
</html>