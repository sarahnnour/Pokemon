<?php

trait Agua {

    public function getForteContra(): array {
        return ['Fogo'];
    }

    public function getFracoContra(): array {
        return ['Planta','Agua'];
    }
}