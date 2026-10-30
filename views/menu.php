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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap">
    <link rel="stylesheet" href="../assets/css/menu.css">
</head>
<body>
    <div class="tela">

        <!-- cabeças dos Pokémon nos cantos -->
        <img class="canto pikachu" src="../assets/img/pikachu-head.png" alt="Pikachu">
        <img class="canto charmander" src="../assets/img/charmander-head.png" alt="Charmander">
        <img class="canto bulbassauro" src="../assets/img/bulbassauro-head.png" alt="Bulbassauro">
        <img class="canto squirtle" src="../assets/img/squirtle-head.png" alt="Squirtle">

      
        <h1 class="texto-ash">
            Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!
            <img class="icone-ash" src="../assets/img/icone-ash.png" alt="Ash">
        </h1>

        <hr class="linha">

        <div class="botoes">
            <a class="botao" href="../index.php?pagina=time">
                <span class="botao-interno">
                    <img class="icone" src="../assets/img/icone-teambuilder.png" alt="">
                    <span class="botao-texto">Team<br>Builder</span>
                </span>
            </a>

            <a class="botao" href="../index.php?pagina=batalha">
                <span class="botao-interno">
                    <img class="icone icone-grande" src="../assets/img/icone-batalha.png" alt="Batalha">
                </span>
            </a>

            <a class="botao" href="../index.php?pagina=loja">
                <span class="botao-interno">
                    <img class="icone" src="../assets/img/icone-pokemart.png" alt="">
                    <span class="botao-texto">Poké<br>Mart</span>
                </span>
            </a>

            <!-- Bolsa não funcional (por enquanto) -->
            <div class="botao">
                <span class="botao-interno">
                    <img class="icone" src="../assets/img/icone-bolsa.png" alt="">
                    <span class="botao-texto botao-bolsa">Bolsa</span>
                </span>
            </div>
        </div>

        <a class="sair" href="../src/controllers/UsuarioController.php?acao=sair">
            <img src="../assets/img/icone-sair.png" alt="">
            Sair
        </a>
    </div>
</body>
</html>