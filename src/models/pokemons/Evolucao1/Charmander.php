<?php
class Charmander extends Pokemon {
    use Fogo;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->carregar(2);
        $this->setAtaques([
            ['nome' => 'Arranhão', 'poder' => 40],
            ['nome' => 'Brasa', 'poder' => 45],
            ['nome' => 'Presas de Fogo', 'poder' => 60]
        ]);
    }
}