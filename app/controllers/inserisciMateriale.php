<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../security/csrf.php';
require_once __DIR__ . '/../../storage/azienda_materiali.php';
require_once __DIR__ . '/../helpers/validation.php';
require_once __DIR__ . '/../helpers/session/AccessControl.php';



// Solo aziende
AccessControl::requireAzienda();

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
if ($e = Validation::date($data)) exit($e);
[$y,$m,$d]=array_map('intval', explode('-', $data)); if (!checkdate($m, $d, $y)) exit('Data inesistente');
if ($quantita <= 0) exit('Quantità non valida');
$cents = (int)round($costo * 100);
if ($cents < 0 || $cents % 5 !== 0) exit('Costo non valido (centesimi multipli di 5)');

// Inserisci materiale
$st = Db::prepare('INSERT INTO materiali (nome, descrizione, data, quantita, costo) VALUES (:n,:d,:dt,:q,:c)');
$st->execute([':n'=>$nome, ':d'=>$descrizione, ':dt'=>$data, ':q'=>$quantita, ':c'=>$costo]);
$matId = (int)Db::lastInsertId();

// Aggiorna la mappa su file per questa azienda
$map = loadAziendaMateriali();
$key = (string)$_SESSION['user_id'];
$map[$key] = array_values(array_unique(array_map('intval', array_merge($map[$key] ?? [], [$matId]))));
saveAziendaMateriali($map);

// Redirect
header('Location: /offerta.php?success=1');
exit;
