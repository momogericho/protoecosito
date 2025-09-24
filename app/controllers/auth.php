<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/UserRepository.php';
require_once __DIR__ . '/../helpers/remember.php';
require_once __DIR__ . '/../../models/UserRepository.php';


class AuthController {
    private $userModel;
    private $userRepository;

    public function __construct() {
        $this->userModel = new User();
        $this->userRepository = new UserRepository();
    }

    // Funzione di login
    public function login($nick, $password, $remember) {
        $user = $this->userModel->getByNick($nick);

        if (!$user) {
            return false; // credenziali errate
        }

        $passwordVerified = false;
        $storedValue = (string)$user['password'];

        if (CredentialStore::isCredentialId($storedValue)) {
            $passwordVerified = CredentialStore::verify($storedValue, $password);
        } elseif (hash_equals($storedValue, $password)) {
            $passwordVerified = true;
            $user = $this->migrateLegacyCredential($user, $password);
            clearRememberedCredentials();
        }

        if (!$passwordVerified) {
            return false;
        }

        $this->finalizeLogin($user);

        // Se "ricordami" attivo → genero un token server-side
        if ($remember) {
            issueRememberToken((int)$user['id']);
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
    public function loginWithToken($cookieValue) {
        if (!$cookieValue) {
            return false;
        }

        $parsed = validateRememberTokenCookie($cookieValue);
        if (!$parsed) {
            clearRememberedCredentials();
            return false;
        }

        if ($parsed['expires'] < time()) {
            clearRememberedCredentials($parsed['tokenId']);
            return false;
        }

        $record = remember_tokens_fetch($parsed['tokenId']);
        if (!$record) {
            clearRememberedCredentials($parsed['tokenId']);
            return false;
        }

        if (($record['expires_at'] ?? 0) < time()) {
            clearRememberedCredentials($parsed['tokenId']);
            return false;
        }

        $user = $this->userModel->getById((int)$record['user_id']);
        if (!$user) {
            clearRememberedCredentials($parsed['tokenId']);
            remember_tokens_remove($parsed['tokenId']);
            return false;
        }

        $this->finalizeLogin($user);
        issueRememberToken((int)$user['id']);

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

    // Imposta le variabili di sessione e rigenera l'ID di sessione
     private function finalizeLogin(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nick']    = $user['nick'];
        $_SESSION['artigiano'] = $user['artigiano'];
        $_SESSION['credit'] = $this->userModel->getCredit($user['id'], $user['artigiano']);

        session_regenerate_id(true);
    }

    // Migra una password legacy a un nuovo sistema di credenziali
    private function migrateLegacyCredential(array $user, string $password): array
    {
        $generated = CredentialStore::generateCredential($password);
        $credentialId = $generated['credentialId'];

        try {
            $this->userRepository->updateCredentialId((int)$user['id'], $credentialId);
            CredentialStore::store($credentialId, $generated['record']);
        } catch (Throwable $ex) {
            CredentialStore::delete($credentialId);
            $this->userRepository->updateCredentialId((int)$user['id'], $user['password']);
            throw $ex;
        }

        return $this->userModel->getById((int)$user['id']);
    }
}