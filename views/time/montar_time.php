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
    <title>Team Builder - Pokémon</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap">
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <link rel="stylesheet" href="../../assets/css/time.css">
</head>
<body>
    <div class="tela">

        <img class="canto pikachu" src="../../assets/img/pikachu-head.png" alt="Pikachu">
        <img class="canto charmander" src="../../assets/img/charmander-head.png" alt="Charmander">
        <img class="canto bulbassauro" src="../../assets/img/bulbassauro-head.png" alt="Bulbassauro">
        <img class="canto squirtle" src="../../assets/img/squirtle-head.png" alt="Squirtle">

        <h1 class="titulo-time">Team Builder</h1>

        <hr class="linha">

        <div class="area-time">
            <p id="mensagem"></p>

            <h2>Meu time</h2>
            <div id="time" class="grade grade-time"></div>

            <h2>Pokémon disponíveis</h2>
            <div id="disponiveis" class="grade grade-disponiveis"></div>
        </div>

        <a class="sair" href="../menu.php">
            <img src="../../assets/img/icone-sair.png" alt="">
            Sair
        </a>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/MontarTime.js"></script>
    <script>
        const montarTime = new MontarTime(new Requisicao());
        montarTime.carregar();
    </script>
</body>
</html>