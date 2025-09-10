<?php
function getRememberToken() {
    return $_COOKIE['remember_token'] ?? "";
}

function setRememberToken($token) {
    setcookie('remember_token', $token, time() + 60*60*24*3, "/");
}

function clearRememberToken() {
    setcookie('remember_token', '', time() - 3600, "/");
}
?>
