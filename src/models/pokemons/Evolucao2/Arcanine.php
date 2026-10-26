<?php

class Arcanine extends Pokemon {

    use Fogo;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(16);

        $this->setAtaques([
            ['nome' => 'Mordida',       'tipo' => 'Normal', 'poder' => 50,  'usos' => null],
            ['nome' => 'Fogo Fátuo',    'tipo' => 'Fogo',   'poder' => 30,  'usos' => 5, 'efeito' => 'queimar'],
            ['nome' => 'Lança-chamas',  'tipo' => 'Fogo',   'poder' => 100, 'usos' => 3]
        ]);
    }
}