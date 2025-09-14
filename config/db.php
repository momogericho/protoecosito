<?php
class Db {

    private static ?\PDO $pdoRead = null;
    private static ?\PDO $pdoWrite = null;
    private static bool $envLoaded = false;

     private static function loadEnv(): void {
        if (self::$envLoaded) {
            return;
        }

        require_once __DIR__ . '/../vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
        self::$envLoaded = true;
    }
        public static function connectRead(): \PDO {
        if (self::$pdoRead !== null) {
            return self::$pdoRead;
        }
        self::loadEnv();
        $dsn = getenv('DB_DSN');
        $user = getenv('DB_USER_READ');
        $pass = getenv('DB_PASS_READ');
        $caCert = getenv('MYSQL_CA_CERT');
        if (!$caCert) {
            throw new \RuntimeException('Certificato CA non configurato.');
        }
        self::$pdoRead = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::MYSQL_ATTR_SSL_CA => $caCert,
            \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
        ]);
        return self::$pdoRead;
    }

public static function connectWrite(): \PDO {
        if (self::$pdoWrite !== null) {
            return self::$pdoWrite;
        }
        self::loadEnv();
        $dsn = getenv('DB_DSN');
        $user = getenv('DB_USER_WRITE');
        $pass = getenv('DB_PASS_WRITE');
        $caCert = getenv('MYSQL_CA_CERT');
        if (!$caCert) {
            throw new \RuntimeException('Certificato CA non configurato.');
        }
        self::$pdoWrite = new \PDO($dsn, $user, $pass, [            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::MYSQL_ATTR_SSL_CA => $caCert,
            \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true, // verifica il certificato del server,disattivare solo in sviluppo
        ]);
        return self::$pdoWrite;
    }

    public static function prepareRead(string $sql): \PDOStatement {
        return self::connectRead()->prepare($sql);
    }

    public static function prepareWrite(string $sql): \PDOStatement {
        return self::connectWrite()->prepare($sql);
    }

    public static function beginTransaction(): bool {
        return self::connectWrite()->beginTransaction();

    }

    public static function commit(): bool {
        return self::$pdoWrite?->commit() ?? false;
    }

    public static function rollBack(): bool {
        return self::$pdoWrite?->rollBack() ?? false;
    }

    public static function lastInsertId(): string {
        return self::$pdoWrite?->lastInsertId() ?? '0';
    }

    public static function inTransaction(): bool {
        return self::$pdoWrite?->inTransaction() ?? false;
    }
}