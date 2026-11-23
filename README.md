Projeto Pokemon Sarah Nour, Pedro Rezende

ESTRUTURA DE PASTA MVC
    pokemon/
    ├── index.php
    ├── autoload.php
    ├── src/
    │   ├── models/
    │   │   ├── pokemons/
    │   │   │   ├── Evolucao1/
    │   │   │   │   ├── Bulbassauro.php
    │   │   │   │   ├── Charmander.php
    │   │   │   │   ├── Cubchoo.php
    │   │   │   │   ├── Exeggcute.php
    │   │   │   │   ├── Growlithe.php
    │   │   │   │   ├── Koffing.php
    │   │   │   │   ├── Magikarp.php
    │   │   │   │   ├── Pikachu.php
    │   │   │   │   ├── Squirtle.php
    │   │   │   │   └── Zeraora.php
    │   │   │   ├── Evolucao2/
    │   │   │   │   ├── Arcanine.php
    │   │   │   │   ├── Beartic.php
    │   │   │   │   ├── Charmeleon.php
    │   │   │   │   ├── Exeggutor.php
    │   │   │   │   ├── Gyarados.php
    │   │   │   │   ├── Ivyssauro.php
    │   │   │   │   ├── Raichu.php
    │   │   │   │   ├── Wartortle.php
    │   │   │   │   └── Weezing.php
    │   │   │   └── Evolucao3/
    │   │   │       ├── Blastoise.php
    │   │   │       ├── Charizard.php
    │   │   │       └── Venossauro.php
    │   │   ├── Conexao.php
    │   │   ├── Usuario.php
    │   │   ├── Pokemon.php
    │   │   ├── Item.php
    │   │   ├── Bolsa.php
    │   │   ├── Batalha.php
    │   │   ├── Log.php
    │   │   └── Time.php
    │   ├── traits/
    │   │   ├── Agua.php
    │   │   ├── Fogo.php
    │   │   ├── Gelo.php
    │   │   ├── Planta.php
    │   │   ├── Raio.php
    │   │   └── Veneno.php
    │   ├── excecoes/
    │   │   ├── SaldoInsuficienteException.php
    │   │   ├── TimeCheioException.php
    │   │   ├── UsuarioJaExisteException.php
    │   │   └── UsuarioNaoEncontradoException.php
    │   └── controllers/
    │       ├── UsuarioController.php
    │       ├── TimeController.php
    │       ├── BatalhaController.php
    │       ├── BolsaController.php
    │       ├── LojaController.php
    │       └── LogController.php
    ├── views/
    │   ├── menu.php
    │   ├── usuario/
    │   │   ├── cadastro.php
    │   │   └── login.php
    │   ├── time/
    │   │   └── montar_time.php
    │   ├── bolsa/
    │   │   └── bolsa.php
    │   ├── batalha/
    │   │   └── batalha.php
    │   ├── loja/
    │   │   └── loja.php
    │   └── logs/
    │       └── logs.php
    ├── assets/
    │   ├── css/
    │   │   ├── batalha.css
    │   │   ├── login.css
    │   │   ├── logs.css
    │   │   ├── loja.css
    │   │   ├── menu.css
    │   │   └── time.css
    │   ├── img/
    │   └── js/
    │       ├── Requisicao.js
    │       ├── Batalha.js
    │       ├── Bolsa.js
    │       ├── Loja.js
    │       ├── Logs.js
    │       └── MontarTime.js
    └── sql/
        └── jogo_pokemon.sql
