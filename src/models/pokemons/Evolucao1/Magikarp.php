<?php

class Magikarp extends Pokemon {

    use Agua;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(21);

        // usos = null -> ilimitado | usos = 2 -> só pode usar 2 vezes
        // efeito = 'queimar' -> deixa o adversário queimado
        $this->setAtaques([
            ['nome' => 'Investida',       'tipo' => 'Normal', 'poder' => 35, 'usos' => null],
            ['nome' => 'Jato de Bolhas',  'tipo' => 'Agua',   'poder' => 40, 'usos' => 5],
            ['nome' => 'Jato d\'Água',    'tipo' => 'Agua',   'poder' => 50, 'usos' => 2]

        ]);
    }
}