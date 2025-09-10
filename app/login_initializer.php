<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/remember.php";
require_once __DIR__ . "/../security/csrf.php";

$auth = new AuthController($pdo);
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
$token = getRememberToken();
if ($token && $auth->loginWithToken($token)) {
    if ($_SESSION['artigiano']) {
        header("Location: domanda.php");
    } else {
        header("Location: offerta.php");
    }
    exit;
}

// Valori di default per il form
$cookie_user = "";
$cookie_pwd  = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                     die("⚠️ Richiesta non valida: possibile attacco CSRF.");
        }

    $nick = $_POST['user'] ?? '';
    $pwd = $_POST['pwd'] ?? '';
    $remember = isset($_POST['remember']);

    if (!$auth->login($nick, $pwd, $remember)) {
        $error = "Credenziali errate, riprova.";
    }
}
?>