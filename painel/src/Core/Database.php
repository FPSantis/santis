<?php

namespace Painel\Core;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?PDO $instance = null;

    /**
     * Retorna a Instância Singleton PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::connect();
        }
        return self::$instance;
    }

    /**
     * Estabelece Conexão inicial e Trava a Instância
     */
    private static function connect()
    {
        $host = getenv('DB_HOST') ?: 'db';
        $port = getenv('DB_PORT') ?: '3306';
        $db   = getenv('DB_DATABASE') ?: 'db';
        $user = getenv('DB_USERNAME') ?: 'db';
        $pass = getenv('DB_PASSWORD') ?: 'db';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Arrays associativos sempre
                PDO::ATTR_EMULATE_PREPARES   => false, // Segurança: Força o banco a preparar as queries (Evita SQL Injection nativamente)
            ]);
        } catch (PDOException $e) {
            // Em produção não podemos exibir a string do PDO completa, pode vazar IPs de portas
            if (getenv('APP_ENV') === 'local') {
                throw new Exception("Falha Crítica no Banco de Dados: " . $e->getMessage());
            } else {
                throw new Exception("O sistema não pôde estabecer conexão com a base de dados.");
            }
        }
    }

    /**
     * Bloqueia o clone
     */
    private function __clone() {}

    /**
     * Bloqueia a reconstrução do objeto
     */
    public function __wakeup()
    {
        throw new Exception("Não é possível desserializar uma instância singleton (Database).");
    }
}
