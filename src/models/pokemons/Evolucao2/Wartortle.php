<?php

class Wartortle extends Pokemon {

    use Agua;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(7);

        $this->setAtaques([
            ['nome' => 'Investida',       'tipo' => 'Normal', 'poder' => 50,  'usos' => null],
            ['nome' => 'Jato de Bolhas',  'tipo' => 'Agua',   'poder' => 70,  'usos' => 2],
            ['nome' => 'Jato d\'Água',    'tipo' => 'Agua',   'poder' => 100, 'usos' => 2]
        ]);
    }
}