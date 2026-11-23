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
    <title>Logs - Pokémon</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap">
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <link rel="stylesheet" href="../../assets/css/logs.css">
</head>
<body>
    <div class="tela">
        <h1 class="titulo-logs">Logs</h1>

        <hr class="linha">

        <!-- duas abas: logs de batalha e logs do usuário (login, compras, time...) -->
        <div class="abas-logs">
            <button id="aba-batalha" onclick="logs.mostrar('batalha')">Batalhas</button>
            <button id="aba-usuario" onclick="logs.mostrar('usuario')">Usuário</button>
        </div>

        <div class="area-logs">
            <div id="logs"></div>
        </div>

        <a class="sair" href="../menu.php" title="Sair">&#10005;</a>
    </div>

    <script src="../../assets/js/Requisicao.js"></script>
    <script src="../../assets/js/Logs.js"></script>
    <script>
        const logs = new Logs(new Requisicao());
        logs.carregar();
    </script>
</body>
</html>