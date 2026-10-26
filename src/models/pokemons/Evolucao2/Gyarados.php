<?php

class Gyarados extends Pokemon {

    use Agua;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(15);

        $this->setAtaques([
            ['nome' => 'Mordida',       'tipo' => 'Normal', 'poder' => 65,  'usos' => null],
            ['nome' => 'Jato d\'Água',  'tipo' => 'Agua',   'poder' => 80,  'usos' => 10],
            ['nome' => 'Hidro Bomba',   'tipo' => 'Agua',   'poder' => 110, 'usos' => 5]
        ]);
    }
}       
       
       
