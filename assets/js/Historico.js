class Historico {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/BatalhaController.php';
    }

    carregar() {
        this.requisicao.get(this.url + '?acao=historico').then(dados => {
            this.desenharHistorico(dados.historico);
        });
    }

    desenharHistorico(lista) {
        if (lista.length == 0) {
            document.getElementById('historico').innerHTML = '<p class="vazio">Nenhuma batalha ainda.</p>';
            return;
        }

        let html = `<table>
            <tr>
                <th>Data</th>
                <th>Pokémon</th>
                <th>Adversário</th>
                <th>Resultado</th>
                <th>Ganho</th>
            </tr>`;

        for (let i = 0; i < lista.length; i++) {
            const b = lista[i];
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

        html += '</table>';
        document.getElementById('historico').innerHTML = html;
    }
}