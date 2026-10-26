<?php

class Cubchoo extends Pokemon {

    use Gelo;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(18);

        // usos = null -> ilimitado | usos = 2 -> só pode usar 2 vezes
        // efeito = 'queimar' -> deixa o adversário queimado
        $this->setAtaques([
            ['nome' => 'Investida',     'tipo' => 'Normal', 'poder' => 40, 'usos' => null],
            ['nome' => 'Vento Gélido',  'tipo' => 'Gelo',   'poder' => 25, 'usos' => 3, 'efeito' => 'congelar'],
            ['nome' => 'Raio de Gelo',  'tipo' => 'Gelo',   'poder' => 80, 'usos' => 2]

        ]);
    }
}