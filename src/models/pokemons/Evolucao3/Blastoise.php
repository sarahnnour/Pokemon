<?php

class Blastoise extends Pokemon {

    use Agua;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(11);

        $this->setAtaques([
            ['nome' => 'Investida',      'tipo' => 'Normal', 'poder' => 60,  'usos' => null],
            ['nome' => 'Jato d\'Água',   'tipo' => 'Agua',   'poder' => 80,  'usos' => 10],
            ['nome' => 'Hidro Bomba',    'tipo' => 'Agua',   'poder' => 110, 'usos' => 5]
        ]);
    }
}