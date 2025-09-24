#!/usr/bin/env php
<?php
declare(strict_types=1);

// Simple native PHP test runner for key helpers.

$basePath = dirname(__DIR__);

require_once $basePath . '/app/helpers/credential_store.php';
require_once $basePath . '/app/helpers/remember.php';
require_once $basePath . '/app/helpers/validation.php';

if (!defined('REMEMBER_KEY')) {
    define('REMEMBER_KEY', str_repeat('R', 32));
}

$storageFiles = [
    $basePath . '/storage/credentials.json',
    $basePath . '/storage/remember_tokens.json',
];

$backups = [];
foreach ($storageFiles as $path) {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    $backups[$path] = file_exists($path) ? file_get_contents($path) : null;
    file_put_contents($path, "{}\n");
}

register_shutdown_function(function () use ($storageFiles, $backups): void {
    foreach ($storageFiles as $path) {
        if (array_key_exists($path, $backups) && $backups[$path] !== null) {
            file_put_contents($path, $backups[$path]);
        } else {
            @unlink($path);
        }
    }
});

class TestSuite
{
    private array $results = [];
    private string $currentGroup = 'default';

    public function run(string $group, callable $callback): void
    {
        $previous = $this->currentGroup;
        $this->currentGroup = $group;
        try {
            $callback($this);
        } finally {
            $this->currentGroup = $previous;
        }
    }

    public function assertTrue(bool $condition, string $message): void
    {
        $this->record($condition, $message, $condition ? null : 'Aspettato true');
    }

    public function assertFalse(bool $condition, string $message): void
    {
        $this->record(!$condition, $message, $condition ? 'Aspettato false' : null);
    }

    public function assertNull($value, string $message): void
    {
        $this->record($value === null, $message, 'Valore atteso null');
    }

    public function assertNotNull($value, string $message): void
    {
        $this->record($value !== null, $message, 'Valore non deve essere null');
    }

    public function assertEquals($expected, $actual, string $message): void
    {
        $this->record($expected === $actual, $message, sprintf('Atteso %s, ottenuto %s', var_export($expected, true), var_export($actual, true)));
    }

    private function record(bool $success, string $message, ?string $details): void
    {
        $this->results[] = [
            'group' => $this->currentGroup,
            'message' => $message,
            'success' => $success,
            'details' => $success ? '' : (string)$details,
        ];
    }

    public function report(): int
    {
        $failures = 0;
        foreach ($this->results as $result) {
            $status = $result['success'] ? '[OK]' : '[FAIL]';
            if (!$result['success']) {
                $failures++;
            }
            $line = sprintf('%s (%s) %s', $status, $result['group'], $result['message']);
            if (!$result['success'] && $result['details'] !== '') {
                $line .= ' → ' . $result['details'];
            }
            echo $line, PHP_EOL;
        }

        echo PHP_EOL, sprintf('Totale: %d, Successi: %d, Fallimenti: %d', count($this->results), count($this->results) - $failures, $failures), PHP_EOL;
        return $failures === 0 ? 0 : 1;
    }
}

$suite = new TestSuite();

$suite->run('Validation', function (TestSuite $t): void {
    $t->assertNull(Validation::nick('User_1'), 'Nick valido accettato');
    $t->assertNotNull(Validation::nick('1User'), 'Nick che non inizia con lettera rifiutato');
    $t->assertNull(Validation::password('Passw0rd.'), 'Password valida accettata');
    $t->assertNotNull(Validation::password('short'), 'Password troppo corta rifiutata');
    $t->assertNull(Validation::indirizzo('Via Roma 12, Torino'), 'Indirizzo valido accettato');
    $t->assertNotNull(Validation::indirizzo('Piazza Duomo'), 'Indirizzo senza formato richiesto rifiutato');
});

$suite->run('CredentialStore', function (TestSuite $t): void {
    $password = 'Str0ngPwd.';
    $generated = CredentialStore::createAndStore($password);
    $credentialId = $generated['credentialId'];

    $t->assertTrue(CredentialStore::isCredentialId($credentialId), 'Formato credentialId corretto');
    $t->assertTrue(CredentialStore::verify($credentialId, $password), 'Password corretta verificata');
    $t->assertFalse(CredentialStore::verify($credentialId, 'WrongPwd1.'), 'Password errata rifiutata');

    CredentialStore::delete($credentialId);
    $t->assertFalse(CredentialStore::verify($credentialId, $password), 'Credential eliminato non verificabile');
});

$suite->run('Remember token', function (TestSuite $t): void {
    $_COOKIE = [];
    $_SERVER['HTTPS'] = 'on';

    $ttl = 1; // secondo
    issueRememberToken(7, $ttl);

    $cookieName = remember_token_cookie_name();
    $t->assertTrue(isset($_COOKIE[$cookieName]), 'Cookie remember impostato');

    $cookieValue = $_COOKIE[$cookieName];
    $parsed = validateRememberTokenCookie($cookieValue);
    $t->assertNotNull($parsed, 'Cookie remember valido');

    $tokenId = $parsed['tokenId'] ?? '';
    $record = $tokenId ? remember_tokens_fetch($tokenId) : null;
    $t->assertNotNull($record, 'Token memorizzato recuperabile');
    if ($record !== null) {
        $t->assertEquals(7, (int)$record['user_id'], 'Token associato all\'utente corretto');
    }

    // Forza pruning dopo la scadenza
    $removed = remember_tokens_prune(($parsed['expires'] ?? time()) + 5);
    $t->assertEquals(1, $removed, 'Token scaduto rimosso dal pruning');
    $t->assertNull($tokenId ? remember_tokens_fetch($tokenId) : null, 'Token non più presente dopo pruning');

    clearRememberedCredentials();
    $t->assertFalse(isset($_COOKIE[$cookieName]), 'Cookie remember eliminato');

    $t->assertNull(validateRememberTokenCookie('token.invalid'), 'Cookie non valido rifiutato');
});

exit($suite->report());