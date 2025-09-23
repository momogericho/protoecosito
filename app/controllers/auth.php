<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../helpers/remember.php';


class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Funzione di login
    public function login($nick, $password, $remember) {
        $user = $this->userModel->getByNick($nick);

        if (!$user || $user['password'] !== $password) {
            return false; // credenziali errate
        }

        // Avvia sessione
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nick']    = $user['nick'];
        $_SESSION['artigiano'] = $user['artigiano'];
        $_SESSION['credit'] = $this->userModel->getCredit($user['id'], $user['artigiano']);

         session_regenerate_id(true);

        // Se "ricordami" attivo → salvo credenziali nel cookie remember_token per 72 ore
        if ($remember) {
            setRememberedCredentials($nick, $password);
        } else {
            clearRememberedCredentials();
        }

        // Redirigi in base al ruolo
        if ($user['artigiano']) {
            header("Location: domanda.php");
        } else {
            header("Location: offerta.php");
        }
        exit;
    }

     // Autenticazione tramite token
    public function loginWithToken($token) {
        $user = $this->userModel->getByRememberToken($token);
        if (!$user) {
            return false;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nick']    = $user['nick'];
        $_SESSION['artigiano'] = $user['artigiano'];
        $_SESSION['credit'] = $this->userModel->getCredit($user['id'], $user['artigiano']);

        session_regenerate_id(true);

        return true;
    }

    // Funzione di logout
    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        // Elimina cookie remember_token
        clearRememberedCredentials();

        header("Location: login.php");
        exit;
    }
}
