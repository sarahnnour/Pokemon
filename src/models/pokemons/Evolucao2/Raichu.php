<?php

class Raichu extends Pokemon {

    use Raio;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(8);

        $this->setAtaques([
            ['nome' => 'Investida',          'tipo' => 'Normal', 'poder' => 50,  'usos' => null],
            ['nome' => 'Choque do Trovão',   'tipo' => 'Raio',   'poder' => 75,  'usos' => 7],
            ['nome' => 'Trovão',             'tipo' => 'Raio',   'poder' => 105, 'usos' => 3]
        ]);
    }
}