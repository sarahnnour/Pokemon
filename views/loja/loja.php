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
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <h1>Poké Mart</h1>

        <p>Pokédollar: <span id="dinheiro"></span></p>
        <p id="mensagem"></p>

        <label>Usar em:
            <select id="pokemon"></select>
        </label>

        <h2>Itens</h2>
        <div id="itens"></div>

        <a href="../menu.php">Voltar</a>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/Loja.js"></script>
    <script>
        const loja = new Loja(new Requisicao());
        loja.carregar();
    </script>
</body>
</html>