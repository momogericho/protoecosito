<?php
require_once __DIR__ . '/crypto_utils.php';
const REMEMBER_TTL = 72 * 3600; // 72 hours


// Name of the remember token cookie
function remember_token_cookie_name(): string
{
    return 'remember_token';
}

// Path to store remember tokens
function remember_tokens_path(): string
{
    return __DIR__ . '/../../storage/remember_tokens.json';
}   

// issue a remember token for a user and set it as a cookie
function issueRememberToken(int $userId, int $ttl = REMEMBER_TTL): void
{
    $expiresAt = time() + $ttl;
    $tokenId = base64url_encode(random_bytes(15));
    $payload = $tokenId . '.' . $expiresAt;
    $mac = base64url_encode(hash_hmac('sha256', $payload, REMEMBER_KEY, true));

    remember_tokens_mutate(function (array &$data) use ($userId, $tokenId, $expiresAt): void {
        $now = time();
        foreach ($data as $id => $info) {
            if (($info['user_id'] ?? null) === $userId || ($info['expires_at'] ?? 0) <= $now) {
                unset($data[$id]);
            }
        }

     $data[$tokenId] = [
            'user_id' => $userId,
            'expires_at' => $expiresAt,
            'issued_at' => time(),
        ];
    });

    $cookieValue = $payload . '.' . $mac;
    setcookie(remember_token_cookie_name(), $cookieValue, [
        'expires'  => $expiresAt,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[remember_token_cookie_name()] = $cookieValue;
}

// clear the remember token cookie and remove the token from storage
function clearRememberedCredentials(?string $tokenId = null): void
{
    $cookieName = remember_token_cookie_name();
    $cookie = $_COOKIE[$cookieName] ?? null;
    if ($tokenId === null && $cookie) {
        $parsed = validateRememberTokenCookie($cookie);
        if ($parsed) {
            $tokenId = $parsed['tokenId'];
        }
    }

    if ($tokenId) {
        remember_tokens_mutate(function (array &$data) use ($tokenId): void {
            unset($data[$tokenId]);
        });
    }

    setcookie($cookieName, '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax',
    ]);
    unset($_COOKIE[$cookieName]);
}

// validate and parse a remember token cookie value
function validateRememberTokenCookie(string $cookieValue): ?array
{
    $parts = explode('.', $cookieValue);
    if (count($parts) !== 3) {
        return null;
    }

    [$tokenId, $expiresAt, $mac] = $parts;
    if ($tokenId === '' || $expiresAt === '' || !ctype_digit($expiresAt)) {
        return null;
    }

    $payload = $tokenId . '.' . $expiresAt;
    $expected = base64url_encode(hash_hmac('sha256', $payload, REMEMBER_KEY, true));
    if (!hash_equals($expected, $mac)) {
        return null;
    }

    return [
        'tokenId' => $tokenId,
        'expires' => (int)$expiresAt,
    ];
}

// fetch a remember token info by its ID
function remember_tokens_fetch(string $tokenId): ?array
{
    [$handle, $data] = remember_tokens_read();
    try {
        return $data[$tokenId] ?? null;
    } finally {
        remember_tokens_close($handle);
    }
}

// remove a remember token by its ID
function remember_tokens_remove(string $tokenId): void
{
    remember_tokens_mutate(function (array &$data) use ($tokenId): void {
        unset($data[$tokenId]);
    });
}

// prune expired remember tokens, return the number of removed tokens
function remember_tokens_prune(?int $now = null): int
{
    $now = $now ?? time();
    $removed = 0;
    remember_tokens_mutate(function (array &$data) use ($now, &$removed): void {
        foreach ($data as $id => $info) {
            if (($info['expires_at'] ?? 0) <= $now) {
                unset($data[$id]);
                $removed++;
            }
        }
    });

    return $removed;
}

// internal: read the remember tokens file with a lock
function remember_tokens_read(int $lock = LOCK_SH): array
{
    $path = remember_tokens_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }

    $handle = fopen($path, 'c+');
    if ($handle === false) {
        throw new RuntimeException('Impossibile aprire il file dei token remember-me.');
    }

    if (!flock($handle, $lock)) {
        fclose($handle);
        throw new RuntimeException('Impossibile bloccare il file dei token remember-me.');
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

// internal: mutate the remember tokens file with an exclusive lock
function remember_tokens_mutate(callable $callback): void
{
    [$handle, $data] = remember_tokens_read(LOCK_EX);
    try {
        $callback($data);
        $json = json_encode($data, JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new RuntimeException('Impossibile serializzare il file dei token remember-me.');
        }

        rewind($handle);
        if (!ftruncate($handle, 0)) {
            throw new RuntimeException('Impossibile troncare il file dei token remember-me.');
        }

        if (fwrite($handle, $json) === false) {
            throw new RuntimeException('Impossibile scrivere il file dei token remember-me.');
        }

        fflush($handle);
    } finally {
        remember_tokens_close($handle);
    }
}

// internal: close the remember tokens file and release the lock
function remember_tokens_close($handle): void
{
    flock($handle, LOCK_UN);
    fclose($handle);
}