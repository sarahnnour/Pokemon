class Loja {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/LojaController.php';
        this.imagens = {}; // guarda as imagens já buscadas (para não buscar de novo)
    }

    carregar() {
        this.requisicao.get(this.url + '?acao=listar').then(dados => {
            document.getElementById('dinheiro').innerHTML = dados.dinheiro;
            this.desenharTime(dados.time);
            this.desenharItens(dados.itens);
        });
    }

    comprar(itemId) {
        const pokemonId = document.getElementById('pokemon').value;

        this.requisicao.post(this.url + '?acao=comprar', { item_id: itemId, pokemon_id: pokemonId }).then(dados => {
            this.mostrarMensagem(dados);
            this.carregar();
        });
    }

    mostrarMensagem(dados) {
        const mensagem = document.getElementById('mensagem');
        if (dados.erro) {
            mensagem.className = 'erro';
            mensagem.innerHTML = dados.erro;
        } else {
            mensagem.className = 'sucesso';
            mensagem.innerHTML = 'Compra realizada!';
        }
    }

    desenharTime(lista) {
        const select = document.getElementById('pokemon');
        const escolhido = select.value;

        let html = '';
        for (let i = 0; i < lista.length; i++) {
            const p = lista[i];
            html += `<option value="${p.pokemon_id}">${p.pokemon} - HP ${p.hp_atual}/${p.hp_maximo} - ${p.status_condicao}</option>`;
        }
        select.innerHTML = html;
        select.value = escolhido;
    }

    desenharItens(lista) {
        let html = '';
        for (let i = 0; i < lista.length; i++) {
            const item = lista[i];
    
            let imagem = '';
            if (this.imagens[item.id]) {
                imagem = `src="${this.imagens[item.id]}"`;
            }
    
            html += `<div class="item">
                <img class="item-imagem" id="imagem-${item.id}" ${imagem} alt="">
                <h3>${item.nome}</h3>
                <p>${item.descricao}</p>
                <p class="preco">Preço: ${item.preco}</p>
                <button onclick="loja.comprar(${item.id})">Comprar</button>
            </div>`;
        }
        document.getElementById('itens').innerHTML = html;
    
        // busca na API a imagem dos itens que ainda não têm
        for (let i = 0; i < lista.length; i++) {
            if (!this.imagens[lista[i].id]) {
                this.buscarImagem(lista[i]);
            }
        }
    }
    
    // Consome a PokeAPI: https://pokeapi.co/api/v2/item/potion
    buscarImagem(item) {
        let nome = item.nome.toLowerCase().replaceAll(' ', '-');
    
        // a Pedra de Evolução não existe com esse nome na API
        if (item.tipo == 'evolucao') {
            nome = 'moon-stone';
        }
    
        this.requisicao.get('https://pokeapi.co/api/v2/item/' + nome).then(dados => {
            this.imagens[item.id] = dados.sprites.default;
            document.getElementById('imagem-' + item.id).src = dados.sprites.default;
        });
    }
}