Projeto Pokemon Sarah Nour, Pedro Rezende

ESTRUTURA DE PASTA MVC
    pokemon/
    ├── index.php
    ├── configuracao.php
    |-- autoload.php
    ├── src/
    │   ├── models/
    │   │   ├── pokemons/
    │   │   │  ├── Evolucao1/
    │   │   │  |   ├──Bulbassauro.php
    │   │   │  |   ├──Charmander.php
    │   │   │  |   ├──Pikachu.php
    │   │   │  |   ├──Squirtle.php
    │   │   │  ├── Evolucao2/
    │   │   │  |   ├──Charmeleon.php
    │   │   │  |   ├──Ivyssauro.php
    │   │   │  |   ├──Raichu.php
    │   │   │  |   ├──Wartortle.php
    │   │   │  ├── Evolucao3/
    │   │   │  |   ├──Blastoise.php
    │   │   │  |   ├──Charizard.php
    │   │   │  |   ├──Venossauro.php
    │   │   ├── Conexao.php
    │   │   ├── Usuario.php
    │   │   ├── Pokemon.php
    │   │   ├── Item.php
    │   │   ├── Batalha.php
    │   │   └── Time.php
    │   ├── traits/
    │   │   ├── Fogo.php
    │   │   ├── Agua.php
    │   │   └── Planta.php
    │   │   └── Raio.php
    │   ├── excecoes/
    │   │   └── SaldoInsuficienteException.php
    │   │   └── TimeCheioException.php
    │   │   └── UsuarioNaoEncontradoException.php
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
    ├── assets/
    |   ├── css/
    |   ├── img/
    |   ├── js/
    │   │   ├── Requisicao.js
    │   │   ├── Batalha.js
    │   │   ├── Loja.js
    │   │   └── MontarTime.js
    └── sql/
        └── jogo_pokemon.sql

