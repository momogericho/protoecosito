<?php
require_once __DIR__ . '/crypto_utils.php';

class CredentialStore
{
    private const STORAGE_FILE = __DIR__ . '/../../storage/credentials.json';
    private const DERIVED_LENGTH = 15; // bytes → 20 chars once base64url encoded
    private const PBKDF2_ITERATIONS = 310000;
    private const VERSION = 1;

    /**
     * Generate a new credential identifier and derive a password hash.
     *
     * @return array{credentialId:string,record:array}
     */
    public static function generateCredential(string $password): array
    {
        do {
            $credentialId = self::generateIdentifier();
        } while (self::fetch($credentialId));
        $record = self::derivePassword($password);

        return [
            'credentialId' => $credentialId,
            'record' => $record,
        ];
    }

    /**
     * Persist the credential record to disk.
     */
    public static function store(string $credentialId, array $record): void
    {
        self::mutateStore(function (array &$data) use ($credentialId, $record): void {
            $data[$credentialId] = $record;
        });
    }

    /**
     * Remove a credential record from disk.
     */
    public static function delete(?string $credentialId): void
    {
        if (!$credentialId || !self::isCredentialId($credentialId)) {
            return;
        }

        self::mutateStore(function (array &$data) use ($credentialId): void {
            unset($data[$credentialId]);
        });
    }

    /**
     * Retrieve a stored credential record.
     */
    public static function fetch(string $credentialId): ?array
    {
        [$handle, $data] = self::readStore();
        try {
            return $data[$credentialId] ?? null;
        } finally {
            self::closeHandle($handle);
        }
    }

    /**
     * Verify that the provided password matches the stored credential.
     */
    public static function verify(string $credentialId, string $password): bool
    {
        $record = self::fetch($credentialId);
        if (!$record) {
            return false;
        }

        $salt = base64url_decode($record['salt'] ?? '');
        $hash = base64url_decode($record['hash'] ?? '');
        if ($salt === false || $hash === false) {
            return false;
        }

        $algo = $record['algo'] ?? '';
        if ($algo !== 'pbkdf2-sha256') {
            return false;
        }

        $iterations = $record['params']['iterations'] ?? self::PBKDF2_ITERATIONS;
        $calc = hash_pbkdf2('sha256', $password, $salt, (int)$iterations, self::DERIVED_LENGTH, true);
        return hash_equals($hash, $calc);
    }

    /**
     * Determine if the value stored in DB already matches the new credential format.
     */
    public static function isCredentialId(string $value): bool
    {
        return (bool)preg_match('/^[A-Za-z0-9_-]{20}$/', $value);
    }

    /**
     * Create a credential directly from a plain-text password and store it.
     *
     * @return array{credentialId:string}
     */
    public static function createAndStore(string $password): array
    {
        $generated = self::generateCredential($password);
        self::store($generated['credentialId'], $generated['record']);
        return ['credentialId' => $generated['credentialId']];
    }

    /**
     * Summary of derivePassword
     */
    private static function derivePassword(string $password): array
    {
        $salt = random_bytes(16);
        $iterations = self::PBKDF2_ITERATIONS;
        $hash = hash_pbkdf2('sha256', $password, $salt, $iterations, self::DERIVED_LENGTH, true);
        $algo = 'pbkdf2-sha256';
        $params = [
            'iterations' => $iterations,
        ];

        return [
            'algo' => $algo,
            'hash' => base64url_encode($hash),
            'salt' => base64url_encode($salt),
            'params' => $params,
            'version' => self::VERSION,
            'created_at' => time(),
        ];
    }

    /**
     * Generate a random credential identifier.
     */
    private static function generateIdentifier(): string
    {
        return base64url_encode(random_bytes(self::DERIVED_LENGTH));
    }

    /**
     * Execute a mutation on the credential store acquiring an exclusive lock.
     */
    private static function mutateStore(callable $callback): void
    {
        [$handle, $data] = self::readStore(LOCK_EX);
        try {
            $callback($data);
            self::writeStore($handle, $data);
        } finally {
            self::closeHandle($handle);
        }
    }

    /**
     * @return array{0:resource,1:array}
     */
    private static function readStore(int $lock = LOCK_SH): array
    {
        $path = self::STORAGE_FILE;
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }

        $handle = fopen($path, 'c+');
        if ($handle === false) {
            throw new RuntimeException('Impossibile aprire il file credenziali.');
        }

        if (!flock($handle, $lock)) {
            fclose($handle);
            throw new RuntimeException('Impossibile acquisire il lock sul file credenziali.');
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        $data = [];
        if ($contents !== false && trim($contents) !== '') {
            $decoded = json_decode($contents, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        return [$handle, $data];
    }

    /**
     * Write the credential store data back to disk.
     */
    private static function writeStore($handle, array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new RuntimeException('Impossibile serializzare il file credenziali.');
        }

        rewind($handle);
        if (!ftruncate($handle, 0)) {
            throw new RuntimeException('Impossibile troncare il file credenziali.');
        }

        if (fwrite($handle, $json) === false) {
            throw new RuntimeException('Impossibile scrivere il file credenziali.');
        }

        fflush($handle);
    }

    /**
     * Release the lock and close the file handle.
     */
    private static function closeHandle($handle): void
    {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}