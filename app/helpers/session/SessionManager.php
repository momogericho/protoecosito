<?php
require_once __DIR__ . '/../storage_permissions.php';

class SessionManager {
    /**
     * Refresh user information in session querying the DB.
     * Returns false if user not found or role mismatch.
     */
    public static function refreshUserInfo(): bool {
        if (empty($_SESSION['user_id'])) {
            return true; // nothing to refresh
        }

        require_once __DIR__ . '/../../../config/db.php';
        require_once __DIR__ . '/../../../models/User.php';

        $userModel = new User();

        $st = Db::prepareRead('SELECT artigiano FROM utenti WHERE id = :id LIMIT 1');
        $st->execute([':id' => $_SESSION['user_id']]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false; // user not found
        }

        $role = (int)$row['artigiano'];

        if (isset($_SESSION['artigiano']) && (int)$_SESSION['artigiano'] !== $role) {
            return false; // role changed → possible hijacking
        }

        // align session with current data
        $_SESSION['artigiano'] = $role;
        $_SESSION['credit'] = $userModel->getCredit($_SESSION['user_id'], $role);

        return true;
    }

    /**
     * Start a secure session and validate stored user data.
     */
    public static function startSecureSession(): void {
        // Start secure session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            checkStoragePermissions(__DIR__ . '/../../../storage');
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            // Limit session cache to 60 minutes
            session_cache_expire(60);
            session_start();
        }
        if (!self::refreshUserInfo()) {
            // inconsistent data → invalidate session
            $_SESSION = [];
            session_destroy();
        }
    }
}