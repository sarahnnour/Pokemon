<?php

class Raichu extends Pokemon {

    use Raio;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(8);

        $this->setAtaques([
            ['nome' => 'Investida',          'tipo' => 'Normal', 'poder' => 50,  'usos' => null],
            ['nome' => 'Choque do Trovão',   'tipo' => 'Raio',   'poder' => 70,  'usos' => 2],
            ['nome' => 'Trovão',             'tipo' => 'Raio',   'poder' => 100, 'usos' => 2]
        ]);
    }
}