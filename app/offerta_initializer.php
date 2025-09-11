<?php
// app/offerta_initializer.php
require_once __DIR__ . '/session_helpers.php';
startSecureSession();
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../security/csrf.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../storage/azienda_materiali.php';


// Accesso: solo aziende loggate
requireAzienda(['redirect' => 'login.php?error=accesso_negato']);

$aziendaId = (int)$_SESSION['user_id'];
$csrf = generateCsrfToken();

/**
 * 1) Carico la mappa azienda→materiali 
 * 2) Estraggo gli ID materiali dell' azienda.
 * 3) Se esistono, carico i materiali dal DB con una query IN (...).
 */

// carica mappa
$map = loadAziendaMateriali();

$myIds = array_map('intval', $map[(string)$aziendaId] ?? []);

// carica materiali per ID
$materiali = [];
if ($myIds) {
    $in  = implode(',', array_fill(0, count($myIds), '?'));
    $sql = "SELECT id, nome, descrizione, data, quantita, costo FROM materiali WHERE id IN ($in) ORDER BY id DESC";
    $st = $pdo->prepare($sql);
    $st->execute($myIds);
    $materiali = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

?>
