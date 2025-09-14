<?php

class AccessControl {
    // Check that the user is authenticated and is an azienda
    public static function requireAzienda(array $opts = []): void {
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

    // Check that the user is authenticated and is an artigiano
    public static function requireArtigiano(array $opts = []): void {
        $ok = !empty($_SESSION['user_id']) && isset($_SESSION['artigiano']) && (int)$_SESSION['artigiano'] === 1;
        if ($ok) return;

        // Allow rendering of domanda.php even when the user is not authenticated.
        // The page itself will handle showing a warning message.
        $script = basename($_SERVER['PHP_SELF'] ?? '');
        if ($script === 'domanda.php') return;


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
}