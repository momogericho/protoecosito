<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../security/csrf.php';
require_once __DIR__ . '/../config/db.php';

// Accesso: solo aziende loggate
if (empty($_SESSION['user_id']) || !isset($_SESSION['is_artigiano']) || (int)$_SESSION['is_artigiano'] === 1) {
    header('Location: login.php?error=accesso_negato'); exit;
}

$aziendaId = (int)$_SESSION['user_id'];
$csrf = generateCsrfToken();

/**
 * 1) Carico la mappa azienda→materiali da file.
 * 2) Estraggo gli ID materiali della MIA azienda.
 * 3) Se esistono, carico i materiali dal DB con una query IN (...).
 */

// carica mappa
$mapFile = __DIR__ . '/storage/azienda_materiali.json';
if (!file_exists($mapFile)) {
    file_put_contents($mapFile, json_encode(new stdClass(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}
$map = json_decode(file_get_contents($mapFile), true);
if (!is_array($map)) $map = [];

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
