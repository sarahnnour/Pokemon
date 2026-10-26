<?php

class Exeggcute extends Pokemon {

    use Planta;

    public function __construct($pdo) {
        parent::__construct($pdo);

        $this->carregar(20);

        // usos = null -> ilimitado | usos = 2 -> só pode usar 2 vezes
        // efeito = 'queimar' -> deixa o adversário queimado
        $this->setAtaques([
            ['nome' => 'Investida',          'tipo' => 'Normal', 'poder' => 40, 'usos' => null],
            ['nome' => 'Mega Dreno',         'tipo' => 'Planta', 'poder' => 60, 'usos' => 5],
            ['nome' => 'Bomba de Sementes',  'tipo' => 'Planta', 'poder' => 90, 'usos' => 2]
        ]);
    }
}