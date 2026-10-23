// Classe simples para fazer requisições assíncronas que trocam JSON com o PHP.
// Vai ser reaproveitada na Batalha e na Loja.
class Requisicao {

    get(url) {
        return fetch(url).then(resposta => resposta.json());
    }

    post(url, dados) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        }).then(resposta => resposta.json());
    }
}