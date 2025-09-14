<?php
/**
 * Retrieve remembered credentials from the remember_token cookie.
 *
 * @return array{0:string,1:string} Array with username and password.
 */
function getRememberedCredentials() {
    if (empty($_COOKIE['remember_token'])) {
        return ["", ""];
    }

    $cipher = 'aes-256-gcm';
    $cookie = $_COOKIE['remember_token'];

    $data = json_decode($cookie, true);
    if (!is_array($data) || !isset($data['ciphertext'], $data['iv'], $data['tag'])) {
        return ["", ""];
    }

    $iv = hex2bin($data['iv']);
    $ciphertext = hex2bin($data['ciphertext']);
    $tag = hex2bin($data['tag']);

    if ($iv === false || $ciphertext === false || $tag === false) {
        return ["", ""];
    }

    $json = openssl_decrypt($ciphertext, $cipher, REMEMBER_KEY, OPENSSL_RAW_DATA, $iv, $tag);
    if ($json === false) {
        return ["", ""];
    }

    $creds = json_decode($json, true);
    if (!is_array($creds) || !isset($creds['user'], $creds['pwd'])) {
        return ["", ""];    }

    return [$creds['user'], $creds['pwd']];
}

/**
 * Store credentials in a remember_token cookie for 72 hours.
 */
function setRememberedCredentials($user, $pwd) {
    $cipher = 'aes-256-gcm';
    $iv = random_bytes(openssl_cipher_iv_length($cipher));

    $payload = json_encode(['user' => $user, 'pwd' => $pwd]);

    $tag = '';
    $ciphertext = openssl_encrypt($payload, $cipher, REMEMBER_KEY, OPENSSL_RAW_DATA, $iv, $tag);
    
    $data = [
        'ciphertext' => bin2hex($ciphertext),
        'iv' => bin2hex($iv),
        'tag' => bin2hex($tag)
    ];

    $cookieValue = json_encode($data);

    setcookie('remember_token', $cookieValue, [
        'expires'  => time() + 72 * 3600,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax'
    ]);
}

/**
 * Clear the remember_token cookie.
 */
function clearRememberedCredentials() {
        setcookie('remember_token', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax'
    ]);
}
?>
