<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar conta - Pokémon</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>
<body>
    <div class="caixa">
        <h1>Criar conta</h1>

        <?php
        if (isset($_GET['erro'])) {
            echo '<p class="erro">' . htmlspecialchars($_GET['erro']) . '</p>';
        }
        ?>

        <form action="../../src/controllers/UsuarioController.php?acao=cadastrar" method="post">
            <label>Nome de usuário <input type="text" name="nome" maxlength="50" required></label>
            <label>E-mail <input type="email" name="email" maxlength="100" required></label>
            <label>Senha <input type="password" name="senha" maxlength="50" required></label>
            <label>Confirmar senha <input type="password" name="confirmar_senha" maxlength="50" required></label>
            <input type="submit" value="Criar conta">
        </form>

        <a href="login.php">Voltar ao login</a>
    </div>
</body>
</html>