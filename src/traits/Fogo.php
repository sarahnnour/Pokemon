<?php

trait Fogo {

    // Causar 2x de dano
    public function getForteContra(): array {
        return ['Planta', 'Gelo'];
    }

    // Causar 0.5x de dano
    public function getFracoContra(): array {
        return ['Agua', 'Fogo'];
    }
}