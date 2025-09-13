<?php
// api/associazione.php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
require_once __DIR__ . '/../app/helpers/session/AccessControl.php';
require_once __DIR__ . '/../storage/azienda_materiali.php';



// Solo autenticati e solo aziende
AccessControl::requireAzienda(['mode' => 'json']);

header('Content-Type: application/json; charset=utf-8');

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
$map = loadAziendaMateriali();

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
    saveAziendaMateriali($map);
    echo json_encode(['ok'=>true, 'saved'=>count($ids)]); exit;
}
