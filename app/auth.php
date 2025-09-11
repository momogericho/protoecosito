<?php
require_once __DIR__ . "/../models/User.php";

class AuthController {
    private $pdo;
    private $userModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    // Funzione di login
    public function login($nick, $password, $remember) {
        $user = $this->userModel->getByNick($nick);

        if (!$user || !password_verify($password, $user['password'])) {
            return false; // credenziali errate
        }

        // Avvia sessione
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nick']    = $user['nick'];
        $_SESSION['artigiano'] = $user['artigiano'];
        $_SESSION['credit'] = $this->userModel->getCredit($user['id'], $user['artigiano']);

         session_regenerate_id(true);

        // Se "ricordami" attivo → salvo cookie per 72 ore
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->storeRememberToken($user['id'], $token);
            setcookie('remember_token', $token, [
                'expires'  => time()+72*3600,
                'path'     => '/',
                'httponly' => true,
                'secure'   => true
            ]);
        } else {
            $this->userModel->clearRememberToken($user['id']);
             setcookie('remember_token', '', [
                'expires'  => time()-3600,
                'path'     => '/',
                'httponly' => true,
                'secure'   => true
            ]);
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
        $userId = $_SESSION['user_id'] ?? null;
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        // Elimina cookie remember
        if ($userId) {
            $this->userModel->clearRememberToken($userId);
        }
         setcookie('remember_token', '', [
            'expires'  => time()-3600,
            'path'     => '/',
            'httponly' => true,
            'secure'   => true
        ]);

        header("Location: login.php");
        exit;
    }
}
