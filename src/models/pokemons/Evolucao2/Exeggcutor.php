<?php

class Exeggcutor extends Pokemon {

    use Planta;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(14);

        $this->setAtaques([
            ['nome' => 'Investida',          'tipo' => 'Normal', 'poder' => 50,  'usos' => null],
            ['nome' => 'Mega Dreno',         'tipo' => 'Planta', 'poder' => 70,  'usos' => 7],
            ['nome' => 'Bomba de Sementes',  'tipo' => 'Planta', 'poder' => 100, 'usos' => 3]
        ]);
    }
}