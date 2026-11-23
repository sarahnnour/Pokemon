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
    <title>Bolsa - Pokémon</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap">
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <link rel="stylesheet" href="../../assets/css/loja.css">
</head>
<body>
    <div class="tela">
        <h1 class="titulo-loja">Bolsa</h1>

        <hr class="linha">

        <div class="area-loja">
            <label class="usar-em">Usar em:
                <select id="pokemon"></select>
            </label>
            <p id="mensagem"></p>
            <div id="itens"></div>
        </div>

        <a class="sair" href="../menu.php" title="Sair">&#10005;</a>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/Bolsa.js"></script>
    <script>
        const bolsa = new Bolsa(new Requisicao());
        bolsa.carregar();
    </script>
</body>
</html>