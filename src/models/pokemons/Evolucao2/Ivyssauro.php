<?php

class Ivyssauro extends Pokemon {

    use Planta;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(5);

        $this->setAtaques([
            ['nome' => 'Investida',         'tipo' => 'Normal', 'poder' => 50,  'usos' => null],
            ['nome' => 'Chicote de Vinha',  'tipo' => 'Planta', 'poder' => 70,  'usos' => 7],
            ['nome' => 'Folha Navalha',     'tipo' => 'Planta', 'poder' => 100, 'usos' => 3]
        ]);
    }
}