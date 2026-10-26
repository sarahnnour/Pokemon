<?php

class Weezing extends Pokemon {

    use Veneno;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(12);

        $this->setAtaques([
            ['nome' => 'Investida',       'tipo' => 'Normal', 'poder' => 50, 'usos' => null],
            ['nome' => 'Gás Venenoso',    'tipo' => 'Veneno', 'poder' => 35, 'usos' => 5, 'efeito' => 'envenenar'],
            ['nome' => 'Bomba de Lodo',   'tipo' => 'Veneno', 'poder' => 90, 'usos' => 3]
        ]);
    }
}       
       
       
