<?php

class Gyarados extends Pokemon {

    use Agua;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(15);

        $this->setAtaques([
            ['nome' => 'Mordida',       'tipo' => 'Normal', 'poder' => 55,  'usos' => null],
            ['nome' => 'Jato d\'Água',  'tipo' => 'Agua',   'poder' => 70,  'usos' => 7],
            ['nome' => 'Hidro Bomba',   'tipo' => 'Agua',   'poder' => 100, 'usos' => 3]
        ]);
    }
}