<?php

class Venossauro extends Pokemon {

    use Planta;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(9);

        $this->setAtaques([
            ['nome' => 'Investida',         'tipo' => 'Normal', 'poder' => 60,  'usos' => null],
            ['nome' => 'Chicote de Vinha',  'tipo' => 'Planta', 'poder' => 80,  'usos' => 2],
            ['nome' => 'Raio Solar',        'tipo' => 'Planta', 'poder' => 110, 'usos' => 2]
        ]);
    }
}