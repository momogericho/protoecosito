<?php
class Db {
    private static ?\PDO $pdo = null;

    private static function connect(): void {
        if (self::$pdo !== null) {
            return;
        }

        require_once __DIR__ . '/../vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();

        $dsn  = getenv('DB_DSN');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $caCert = getenv('MYSQL_CA_CERT');
        if (!$caCert) {
            throw new \RuntimeException('Certificato CA non configurato.');
        }

        self::$pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::MYSQL_ATTR_SSL_CA => $caCert,
            \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true, // verifica il certificato del server,disattivare solo in sviluppo
        ]);
    }

    public static function prepare(string $sql): \PDOStatement {
        self::connect();
        return self::$pdo->prepare($sql);
    }

    public static function beginTransaction(): bool {
        self::connect();
        return self::$pdo->beginTransaction();
    }

    public static function commit(): bool {
        return self::$pdo?->commit() ?? false;
    }

    public static function rollBack(): bool {
        return self::$pdo?->rollBack() ?? false;
    }

    public static function lastInsertId(): string {
        return self::$pdo?->lastInsertId() ?? '0';
    }

    public static function inTransaction(): bool {
        return self::$pdo?->inTransaction() ?? false;
    }
}