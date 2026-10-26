<?php

class Beartic extends Pokemon {

    use Gelo;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(13);

        $this->setAtaques([
            ['nome' => 'Investida',     'tipo' => 'Normal', 'poder' => 50, 'usos' => null],
            ['nome' => 'Vento Gélido',  'tipo' => 'Gelo',   'poder' => 35, 'usos' => 5, 'efeito' => 'congelar'],
            ['nome' => 'Raio de Gelo',  'tipo' => 'Gelo',   'poder' => 90, 'usos' => 3]
        ]);
    }
}