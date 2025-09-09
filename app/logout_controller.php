<?php
session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../security/csrf.php";


if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                     die("⚠️ Richiesta non valida: Token CSRF non valido.");
        }

$auth = new AuthController($pdo);
$auth->logout();


// Chiudo sessione
session_start();
session_unset();
session_destroy();

// Elimino eventuali cookie "remember me"
setcookie("remember_user", "", time() - 3600, "/");

// Redirect a home
header("Location: home.php");
exit;
