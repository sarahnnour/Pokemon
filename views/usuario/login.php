<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Pokémon</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>
    <div class="coluna">
        <div class="painel">
            <h1 class="logo">Pokémon Adventure</h1>
            <h2 class="boas-vindas">
                <span class="azul">BEM-VINDO AO</span><br>
                <span class="laranja">POKÉMON</span> <span class="verde">ADVENTURE!</span>
            </h2>

            <?php
            if (isset($_GET['erro'])) {
                echo '<p class="erro">Nome ou senha incorretos</p>';
            }
            if (isset($_GET['cadastro'])) {
                echo '<p class="sucesso">Conta criada! Faça login.</p>';
            }
            ?>

            <form action="../../src/controllers/UsuarioController.php?acao=entrar" method="post">
                <input type="text" name="nome" placeholder="Nome do Treinador" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <input type="submit" value="Entrar" class="botao-verde">
            </form>
        </div>

        <div class="fora-do-painel">
            <a href="cadastro.php" class="botao-azul">Criar nova conta</a>
        </div>
    </div>
</body>
</html>