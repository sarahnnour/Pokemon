<?php

class Pikachu extends Pokemon {

    use Raio;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(4);

        $this->setAtaques([
            ['nome' => 'Investida',          'tipo' => 'Normal', 'poder' => 40, 'usos' => null],
            ['nome' => 'Choque do Trovão',   'tipo' => 'Raio',   'poder' => 60, 'usos' => 2],
            ['nome' => 'Trovão',             'tipo' => 'Raio',   'poder' => 90, 'usos' => 2]
        ]);
    }
}