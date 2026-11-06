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
    <title>Poké Mart - Pokémon</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap">
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <link rel="stylesheet" href="../../assets/css/loja.css">
</head>
<body>
    <div class="tela">

        <img class="canto pikachu" src="../../assets/img/pikachu-head.png" alt="Pikachu">
        <img class="canto charmander" src="../../assets/img/charmander-head.png" alt="Charmander">
        <img class="canto bulbassauro" src="../../assets/img/bulbassauro-head.png" alt="Bulbassauro">
        <img class="canto squirtle" src="../../assets/img/squirtle-head.png" alt="Squirtle">

        <h1 class="titulo-loja">Poké Mart</h1>
        <p class="saldo">Pokédollar Atual: <span id="dinheiro"></span> ¥</p>

        <hr class="linha">

        <div class="area-loja">
            <p id="mensagem"></p>
            <div id="itens"></div>
        </div>

        <a class="sair" href="../menu.php">
            <img src="../../assets/img/icone-sair.png" alt="">
            Sair
        </a>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/Loja.js"></script>
    <script>
        const loja = new Loja(new Requisicao());
        loja.carregar();
    </script>
</body>
</html>