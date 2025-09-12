<?php
require_once __DIR__ . '/session_helpers.php';
startSecureSession();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../security/csrf.php";
require_once __DIR__ . "/validation.php";

$auth = new AuthController();
$error = "";

// Se già loggato → ridirigi
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['artigiano']) {
        header("Location: domanda.php");
    } else {
        header("Location: offerta.php");
    }
    exit;
}

// Login automatico tramite token "ricordami"
//$token = getRememberToken();
//if ($token && $auth->loginWithToken($token)) {
//    if ($_SESSION['artigiano']) {
//        header("Location: domanda.php");
//    } else {
//        header("Location: offerta.php");
//    }
//    exit;
//}

// Gestione invio form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                     die("⚠️ Richiesta non valida: possibile attacco CSRF.");
        }

    $nick = trim($_POST['user'] ?? '');
    $pwd = trim($_POST['pwd'] ?? '');
    $remember = isset($_POST['remember']);

    if ($e = Validation::nick($nick)) {
        $error = $e;
    } elseif (strlen($pwd) < 8 || strlen($pwd) > 16) {
        $error = "Password 8-16 caratteri.";
    } elseif (!$auth->login($nick, $pwd, $remember)) {
        $error = "Credenziali errate, riprova.";
    }
}
?>