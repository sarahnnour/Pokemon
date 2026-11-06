class Loja {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/LojaController.php';
        this.imagens = {}; // guarda as imagens já buscadas (para não buscar de novo)
    }

    carregar() {
        this.requisicao.get(this.url + '?acao=listar').then(dados => {
            document.getElementById('dinheiro').innerHTML = dados.dinheiro;
            this.desenharItens(dados.itens);
        });
    }

    comprar(itemId) {
        this.requisicao.post(this.url + '?acao=comprar', { item_id: itemId }).then(dados => {
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
            mensagem.innerHTML = 'Item guardado na bolsa!';
        }
    }


    desenharItens(lista) {
        let html = '';
        for (let i = 0; i < lista.length; i++) {
            const item = lista[i];

            // se a imagem já foi buscada, usa direto
            let src = '';
            if (this.imagens[item.id]) {
                src = this.imagens[item.id];
            }

            html += `<div class="item">
                <img class="item-imagem" id="imagem-${item.id}" src="${src}" alt="">
                <div class="item-texto">
                    <h3>${item.nome}</h3>
                    <p>${item.descricao}</p>
                    <p class="preco">Preço: ${item.preco} ¥</p>
                    <button onclick="loja.comprar(${item.id})">Comprar</button>
                </div>
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

    // Nome do item na PokeAPI (ex: "Super Potion" -> "super-potion")
    nomeNaApi(item) {
        // a Pedra de Evolução não existe com esse nome na API
        if (item.tipo == 'evolucao') {
            return 'moon-stone';
        }
        // na API o Full Revive se chama Max Revive
        if (item.nome == 'Full Revive') {
            return 'max-revive';
        }
        return item.nome.toLowerCase().replaceAll(' ', '-');
    }

    // Consome a PokeAPI: https://pokeapi.co/api/v2/item/potion
       // Consome a PokeAPI: https://pokeapi.co/api/v2/item/potion
       buscarImagem(item) {
        const nome = this.nomeNaApi(item);
        const url = 'https://pokeapi.co/api/v2/item/' + nome;
        // endereço padrão dos sprites de itens (usado se a API não trouxer imagem)
        const reserva = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/' + nome + '.png';

        this.requisicao.get(url).then(dados => {
            let imagem = dados.sprites.default;
            if (!imagem) {
                imagem = reserva;
            }
            this.mostrarImagem(item.id, imagem);
        }).catch(erro => {
            console.log('Erro na API para: ' + item.nome, erro);
            this.mostrarImagem(item.id, reserva);
        });
    }

    mostrarImagem(itemId, imagem) {
        this.imagens[itemId] = imagem;

        // o elemento pode ter sido redesenhado, por isso busca de novo pelo id
        const img = document.getElementById('imagem-' + itemId);
        if (img) {
            img.src = imagem;
        }
    }
}