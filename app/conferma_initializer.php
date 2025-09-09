<?php
// app/conferma_initializer.php

// --- access control
if (empty($_SESSION['user_id']) || !isset($_SESSION['is_artigiano']) || (int)$_SESSION['is_artigiano'] !== 1) {
    echo '<main class="card"><p>Attenzione! Questa pagina è riservata agli artigiani registrati.</p></main>';
    require_once __DIR__ . '/templates/footer.php';
    exit;
}

// If arrived via POST from domanda: validate CSRF and build cart in session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) die('CSRF non valido.');

    // Extract qty[...] map
    $qtys = $_POST['qty'] ?? [];
    $cart = [];
    foreach ($qtys as $id => $q) {
        $id = (int)$id;
        $q = (int)$q;
        if ($q > 0) $cart[$id] = $q;
    }
    $_SESSION['cart'] = $cart;
}

// Use cart from session
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: domanda.php'); exit;
}

// Load selected materials from DB (server trust)
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT id, nome, descrizione, data, quantita, costo FROM materiali WHERE id IN ($placeholders)";
$stmt = $pdo->prepare($sql);
$stmt->execute($ids);
$rows = $stmt->fetchAll();

// Build items, limit quantity to available, compute subtotal/total
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

// Fetch artisan credit
$stmt = $pdo->prepare("SELECT credit FROM dati_artigiani WHERE id_utente = ? LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$row = $stmt->fetch();
$credit = $row ? (float)$row['credit'] : 0.0;

// new CSRF token for actions on this page
$csrf2 = generateCsrfToken();
?>
<link rel="stylesheet" href="/public/css/domanda.css">
