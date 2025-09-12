<?php
require_once __DIR__ . '/storage_permissions.php';
/**
 * Utility helpers per sessioni e controlli di ruolo (azienda/artigiano).
 */
function startSecureSession(): void {
    // Avvia sessione sicura se non già avviata
    if (session_status() === PHP_SESSION_NONE) {
        checkStoragePermissions(__DIR__ . '/../storage');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        // Limitiamo la cache della sessione a 60 minuti, in linea con la durata media di utilizzo.
        session_cache_expire(60);
        session_start();
    }
}

function requireAzienda(array $opts = []): void {
    // Controlla che l'utente sia autenticato e sia un'azienda
    $ok = !empty($_SESSION['user_id']) && isset($_SESSION['artigiano']) && (int)$_SESSION['artigiano'] === 0;
    if ($ok) return;

    $mode = $opts['mode'] ?? 'web';
    $redirect = $opts['redirect'] ?? null;

    if ($mode === 'json') {
        http_response_code(403);
        echo json_encode(['error' => 'Accesso negato']);
    } elseif ($redirect) {
        header('Location: ' . $redirect);
    } else {
        http_response_code(403);
        echo 'Accesso negato';
    }
    exit;
}

function requireArtigiano(array $opts = []): void {
    // Controlla che l'utente sia autenticato e sia un'artigiano
    $ok = !empty($_SESSION['user_id']) && isset($_SESSION['artigiano']) && (int)$_SESSION['artigiano'] === 1;
    if ($ok) return;

    $mode = $opts['mode'] ?? 'web';
    $redirect = $opts['redirect'] ?? null;

    if ($mode === 'json') {
        http_response_code(403);
        echo json_encode(['error' => 'Accesso negato']);
    } elseif ($redirect) {
        header('Location: ' . $redirect);
    } else {
        http_response_code(403);
        echo 'Accesso negato';
    }
    exit;
}