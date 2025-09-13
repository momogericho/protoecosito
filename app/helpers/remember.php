<?php
function getRememberToken() {
    return $_COOKIE['remember_token'] ?? "";
}

function setRememberToken($token) {
    setcookie('remember_token', $token, [
        'expires'  => time() + 72*3600,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax'
    ]);}

function clearRememberToken() {
    setcookie('remember_token', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax'
    ]);}
?>
