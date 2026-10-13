<?php

class Charizard extends Pokemon {

    use Fogo;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(10);

        $this->setAtaques([
            ['nome' => 'Garra de Dragão',  'tipo' => 'Normal', 'poder' => 60,  'usos' => null],
            ['nome' => 'Lança-chamas',     'tipo' => 'Fogo',   'poder' => 80,  'usos' => 2],
            ['nome' => 'Rajada de Fogo',   'tipo' => 'Fogo',   'poder' => 110, 'usos' => 2]
        ]);
    }
}