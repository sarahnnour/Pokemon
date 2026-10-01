Projeto Pokemon Sarah Nour, Pedro Rezende

ESTRUTURA DE PASTA MVC
    pokemon/
    ├── index.php
    ├── configuracao.php
    ├── src/
    │   ├── models/
    │   │   ├── Conexao.php
    │   │   ├── Usuario.php
    │   │   ├── Pokemon.php
    │   │   ├── Charmander.php
    │   │   ├── Squirtle.php
    │   │   ├── Geodude.php
    │   │   ├── Item.php
    │   │   ├── Batalha.php
    │   │   └── Time.php
    │   ├── traits/
    │   │   ├── Fogo.php
    │   │   ├── Agua.php
    │   │   └── Terra.php
    │   ├── excecoes/
    │   │   └── SaldoInsuficienteException.php
    │   └── controllers/
    │       ├── UsuarioController.php
    │       ├── TimeController.php
    │       ├── BatalhaController.php
    │       └── LojaController.php
    ├── views/
    │   ├── cabecalho.php
    │   ├── rodape.php
    │   ├── usuario/
    │   │   └── perfis.php
    │   ├── time/
    │   │   └── montar_time.php
    │   ├── batalha/
    │   │   └── batalha.php
    │   └── loja/
    │       └── loja.php
    ├── js/
    │   ├── Requisicao.js
    │   ├── Batalha.js
    │   ├── Loja.js
    │   └── MontarTime.js
    └── sql/
        └── jogo_pokemon.sql

