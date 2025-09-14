<?php
// api/reset_selection.php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /domanda.php'); exit;
}
$token = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($token)) {
    http_response_code(400); die('CSRF non valido');
}
unset($_SESSION['cart']);
header('Location: /domanda.php');
exit;