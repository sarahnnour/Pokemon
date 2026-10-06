<?php
/**
 * Temos que lembrar de usar em todos os outro arquivos:
 *   require_once 'conexao.php';
 *   $pdo = getConnection();
 */

function getConnection(): PDO
{
    $host    = "localhost";
    $dbname  = "jogo_pokemon";  
    $user    = "root";
    $pass    = "";          
    $charset = "utf8mb4";


    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    try {
        $conn = new PDO($dsn, $user, $pass);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $conn;
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
}


// Para testes:

/*if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $pdo = getConnection();
    echo "Conectado com sucesso!";
} */