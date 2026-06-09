<?php
namespace App\Database;

use PDO;
use PDOException;
use Exception;
use PDOStatement;

class Database
{
    private PDO $connection;
    
    public function __construct()
    {
        $this->connection = $this->createConnection();
    }
    
    private function createConnection(): PDO
    {
        $configPath = __DIR__ . '/../../../core/conf/database.config.php';
        
        if (!file_exists($configPath)) {
            throw new Exception("Database config not found: $configPath");
        }
        
        $config = require $configPath;
        
        $driver = $config['driver'] ?? 'mysql';
        $host = $config['host'] ?? 'localhost';
        $user = $config['user'] ?? 'root';
        $password = $config['password'] ?? '';
        $dbname = $config['dbname'] ?? 'museum';
        
        $dsn = match($driver) {
            'mysql' => "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            'pgsql' => "pgsql:host=$host;dbname=$dbname",
            'sqlite' => "sqlite:$dbname",
            'sqlsrv' => "sqlsrv:Server=$host;Database=$dbname",
            'oci' => "oci:dbname=//$host:1521/$dbname",
            default => throw new Exception("Unsupported driver: $driver")
        };
        
        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
    
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
    public function prepare(string $sql): PDOStatement
    {
        return $this->connection->prepare($sql);
    }
    
    public function query(string $sql): PDOStatement
    {
        return $this->connection->query($sql);
    }
}