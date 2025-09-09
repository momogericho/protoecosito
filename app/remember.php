<?php
function getRememberCookies() {
    return [
        'user' => $_COOKIE['remember_user'] ?? "",
        'pwd'  => $_COOKIE['remember_pwd'] ?? ""
    ];
}

function setRememberCookies($user, $pwd) {
    setcookie('remember_user', $user, time() + 60*60*24*3, "/");
    setcookie('remember_pwd', $pwd, time() + 60*60*24*3, "/");
}

function clearRememberCookies() {
    setcookie('remember_user', '', time() - 3600, "/");
    setcookie('remember_pwd', '', time() - 3600, "/");
}
?>