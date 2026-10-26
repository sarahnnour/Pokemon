<?php

class Zeraora extends Pokemon {

    use Raio;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(19);

        // usos = null -> ilimitado | usos = 2 -> só pode usar 2 vezes
        // efeito = 'queimar' -> deixa o adversário queimado
        $this->setAtaques([
            ['nome' => 'Investida',        'tipo' => 'Normal', 'poder' => 45,  'usos' => null],
            ['nome' => 'Faísca',           'tipo' => 'Raio',   'poder' => 65,  'usos' => 5],
            ['nome' => 'Soco de Plasma',   'tipo' => 'Raio',   'poder' => 100, 'usos' => 2]

        ]);
    }
}
