<?php
/**
 * Retrieve remembered credentials from cookie.
 *
 * @return array{0:string,1:string} Array with username and password.
 */
function getRememberedCredentials() {
    if (empty($_COOKIE['remember_token'])) {
        return ["", ""];
    }

    $cipher = 'aes-256-gcm';
    $cookie = base64_decode($_COOKIE['remember_token'], true);
    if ($cookie === false) {
        return ["", ""];
    }

    $data = json_decode($cookie, true);
    if (!is_array($data) || !isset($data['user'], $data['pwd'], $data['iv'], $data['tag'])) {
        return ["", ""];
    }

    $iv = base64_decode($data['iv'], true);
    $tags = explode(':', $data['tag']);
    $tagUser = $tags[0] ?? '';
    $tagPwd  = $tags[1] ?? '';
    $encUser = base64_decode($data['user'], true);
    $encPwd  = base64_decode($data['pwd'], true);
    $tagUser = $tagUser !== '' ? base64_decode($tagUser, true) : false;
    $tagPwd  = $tagPwd !== '' ? base64_decode($tagPwd, true) : false;

    if ($iv === false || $encUser === false || $encPwd === false || $tagUser === false || $tagPwd === false) {
        return ["", ""];
    }

    $user = openssl_decrypt($encUser, $cipher, REMEMBER_KEY, OPENSSL_RAW_DATA, $iv, $tagUser);
    $pwd  = openssl_decrypt($encPwd, $cipher, REMEMBER_KEY, OPENSSL_RAW_DATA, $iv, $tagPwd);

    if ($user === false || $pwd === false) {
        return ["", ""];
    }

    return [$user, $pwd];
}

/**
 * Store credentials in a secure cookie for 72 hours.
 */
function setRememberedCredentials($user, $pwd) {
    $cipher = 'aes-256-gcm';
    $iv = random_bytes(openssl_cipher_iv_length($cipher));

    $tagUser = '';
    $encUser = openssl_encrypt($user, $cipher, REMEMBER_KEY, OPENSSL_RAW_DATA, $iv, $tagUser);

    $tagPwd = '';
    $encPwd = openssl_encrypt($pwd, $cipher, REMEMBER_KEY, OPENSSL_RAW_DATA, $iv, $tagPwd);

    $payload = [
        'user' => base64_encode($encUser),
        'pwd'  => base64_encode($encPwd),
        'iv'   => base64_encode($iv),
        'tag'  => base64_encode($tagUser) . ':' . base64_encode($tagPwd)
    ];

    $cookieValue = base64_encode(json_encode($payload));

    setcookie('remember_token', $cookieValue, [
        'expires'  => time() + 72 * 3600,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax'
    ]);
}

/**
 * Clear the remember-me cookie.
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
