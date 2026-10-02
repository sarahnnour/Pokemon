<?php

spl_autoload_register(function ($classe) {
    // Lista de diretórios onde o Autoload deve buscar
    $diretorios = [
        __DIR__ . '/src/class/',
        __DIR__ . '/src/controllers/'
    ];

    foreach ($diretorios as $diretorio) {
        $arquivo = $diretorio . $classe . '.php';

        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});