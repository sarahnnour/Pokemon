<?php

class Conexao
{
	private static $conexao = null;

	public static function conectar()
	{
		if (self::$conexao === null) {
			$host = getenv('DB_HOST') ?: 'localhost';
			$dbname = getenv('DB_NAME') ?: 'jogo_pokemon';
			$username = getenv('DB_USER') ?: 'root';
			$password = getenv('DB_PASS') ?: '';

			$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

			try {
				self::$conexao = new PDO($dsn, $username, $password, [
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES => false,
				]);
			} catch (PDOException $e) {
				throw new PDOException('Erro na conexão: ' . $e->getMessage(), (int) $e->getCode(), $e);
			}
		}

		return self::$conexao;
	}
}
