class MontarTime {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/TimeController.php';
        this.imagens = {}; // guarda as imagens já buscadas (para não buscar de novo)
    }

    carregar() {
        this.requisicao.get(this.url + '?acao=listar').then(dados => {
            this.desenharTime(dados.time);
            this.desenharDisponiveis(dados.disponiveis);
        });
    }

    adicionar(pokemonId) {
        this.requisicao.post(this.url + '?acao=adicionar', { pokemon_id: pokemonId }).then(dados => {
            this.mostrarMensagem(dados);
            this.carregar();
        });
    }

    remover(pokemonId) {
        this.requisicao.post(this.url + '?acao=remover', { pokemon_id: pokemonId }).then(dados => {
            this.mostrarMensagem(dados);
            this.carregar();
        });
    }

    mostrarMensagem(dados) {
        let texto = '';
        if (dados.erro) {
            texto = dados.erro;
        }
        document.getElementById('mensagem').innerHTML = texto;
    }

    desenharTime(lista) {
        let html = '';
        if (lista.length == 0) {
            html = '<p class="vazio">Nenhum Pokémon no time.</p>';
        }
        for (let i = 0; i < lista.length; i++) {
            const p = lista[i];
            html += `<div class="card">
                <img class="card-imagem" data-nome="${p.pokemon}" src="${this.imagemSalva(p.pokemon)}" alt="">
                <h3>${p.pokemon}</h3>
                <p>${p.tipo}</p>
                <p>HP ${p.hp_atual}/${p.hp_maximo}</p>
                <button onclick="montarTime.remover(${p.pokemon_id})">Remover</button>
            </div>`;
        }
        document.getElementById('time').innerHTML = html;

        for (let i = 0; i < lista.length; i++) {
            this.buscarImagem(lista[i].pokemon);
        }
    }

    desenharDisponiveis(lista) {
        let html = '';
        for (let i = 0; i < lista.length; i++) {
            const p = lista[i];
            html += `<div class="card">
                <img class="card-imagem" data-nome="${p.nome}" src="${this.imagemSalva(p.nome)}" alt="">
                <h3>${p.nome}</h3>
                <p>${p.tipo}</p>
                <p>HP ${p.hp_maximo} | Atq ${p.ataque}</p>
                <p>Def ${p.defesa} | Vel ${p.velocidade}</p>
                <button onclick="montarTime.adicionar(${p.id})">Adicionar</button>
            </div>`;
        }
        document.getElementById('disponiveis').innerHTML = html;

        for (let i = 0; i < lista.length; i++) {
            this.buscarImagem(lista[i].nome);
        }
    }

    // Se a imagem já foi buscada, devolve o endereço dela
    imagemSalva(nome) {
        if (this.imagens[nome]) {
            return this.imagens[nome];
        }
        return '';
    }

    // Nome do Pokémon na PokeAPI (ex: "Pikachu" -> "pikachu")
    nomeNaApi(nome) {
        // estes três têm nome diferente no nosso banco
        if (nome == 'Bulbassauro') {
            return 'bulbasaur';
        }
        if (nome == 'Ivyssauro') {
            return 'ivysaur';
        }
        if (nome == 'Venossauro') {
            return 'venusaur';
        }
        return nome.toLowerCase();
    }

    // Consome a PokeAPI: https://pokeapi.co/api/v2/pokemon/pikachu
    buscarImagem(nome) {
        if (this.imagens[nome]) {
            return;
        }

        const url = 'https://pokeapi.co/api/v2/pokemon/' + this.nomeNaApi(nome);

        this.requisicao.get(url).then(dados => {
            this.mostrarImagem(nome, dados.sprites.front_default);
        }).catch(erro => {
            console.log('Erro na API para: ' + nome, erro);
        });
    }

    mostrarImagem(nome, imagem) {
        this.imagens[nome] = imagem;

        // o mesmo Pokémon pode aparecer no time e nos disponíveis
        const imgs = document.querySelectorAll('img[data-nome="' + nome + '"]');
        for (let i = 0; i < imgs.length; i++) {
            imgs[i].src = imagem;
        }
    }
}