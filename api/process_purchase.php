<?php
// api/process_purchase.php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
require_once __DIR__ . '/../app/helpers/session/AccessControl.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /domanda.php'); exit;
}
$token = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($token)) die('CSRF non valido');

AccessControl::requireArtigiano();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) { header('Location: /domanda.php'); exit; }

$userId = (int)$_SESSION['user_id'];

try {
    Db::beginTransaction();

    // 1) blocca credit artigiano
    $st = Db::prepare('SELECT credit FROM dati_artigiani WHERE id_utente = ? FOR UPDATE');
    $st->execute([$userId]);
    $row = $st->fetch();
    if (!$row) throw new Exception('Dati artigiano non trovati');
    $credit = (float)$row['credit'];

    // 2) carica e blocca materiali
    $ids = array_map('intval', array_keys($cart));
    if (empty($ids)) throw new Exception('Carrello vuoto');
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = Db::prepare("SELECT id, quantita, costo FROM materiali WHERE id IN ($in) FOR UPDATE");
    $st->execute($ids);
    $materials = [];
    while ($r = $st->fetch()) {
        $materials[(int)$r['id']] = $r;
    }

    // 3) calcola totale e disponibilità
    $total = 0.0;
    foreach ($cart as $mid => $q) {
        $mid = (int)$mid; $q = (int)$q;
        if ($q <= 0) throw new Exception("Qtà non valida per $mid");
        if (!isset($materials[$mid])) throw new Exception("Materiale $mid non trovato");
        $available = (int)$materials[$mid]['quantita'];
        if ($q > $available) throw new Exception("Quantità richiesta maggiore della disponibilità per materiale $mid");
        $total += $q * (float)$materials[$mid]['costo'];
    }

    // 4) controlla credito
    if ($total > $credit) {
        Db::rollBack();
        $_SESSION['purchase_error'] = 'Credito insufficiente';
        header('Location: /conferma.php');
        exit;
    }

    // 5) update quantita materiali
    $stmtUpd = Db::prepare('UPDATE materiali SET quantita = quantita - ? WHERE id = ?');
    foreach ($cart as $mid => $q) {
        $stmtUpd->execute([(int)$q, (int)$mid]);
    }

    // 6) update credito artigiano
    $newCredit = $credit - $total;
    $stmtCred = Db::prepare('UPDATE dati_artigiani SET credit = ? WHERE id_utente = ?');
    $stmtCred->execute([$newCredit, $userId]);

    // 7) commit
    Db::commit();

     // update session credit per display immediato
    $_SESSION['credit'] = number_format($newCredit, 2, ',', '.');

    // pulisci cart, set last_purchase info per fine.php
    unset($_SESSION['cart']);
    $_SESSION['last_purchase'] = [
        'total' => $total,
        'new_credit' => $newCredit,
        'items' => $cart
    ];

    header('Location: /fine.php?ok=1');
    exit;

} catch (Exception $ex) {
    if (Db::inTransaction()) Db::rollBack();
    // In produzione: loggare, non mostrare messaggi tecnici
    die('Errore durante l\'acquisto: ' . $ex->getMessage());
}
