<?php
// Conexão com o banco usando PDO.
// Nos controllers: require_once 'Conexao.php';  $pdo = getConnection();

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
