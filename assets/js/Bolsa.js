class Bolsa {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/BolsaController.php';
    }

    carregar() {
        this.requisicao.get(this.url + '?acao=listar').then(dados => {
            this.desenharTime(dados.time);
            this.desenharItens(dados.itens);
        });
    }

    usar(itemId) {
        const pokemonId = document.getElementById('pokemon').value;

        this.requisicao.post(this.url + '?acao=usar', { item_id: itemId, pokemon_id: pokemonId }).then(dados => {
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
            mensagem.innerHTML = 'Item usado!';
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
        if (lista.length == 0) {
            html = '<p>A bolsa está vazia.</p>';
        }
        for (let i = 0; i < lista.length; i++) {
            const item = lista[i];
            html += `<div class="item">
                <div class="item-texto">
                    <h3>${item.nome}</h3>
                    <p>${item.descricao}</p>
                    <p class="preco">Quantidade: ${item.quantidade}</p>
                    <button onclick="bolsa.usar(${item.id})">Usar</button>
                </div>
            </div>`;
        }
        document.getElementById('itens').innerHTML = html;
    }
}