<?php
// app/conferma_initializer.php

// Controllo accessi e ruolo
require_once __DIR__ . '/../helpers/session/AccessControl.php';
AccessControl::requireArtigiano();


// se arriva via POST da domanda: valida CSRF and costruisci cart in sessione
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) die('CSRF non valido.');

    // Estrai qty[...] map
    $qtys = $_POST['qty'] ?? [];
    $cart = [];
    foreach ($qtys as $id => $q) {
        $id = (int)$id;
        $q = (int)$q;
        if ($q > 0) $cart[$id] = $q;
    }
    $_SESSION['cart'] = $cart;
}

// Usa cart da sessione
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: domanda.php'); exit;
}

// Carica materiali selezionati da DB (server trust)
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT id, nome, descrizione, data, quantita, costo FROM materiali WHERE id IN ($placeholders)";
$stmt = Db::prepare($sql);
$stmt->execute($ids);
$rows = $stmt->fetchAll();

// Costruisci items, limita quantita disponibile , calcola subtotal/total
$items = []; $total = 0.0;
foreach ($rows as $r) {
    $id = (int)$r['id'];
    $desired = $cart[$id] ?? 0;
    $available = (int)$r['quantita'];
    if ($available <= 0) continue;
    $qty = min($desired, $available);
    $subtotal = $qty * (float)$r['costo'];
    $items[] = ['id'=>$id,'nome'=>$r['nome'],'qty'=>$qty,'unit'=>(float)$r['costo'],'subtotal'=>$subtotal,'available'=>$available];
    $total += $subtotal;
}

// Carica credito utente
$stmt = Db::prepare("SELECT credit FROM dati_artigiani WHERE id_utente = ? LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$row = $stmt->fetch();
$credit = $row ? (float)$row['credit'] : 0.0;

// nuovo CSRF token per azioni su questa pagina
$csrf2 = generateCsrfToken();
?>
<link rel="stylesheet" href="/public/css/domanda.css">
