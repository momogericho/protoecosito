<?php
require_once __DIR__ . '/../init.php';
AppInitializer::init();
require_once __DIR__ . '/auth_controller.php';


if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                     die("⚠️ Richiesta non valida: Token CSRF non valido.");
        }

$auth = new AuthController();
$auth->logout();

// Redirect a home
header("Location: home.php");
exit;
