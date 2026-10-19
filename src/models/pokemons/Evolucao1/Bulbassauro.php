<?php

class Bulbassauro extends Pokemon {

    use Planta;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(1);

        $this->setAtaques([
            ['nome' => 'Investida',         'tipo' => 'Normal', 'poder' => 40, 'usos' => null],
            ['nome' => 'Chicote de Vinha',  'tipo' => 'Planta', 'poder' => 60, 'usos' => 5],
            ['nome' => 'Folha Navalha',     'tipo' => 'Planta', 'poder' => 90, 'usos' => 2]
        ]);
    }
}