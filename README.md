Projeto Pokemon Sarah Nour, Pedro Rezende

ESTRUTURA DE PASTA MVC
    pokemon/
    ├── index.php
    |-- autoload.php
    ├── src/
    │   ├── models/
    │   │   ├── pokemons/
    │   │   │  ├── Evolucao1/
    │   │   │  |   ├──Bulbassauro.php 
    │   │   │  |   ├──Charmander.php
    │   │   │  |   ├──Pikachu.php
    │   │   │  |   ├──Squirtle.php
    │   │   │  |   ├──Cubchoo.php
    │   │   │  |   ├──Exeggcute.php
    │   │   │  |   ├──Growlithe.php
    │   │   │  |   ├──Koffing.php
    │   │   │  |   ├──Magikarp.php
    │   │   │  |   ├──Zeraora.php
    │   │   │  ├── Evolucao2/
    │   │   │  |   ├──Charmeleon.php
    │   │   │  |   ├──Ivyssauro.php
    │   │   │  |   ├──Raichu.php
    │   │   │  |   ├──Wartortle.php
    │   │   │  |   ├──Arcanine.php
    │   │   │  |   ├──Beartic.php
    │   │   │  |   ├──Exeggcutor.php
    │   │   │  |   ├──Gyarados.php
    │   │   │  |   ├──Weezing.php
    │   │   │  ├── Evolucao3/
    │   │   │  |   ├──Blastoise.php
    │   │   │  |   ├──Charizard.php
    │   │   │  |   ├──Venossauro.php
    │   │   ├── Conexao.php
    │   │   ├── Usuario.php
    │   │   ├── Pokemon.php
    │   │   ├── Item.php
    │   │   ├── Bolsa.php
    │   │   ├── Batalha.php
    │   │   └── Time.php
    │   ├── traits/
    │   │   ├── Gelo.php
    │   │   ├── Veneno.php
    │   │   ├── Fogo.php
    │   │   ├── Agua.php
    │   │   └── Planta.php
    │   │   └── Raio.php
    │   ├── excecoes/
    │   │   └── SaldoInsuficienteException.php
    │   │   └── TimeCheioException.php
    │   │   └── UsuarioJaExisteException.php
    │   │   └── UsuarioNaoEncontradoException.php
    │   └── controllers/
    │       ├── UsuarioController.php
    │       ├── TimeController.php
    │       ├── BatalhaController.php
    │       ├── BolsaController.php
    │       └── LojaController.php
    ├── views/
    │   ├── cabecalho.php
    │   ├── menu.php
    │   ├── rodape.php
    │   ├── usuario/
    │   │   └── cadastro.php
    │   │   └── login.php
    │   ├── time/
    │   │   └── montar_time.php
    │   ├── bolsa/
    │   │   └── bolsa.php
    │   ├── batalha/
    │   │   └── batalha.php
    │   └── loja/
    │       └── loja.php
    ├── assets/
    |   ├── css/
    │   │   └── estilo.css
    │   │   └── login.css
    │   │   └── loja.css
    │   │   └── menu.css
    │   │   └── time.css
    |   ├── img/
    |   ├── js/
    │   │   ├── Requisicao.js
    │   │   ├── Batalha.js
    │   │   ├── Bolsa.js
    │   │   ├── Loja.js
    │   │   └── MontarTime.js
    └── sql/
        └── jogo_pokemon.sql

