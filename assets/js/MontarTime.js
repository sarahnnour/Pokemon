class MontarTime {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/TimeController.php';
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
            html = '<p>Nenhum Pokémon no time.</p>';
        }
        for (let i = 0; i < lista.length; i++) {
            const p = lista[i];
            html += `<p>${p.pokemon} (${p.tipo}) - HP ${p.hp_atual}/${p.hp_maximo}
                <button onclick="montarTime.remover(${p.pokemon_id})">Remover</button></p>`;
        }
        document.getElementById('time').innerHTML = html;
    }

    desenharDisponiveis(lista) {
        let html = '';
        for (let i = 0; i < lista.length; i++) {
            const p = lista[i];
            html += `<p>${p.nome} (${p.tipo}) - HP ${p.hp_maximo}, Atq ${p.ataque}, Def ${p.defesa}, Vel ${p.velocidade}
                <button onclick="montarTime.adicionar(${p.id})">Adicionar</button></p>`;
        }
        document.getElementById('disponiveis').innerHTML = html;
    }
}