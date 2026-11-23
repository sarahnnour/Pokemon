// Tela de Logs (botão ☰ do menu).
// Aba "Batalhas": resultado de cada batalha + tudo o que aconteceu nelas.
// Aba "Usuário": login, logout, compras, time, bolsa e erros.
class Logs {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.logs = { resultados: [], batalha: [], usuario: [] };
    }

    carregar() {
        this.requisicao.get('../../src/controllers/LogController.php?acao=listar').then(dados => {
            this.logs = dados;
            this.mostrar('batalha');
        }).catch(erro => {
            console.log(erro);
        });
    }

    mostrar(aba) {
        // marca a aba escolhida
        document.getElementById('aba-batalha').className = '';
        document.getElementById('aba-usuario').className = '';
        document.getElementById('aba-' + aba).className = 'ativa';

        let html = '';
        if (aba == 'batalha') {
            html = this.tabelaResultados() + '<h2>Eventos das batalhas</h2>';
        }
        html += this.tabelaLogs(this.logs[aba]);

        document.getElementById('logs').innerHTML = html;
    }

    tabelaResultados() {
        const resultados = this.logs.resultados;
        if (resultados.length == 0) {
            return '<p class="vazio">Nenhuma batalha ainda.</p>';
        }

        let html = `<h2>Resultados</h2>
            <table>
            <tr>
                <th>Data</th>
                <th>Pokémon</th>
                <th>Adversário</th>
                <th>Resultado</th>
                <th>Ganho</th>
            </tr>`;

        for (let i = 0; i < resultados.length; i++) {
            const b = resultados[i];
            let resultado = 'Derrota';
            if (b.resultado == 'vitoria') {
                resultado = 'Vitória';
            }
            html += `<tr>
                <td>${b.data_batalha}</td>
                <td>${b.pokemon}</td>
                <td>${b.adversario}</td>
                <td class="${b.resultado}">${resultado}</td>
                <td>${b.dinheiro_ganho} ¥</td>
            </tr>`;
        }

        return html + '</table>';
    }

    tabelaLogs(lista) {
        if (lista.length == 0) {
            return '<p class="vazio">Nenhum log ainda.</p>';
        }

        let html = `<table class="tabela-logs">
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>O que aconteceu</th>
            </tr>`;

        for (let i = 0; i < lista.length; i++) {
            const l = lista[i];
            html += `<tr>
                <td>${l.data_acao}</td>
                <td class="${l.tipo}">${l.tipo}</td>
                <td class="descricao">${l.descricao}</td>
            </tr>`;
        }

        return html + '</table>';
    }
}
