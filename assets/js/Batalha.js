class Batalha {

    constructor(requisicao) {
        this.requisicao = requisicao;
        this.url = '../../src/controllers/BatalhaController.php';
        this.time = [];      // time do usuário (vem do banco)
        this.imagens = {};   // imagens já buscadas na PokeAPI
        this.jogador = '';   // nome do Pokémon do jogador em campo
        this.inimigo = '';   // nome do Pokémon adversário
        this.ataques = [];   // ataques do Pokémon em campo
        this.golpe = null;   // golpe que o jogador acabou de usar (para o efeito)
    }

    carregar() {
        return this.requisicao.get(this.url + '?acao=listar').then(dados => {
            this.time = dados.time;
        });
    }

    // Começa com o primeiro Pokémon do time que ainda tem HP
    iniciar() {
        this.carregar().then(() => {
            let escolhido = null;
            for (let i = 0; i < this.time.length; i++) {
                if (this.time[i].hp_atual > 0) {
                    escolhido = this.time[i];
                    break;
                }
            }

            if (escolhido == null) {
                document.getElementById('mensagem').innerHTML = 'Nenhum Pokémon do time pode lutar';
                return;
            }

            this.requisicao.post(this.url + '?acao=iniciar', { pokemon_id: escolhido.pokemon_id }).then(dados => {
                this.tratarResposta(dados);
            }).catch(erro => {
                this.mostrarErro(erro);
            });
        }).catch(erro => {
            this.mostrarErro(erro);
        });
    }

    atacar(numero) {
        // guarda o golpe para mostrar o aviso e o efeito quando a resposta chegar
        this.golpe = { quem: this.jogador, nome: this.ataques[numero].nome };

        this.requisicao.post(this.url + '?acao=atacar', { ataque: numero }).then(dados => {
            this.tratarResposta(dados);
        }).catch(erro => {
            this.mostrarErro(erro);
        });
    }

    trocar(pokemonId) {
        this.requisicao.post(this.url + '?acao=trocar', { pokemon_id: pokemonId }).then(dados => {
            this.tratarResposta(dados);
        }).catch(erro => {
            this.mostrarErro(erro);
        });
    }

    mostrarErro(erro) {
        console.log(erro);
        this.golpe = null;
        document.getElementById('mensagem').innerHTML = 'Erro na comunicação com o servidor (veja o Console)';
    }

    tratarResposta(dados) {
        if (dados.erro) {
            this.golpe = null;
            document.getElementById('mensagem').innerHTML = dados.erro;
            return;
        }

        document.getElementById('mensagem').innerHTML = '';

        // atualiza o time (o HP é salvo no banco) e depois desenha a batalha
        this.carregar().then(() => {
            this.desenharBatalha(dados);
        }).catch(erro => {
            this.mostrarErro(erro);
        });
    }

    // Barra superior: nome, tipo e HP
    textoPokemon(p) {
        const porcentagem = p.hp_atual / p.hp_maximo * 100;
        return `<p class="nome">${p.nome} <span>(${p.tipo})</span></p>
            <div class="hp-fundo"><div class="hp-barra" style="width: ${porcentagem}%"></div></div>
            <p class="hp-texto">HP ${p.hp_atual}/${p.hp_maximo} - ${p.status}</p>`;
    }

    desenharBatalha(dados) {
        const golpe = this.golpe;
        this.golpe = null;

        this.jogador = dados.jogador.nome;
        this.inimigo = dados.inimigo.nome;
        this.ataques = dados.jogador.ataques;

        document.getElementById('info-jogador').innerHTML = this.textoPokemon(dados.jogador);
        document.getElementById('info-inimigo').innerHTML = this.textoPokemon(dados.inimigo);

        // Pokémon no campo (imagens da PokeAPI)
        document.getElementById('campo').innerHTML =
            `<img id="imagem-inimigo" class="pokemon inimigo" src="${this.imagemSalva(this.inimigo, 'frente')}" alt="">
             <img id="imagem-jogador" class="pokemon jogador" src="${this.imagemSalva(this.jogador, 'costas')}" alt="">`;
        this.buscarImagem(this.jogador);
        this.buscarImagem(this.inimigo);

        // resultado da batalha
        let resultado = '';
        if (dados.terminou) {
            if (dados.resultado == 'vitoria') {
                resultado = 'Você venceu! +' + dados.dinheiro_ganho + ' ¥';
            } else {
                resultado = 'Você perdeu a batalha.';
            }
        }
        document.getElementById('resultado').innerHTML = resultado;

        // o botão de iniciar some quando a batalha começa
        document.getElementById('inicio').style.display = 'none';

        this.desenharAtaques(dados);
        this.desenharTrocas(dados);
        this.efeitos(dados, golpe);

        // acabou (vitória ou derrota): volta para o menu depois de mostrar o resultado
        if (dados.terminou) {
            setTimeout(() => {
                window.location.href = '../menu.php';
            }, 2500);
        }
    }

    // Aviso com o nome do ataque e Pokémon que "avança" ao atacar
    efeitos(dados, golpe) {
        let espera = 0;

        if (golpe) {
            this.mostrarAviso(golpe.quem + ' usou ' + golpe.nome + '!');
            this.mexer('imagem-jogador', 'ataca-jogador');
            espera = 1000;
        }

        if (dados.ataque_inimigo != '') {
            setTimeout(() => {
                this.mostrarAviso(dados.inimigo.nome + ' usou ' + dados.ataque_inimigo + '!');
                this.mexer('imagem-inimigo', 'ataca-inimigo');
            }, espera);
        }
    }

    // O aviso some sozinho (a animação do CSS apaga ele)
    mostrarAviso(texto) {
        document.getElementById('aviso').innerHTML = '<span>' + texto + '</span>';
    }

    mexer(id, classe) {
        const img = document.getElementById(id);
        if (img) {
            img.classList.add(classe);
        }
    }

    desenharAtaques(dados) {
        let html = '';
        if (!dados.terminou) {
            for (let i = 0; i < dados.jogador.ataques.length; i++) {
                const a = dados.jogador.ataques[i];
                let usos = '∞';
                let desativado = '';
                if (a.usos !== null) {
                    usos = a.usos;
                    if (a.usos <= 0) {
                        desativado = 'disabled';
                    }
                }
                // se o Pokémon desmaiou, precisa trocar antes de atacar
                if (dados.precisa_trocar) {
                    desativado = 'disabled';
                }
                html += `<button class="ataque" onclick="batalha.atacar(${i})" ${desativado}>
                    <b>${a.nome}</b>
                    <span>${a.tipo} | poder ${a.poder}</span>
                    <span>usos: ${usos}</span>
                </button>`;
            }
        }
        document.getElementById('ataques').innerHTML = html;
    }

    // Troca: um botão com a imagem de cada outro Pokémon do time
    desenharTrocas(dados) {
        let html = '';
        if (!dados.terminou) {
            for (let i = 0; i < this.time.length; i++) {
                const p = this.time[i];
                if (p.pokemon != this.jogador) {
                    let desativado = '';
                    if (p.hp_atual <= 0) {
                        desativado = 'disabled';
                    }
                    html += `<button class="troca" onclick="batalha.trocar(${p.pokemon_id})" ${desativado}>
                        <img data-nome="${p.pokemon}" src="${this.imagemSalva(p.pokemon, 'frente')}" alt="${p.pokemon}">
                    </button>`;
                }
            }
        }
        document.getElementById('trocas').innerHTML = html;

        // busca na PokeAPI as imagens do time
        for (let i = 0; i < this.time.length; i++) {
            this.buscarImagem(this.time[i].pokemon);
        }
    }

    // ---------- PokeAPI ----------

    // Se a imagem já foi buscada, devolve o endereço dela
    imagemSalva(nome, lado) {
        if (!this.imagens[nome]) {
            return '';
        }
        if (lado == 'costas' && this.imagens[nome].costas) {
            return this.imagens[nome].costas;
        }
        return this.imagens[nome].frente;
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
            this.imagens[nome] = {
                frente: dados.sprites.front_default,
                costas: dados.sprites.back_default
            };
            this.atualizarImagens();
        }).catch(erro => {
            console.log('Erro na API para: ' + nome, erro);
        });
    }

    atualizarImagens() {
        const jogador = document.getElementById('imagem-jogador');
        const inimigo = document.getElementById('imagem-inimigo');

        if (jogador) {
            jogador.src = this.imagemSalva(this.jogador, 'costas');
        }
        if (inimigo) {
            inimigo.src = this.imagemSalva(this.inimigo, 'frente');
        }

        const trocas = document.querySelectorAll('.troca img');
        for (let i = 0; i < trocas.length; i++) {
            trocas[i].src = this.imagemSalva(trocas[i].dataset.nome, 'frente');
        }
    }
}