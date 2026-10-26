class Loja {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/LojaController.php';
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
            html += `<p>${item.nome} - ${item.descricao} - Preço: ${item.preco}
                <button onclick="loja.comprar(${item.id})">Comprar</button></p>`;
        }
        document.getElementById('itens').innerHTML = html;
    }
}