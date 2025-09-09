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

$nome = trim($_POST['nome'] ?? '');
$descrizione = trim($_POST['descrizione'] ?? '');
$data = $_POST['data'] ?? '';
$quantita = (int)($_POST['quantita'] ?? 0);
$costo = (float)($_POST['costo'] ?? 0);

// Validazioni
if (!preg_match('/^[A-Za-z0-9 ]{10,40}$/', $nome)) exit('Nome non valido');
if (mb_strlen($descrizione) > 250) exit('Descrizione troppo lunga');
if (!preg_match('/^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/', $data)) exit('Data non valida');
[$y,$m,$d]=array_map('intval', explode('-', $data)); if (!checkdate($m, $d, $y)) exit('Data inesistente');
if ($quantita <= 0) exit('Quantità non valida');
$cents = (int)round($costo * 100);
if ($cents < 0 || $cents % 5 !== 0) exit('Costo non valido (centesimi multipli di 5)');

// Inserisci materiale
$st = $pdo->prepare('INSERT INTO materiali (nome, descrizione, data, quantita, costo) VALUES (:n,:d,:dt,:q,:c)');
$st->execute([':n'=>$nome, ':d'=>$descrizione, ':dt'=>$data, ':q'=>$quantita, ':c'=>$costo]);
$matId = (int)$pdo->lastInsertId();

// Aggiorna la mappa su file per questa azienda
$mapFile = realpath(__DIR__ . '/../storage') . DIRECTORY_SEPARATOR . 'azienda_materiali.json';
$map = [];
if (file_exists($mapFile)) $map = json_decode(file_get_contents($mapFile), true);
if (!is_array($map)) $map = [];
$key = (string)$_SESSION['user_id'];
$map[$key] = array_values(array_unique(array_map('intval', array_merge($map[$key] ?? [], [$matId]))));
$tmp = $mapFile . '.tmp';
file_put_contents($tmp, json_encode($map, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
rename($tmp, $mapFile);

// Redirect
header('Location: /offerta.php?success=1');
exit;
