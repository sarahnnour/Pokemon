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
    <title>Batalha - Pokémon</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <h1>Batalha</h1>

        <p id="mensagem" class="erro"></p>

        <label>Escolha seu Pokémon:
            <select id="pokemon"></select>
        </label>
        <button onclick="batalha.iniciar()">Iniciar batalha</button>

        <div id="campo"></div>
        <div id="relatorio"></div>
        <div id="ataques"></div>

        <a href="../menu.php">Voltar</a>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/Batalha.js"></script>
    <script>
        const batalha = new Batalha(new Requisicao());
        batalha.carregar();
    </script>
</body>
</html>