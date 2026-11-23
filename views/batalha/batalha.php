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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap">
    <link rel="stylesheet" href="../../assets/css/batalha.css">
</head>
<body>
    <div class="tela-batalha">

        <div class="barra-topo">
            <div id="info-jogador" class="info"></div>
            <div class="botoes-topo">
                <!-- link simples: funciona a qualquer momento da luta (o controller registra a fuga no log) -->
                <a class="voltar" href="../../src/controllers/BatalhaController.php?acao=fugir">Fugir</a>
                <button id="botao-bolsa" class="voltar" style="display: none" onclick="batalha.alternarBolsa()">Bolsa</button>
            </div>
            <div id="info-inimigo" class="info"></div>
        </div>

        <p id="mensagem"></p>

        <div id="campo"></div>
        <div id="aviso" class="aviso"></div>
        <p id="resultado"></p>

        <button id="inicio" class="botao-iniciar" onclick="batalha.iniciar()">Iniciar batalha</button>

        <div class="painel-baixo">
            <div id="ataques"></div>
            <div id="trocas"></div>
        </div>

        <div id="painel-bolsa" class="painel-bolsa">
            <label>Usar em: <select id="pokemon-item"></select></label>
            <div id="itens"></div>
        </div>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/Batalha.js"></script>
    <script>
        const batalha = new Batalha(new Requisicao());
    </script>
</body>
</html>