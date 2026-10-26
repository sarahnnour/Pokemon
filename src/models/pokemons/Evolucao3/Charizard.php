<?php

class Charizard extends Pokemon {

    use Fogo;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(10);

        $this->setAtaques([
            ['nome' => 'Garra de Dragão',  'tipo' => 'Normal', 'poder' => 60,  'usos' => null],
            ['nome' => 'Fogo Fátuo',       'tipo' => 'Fogo',   'poder' => 40,  'usos' => 7, 'efeito' => 'queimar'],
            ['nome' => 'Rajada de Fogo',   'tipo' => 'Fogo',   'poder' => 110, 'usos' => 5]

        ]);
    }
}