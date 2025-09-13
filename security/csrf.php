<?php
// csrf.php
require_once __DIR__ . '/../app/helpers/session_helpers.php';
startSecureSession();


/**
 * Genera e restituisce un token CSRF univoco
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica se il token CSRF fornito è valido
 */
function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    $isValid = hash_equals($_SESSION['csrf_token'], $token);
    // Una volta validato lo elimino (usa e getta)
    unset($_SESSION['csrf_token']);
    return $isValid;
}
