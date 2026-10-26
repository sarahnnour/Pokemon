<?php

trait Planta {

    // Causar 2x de dano
    public function getForteContra(): array {
        return ['Agua'];
    }

    // Causar 0.5x de dano
    public function getFracoContra(): array {
        return ['Fogo', 'Planta', 'Veneno'];
    }
}