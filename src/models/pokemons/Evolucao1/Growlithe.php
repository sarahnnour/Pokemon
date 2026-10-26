<?php

class Growlithe extends Pokemon {

    use Fogo;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(22);

        // usos = null -> ilimitado | usos = 2 -> só pode usar 2 vezes
        // efeito = 'queimar' -> deixa o adversário queimado
        $this->setAtaques([
            ['nome' => 'Mordida',       'tipo' => 'Normal', 'poder' => 40, 'usos' => null],
            ['nome' => 'Fogo Fátuo',    'tipo' => 'Fogo',   'poder' => 20, 'usos' => 3, 'efeito' => 'queimar'],
            ['nome' => 'Lança-chamas',  'tipo' => 'Fogo',   'poder' => 90, 'usos' => 2]


        ]);
    }
}