<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/registration_controller.php';
require_once __DIR__ . '/../security/csrf.php';

// Possibile iniezione risorse 
$pageTitle = "Registrazione";
//$pageExtraCss = ['/public/css/registrazione.css'];  
//$pageExtraJs  = ['/public/js/registrazione.js'];   

//nel caso fosse supportata e si volesse optare per questa decisione procedere a creare i file css e js 
//con le parti labeled per la registrazione presenti nei rispettivi documenti style.css e model.jss

// CSRF
$csrf_token = generateCsrfToken();

$controller = new RegistrationController($pdo);
$okA = $okR = false;
$errorsA = $errorsR = [];
$oldA = $oldR = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['type'] ?? '') === 'azienda') {
        $oldA = $_POST;
        $res = $controller->handleAzienda($_POST);
        if ($res['ok']) $okA = true; else $errorsA = $res['errors'];
    } elseif (($_POST['type'] ?? '') === 'artigiano') {
        $oldR = $_POST;
        $res = $controller->handleArtigiano($_POST);
        if ($res['ok']) $okR = true; else $errorsR = $res['errors'];
    }
}