<?php
// api/associazione.php
require_once __DIR__ . '/../security/csrf.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Solo autenticati e solo aziende
if (empty($_SESSION['user_id']) || !isset($_SESSION['artigiano']) || (int)$_SESSION['artigiano'] === 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Accesso negato']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$storagePath = realpath(__DIR__ . '/../storage');         // sicuro
$file = $storagePath . DIRECTORY_SEPARATOR . 'azienda_materiali.json';

if (!file_exists($file)) {
    file_put_contents($file, json_encode(new stdClass(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$csrf   = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';

if (!in_array($action, ['get','set'], true)) {
    echo json_encode(['error' => 'Azione non valida']); exit;
}

if (!validateCsrfToken($csrf)) {
    http_response_code(400);
    echo json_encode(['error' => 'Token CSRF non valido']);
    exit;
}

// Carica mappa
$map = json_decode(file_get_contents($file), true);
if (!is_array($map)) $map = [];

$aziendaId = (string)$_SESSION['user_id'];

if ($action === 'get') {
    $ids = $map[$aziendaId] ?? [];
    echo json_encode(['ok'=>true, 'aziendaId'=>$aziendaId, 'materialiIds'=>$ids]); exit;
}

if ($action === 'set') {
    $ids = $_POST['materialiIds'] ?? [];
    if (!is_array($ids)) $ids = [];
    // normalizza numeri interi e unici
    $ids = array_values(array_unique(array_map(fn($v)=> (int)$v, $ids)));
    $map[$aziendaId] = $ids;
    // salva in modo atomico
    $tmp = $file . '.tmp';
    file_put_contents($tmp, json_encode($map, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    rename($tmp, $file);
    echo json_encode(['ok'=>true, 'saved'=>count($ids)]); exit;
}
