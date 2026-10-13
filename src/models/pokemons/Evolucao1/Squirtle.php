<?php

class Squirtle extends Pokemon {

    use Agua;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(3);

        $this->setAtaques([
            ['nome' => 'Investida',       'tipo' => 'Normal', 'poder' => 40, 'usos' => null],
            ['nome' => 'Jato de Bolhas',  'tipo' => 'Agua',   'poder' => 60, 'usos' => 2],
            ['nome' => 'Jato d\'Água',    'tipo' => 'Agua',   'poder' => 90, 'usos' => 2]
        ]);
    }
}