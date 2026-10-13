<?php

class Charmander extends Pokemon {

    use Fogo;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(2);

        // usos = null -> ilimitado | usos = 2 -> só pode usar 2 vezes
        $this->setAtaques([
            ['nome' => 'Arranhão',     'tipo' => 'Normal', 'poder' => 40, 'usos' => null],
            ['nome' => 'Brasas',       'tipo' => 'Fogo',   'poder' => 60, 'usos' => 2],
            ['nome' => 'Lança-chamas', 'tipo' => 'Fogo',   'poder' => 90, 'usos' => 2]
        ]);
    }
}