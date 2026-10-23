<?php

class Pikachu extends Pokemon {

    use Raio;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(4);

        $this->setAtaques([
            ['nome' => 'Investida',          'tipo' => 'Normal', 'poder' => 45, 'usos' => null],
            ['nome' => 'Choque do Trovão',   'tipo' => 'Raio',   'poder' => 65, 'usos' => 5],
            ['nome' => 'Trovão',             'tipo' => 'Raio',   'poder' => 95, 'usos' => 2]
        ]);
    }
}