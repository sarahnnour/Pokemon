<?php

trait Fogo {

    public function getForteContra(): array {
        return ['Planta'];
    }

    public function getFracoContra(): array {
        return ['Agua', 'Fogo'];
    }
}