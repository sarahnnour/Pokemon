<?php

class Charmeleon extends Pokemon {

    use Fogo;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(6);

        $this->setAtaques([
            ['nome' => 'Arranhão',      'tipo' => 'Normal', 'poder' => 50,  'usos' => null],
            ['nome' => 'Brasas',        'tipo' => 'Fogo',   'poder' => 70,  'usos' => 2],
            ['nome' => 'Lança-chamas',  'tipo' => 'Fogo',   'poder' => 100, 'usos' => 2]
        ]);
    }
}