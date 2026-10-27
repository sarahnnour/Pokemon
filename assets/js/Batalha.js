class Batalha {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/BatalhaController.php';
    }

    carregar() {
        this.requisicao.get(this.url + '?acao=listar').then(dados => {
            this.desenharTime(dados.time);
        });
    }

    iniciar() {
        const pokemonId = document.getElementById('pokemon').value;

        this.requisicao.post(this.url + '?acao=iniciar', { pokemon_id: pokemonId }).then(dados => {
            this.tratarResposta(dados);
        });
    }

    atacar(numero) {
        this.requisicao.post(this.url + '?acao=atacar', { ataque: numero }).then(dados => {
            this.tratarResposta(dados);
        });
    }

    tratarResposta(dados) {
        if (dados.erro) {
            document.getElementById('mensagem').innerHTML = dados.erro;
            return;
        }

        document.getElementById('mensagem').innerHTML = '';
        this.desenharBatalha(dados);

        // terminou: atualiza o HP mostrado na lista do time
        if (dados.terminou) {
            this.carregar();
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

    textoPokemon(p) {
        return `<p><b>${p.nome}</b> (${p.tipo}) - HP ${p.hp_atual}/${p.hp_maximo} - ${p.status}</p>`;
    }

    desenharBatalha(dados) {
        let campo = '<h2>Você</h2>' + this.textoPokemon(dados.jogador);
        campo += '<h2>Adversário</h2>' + this.textoPokemon(dados.inimigo);
        document.getElementById('campo').innerHTML = campo;

        let relatorio = '';
        for (let i = 0; i < dados.mensagens.length; i++) {
            relatorio += `<p>${dados.mensagens[i]}</p>`;
        }
        document.getElementById('relatorio').innerHTML = relatorio;

        // quando a batalha termina, não há mais ataques para escolher
        let ataques = '';
        if (!dados.terminou) {
            ataques = '<h2>Ataques</h2>';
            for (let i = 0; i < dados.jogador.ataques.length; i++) {
                const a = dados.jogador.ataques[i];
                let usos = 'ilimitado';
                let desativado = '';
                if (a.usos !== null) {
                    usos = a.usos;
                    if (a.usos <= 0) {
                        desativado = 'disabled';
                    }
                }
                ataques += `<p><button onclick="batalha.atacar(${i})" ${desativado}>${a.nome} (${a.tipo}, poder ${a.poder}) - usos: ${usos}</button></p>`;
            }
        }
        document.getElementById('ataques').innerHTML = ataques;
    }
}