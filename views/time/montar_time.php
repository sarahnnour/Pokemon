<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../usuario/login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Team Builder - Pokémon</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <h1>Team Builder</h1>

        <p id="mensagem" class="erro"></p>

        <h2>Meu time</h2>
        <div id="time"></div>

        <h2>Pokémon disponíveis</h2>
        <div id="disponiveis"></div>

        <a href="../menu.php">Voltar</a>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/MontarTime.js"></script>
    <script>
        const montarTime = new MontarTime(new Requisicao());
        montarTime.carregar();
    </script>
</body>
</html>