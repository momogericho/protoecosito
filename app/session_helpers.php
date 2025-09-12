<?php
require_once __DIR__ . '/storage_permissions.php';
/**
 * Aggiorna i dati dell'utente in sessione interrogando il DB.
 * Ritorna false se l'utente non esiste o se il ruolo non coincide.
 */
function refreshUserInfo(): bool {
    if (empty($_SESSION['user_id'])) {
        return true; // nulla da aggiornare
    }

    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../models/User.php';

    $userModel = new User();

    $st = Db::prepare('SELECT artigiano FROM utenti WHERE id = :id LIMIT 1');
    $st->execute([':id' => $_SESSION['user_id']]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return false; // utente non trovato
    }

    $role = (int)$row['artigiano'];

    if (isset($_SESSION['artigiano']) && (int)$_SESSION['artigiano'] !== $role) {
        return false; // ruolo modificato → possibile hijacking
    }

    // allinea sessione con i dati correnti
    $_SESSION['artigiano'] = $role;
    $_SESSION['credit'] = $userModel->getCredit($_SESSION['user_id'], $role);

    return true;
}


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
    if (!refreshUserInfo()) {
        // dati incoerenti → invalida sessione
        $_SESSION = [];
        session_destroy();
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