<?php declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;
    private static string $dbPath;

    public static function init(string $dbPath): void
    {
        self::$dbPath = $dbPath;
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                // Ensure database directory exists
                $dbDir = dirname(self::$dbPath);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0755, true);
                }

                self::$connection = new PDO(
                    'sqlite:' . self::$dbPath,
                    null,
                    null,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );

                // Enable foreign keys for SQLite
                self::$connection->exec('PRAGMA foreign_keys = ON');
                
            } catch (PDOException $e) {
                throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    public static function execute(string $sql, array $params = []): bool
    {
        return self::query($sql, $params)->rowCount() > 0;
    }

    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }

    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }

    public static function runMigration(string $sqlFile): void
    {
        if (!file_exists($sqlFile)) {
            throw new \RuntimeException("Migration file not found: $sqlFile");
        }

        $sql = file_get_contents($sqlFile);
        $pdo = self::getConnection();
        
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            throw new \RuntimeException('Migration failed: ' . $e->getMessage());
        }
    }

    public static function tableExists(string $tableName): bool
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name=?";
        $result = self::fetchOne($sql, [$tableName]);
        return $result !== null;
    }
}
