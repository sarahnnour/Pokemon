<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar conta - Pokémon</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>
    <div class="coluna">
        <div class="painel">
            <h1 class="logo">Pokémon Adventure</h1>
            <h2 class="boas-vindas">
                <span class="azul">CRIE SUA</span><br>
                <span class="laranja">CONTA</span> <span class="verde">DE TREINADOR!</span>
            </h2>

            <?php
            if (isset($_GET['erro'])) {
                echo '<p class="erro">' . htmlspecialchars($_GET['erro']) . '</p>';
            }
            ?>

            <form action="../../src/controllers/UsuarioController.php?acao=cadastrar" method="post">
                <input type="text" name="nome" placeholder="Nome do Treinador" maxlength="50" required>
                <input type="email" name="email" placeholder="E-mail" maxlength="100" required>
                <input type="password" name="senha" placeholder="Senha" maxlength="50" required>
                <input type="password" name="confirmar_senha" placeholder="Confirmar senha" maxlength="50" required>
                <input type="submit" value="Criar nova conta" class="botao-azul">
            </form>
        </div>

        <a href="login.php" class="link-entrar">Já tem uma conta? Toque para Entrar</a>
    </div>
</body>
</html>