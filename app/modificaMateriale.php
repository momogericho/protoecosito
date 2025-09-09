<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../security/csrf.php';
require_once __DIR__ . '/../config/db.php';

// Solo aziende
if (empty($_SESSION['user_id']) || !isset($_SESSION['is_artigiano']) || (int)$_SESSION['is_artigiano'] === 1) {
    http_response_code(403); exit('Accesso negato');
}

// CSRF
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    http_response_code(400); exit('CSRF non valido');
}

$id = (int)($_POST['id'] ?? 0);
$descrizione = trim($_POST['descrizione'] ?? '');
$quantita = (int)($_POST['quantita'] ?? 0);
$costo = (float)($_POST['costo'] ?? 0);

// Verifica: il materiale è nella mappa dell’azienda?
$mapFile = realpath(__DIR__ . '/../storage') . DIRECTORY_SEPARATOR . 'azienda_materiali.json';
$map = [];
if (file_exists($mapFile)) $map = json_decode(file_get_contents($mapFile), true);
$myIds = array_map('intval', $map[(string)$_SESSION['user_id']] ?? []);
if (!in_array($id, $myIds, true)) {
    http_response_code(403); exit('Non puoi modificare questo materiale');
}

// Validazioni
if (mb_strlen($descrizione) > 250) exit('Descrizione troppo lunga');
if ($quantita <= 0) exit('Quantità non valida');
$cents = (int)round($costo*100);
if ($cents < 0 || $cents % 5 !== 0) exit('Costo non valido (centesimi multipli di 5)');

// Aggiorna
$st = $pdo->prepare('UPDATE materiali SET descrizione=:d, quantita=:q, costo=:c WHERE id=:id');
$st->execute([':d'=>$descrizione, ':q'=>$quantita, ':c'=>$costo, ':id'=>$id]);

header('Location: /offerta.php?updated=1');
exit;
