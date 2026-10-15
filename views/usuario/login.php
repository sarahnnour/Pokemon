<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Pokémon</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>
<body>
    <div class="caixa">
        <h1>Pokémon</h1>

        <?php
        if (isset($_GET['erro'])) {
            echo '<p class="erro">Nome ou senha incorretos</p>';
        }
        if (isset($_GET['cadastro'])) {
            echo '<p class="sucesso">Conta criada! Faça login.</p>';
        }
        ?>

        <form action="../../src/controllers/UsuarioController.php?acao=entrar" method="post">
            <label>Nome <input type="text" name="nome" required></label>
            <label>Senha <input type="password" name="senha" required></label>
            <input type="submit" value="Entrar">
        </form>

        <a href="cadastro.php">Criar conta</a>
    </div>
</body>
</html>