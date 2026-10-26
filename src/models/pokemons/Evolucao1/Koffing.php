<?php

class Koffing extends Pokemon {

    use Veneno;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(17);

        // usos = null -> ilimitado | usos = 2 -> só pode usar 2 vezes
        // efeito = 'queimar' -> deixa o adversário queimado
        $this->setAtaques([
            ['nome' => 'Investida',       'tipo' => 'Normal', 'poder' => 40, 'usos' => null],
            ['nome' => 'Gás Venenoso',    'tipo' => 'Veneno', 'poder' => 25, 'usos' => 3, 'efeito' => 'envenenar'],
            ['nome' => 'Bomba de Lodo',   'tipo' => 'Veneno', 'poder' => 80, 'usos' => 2]

        ]);
    }
}