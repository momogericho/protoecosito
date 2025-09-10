<?php

// 1) Determina ruolo: artigiano autenticato?
$isArtigiano = (!empty($_SESSION['user_id']) && isset($_SESSION['artigiano']) && (int)$_SESSION['artigiano'] === 1);

// 2) Gestione filtro data: accetta GET after_date, valida e salva in sessione per persistenza cross-pagina
if (isset($_GET['after_date'])) {
    $candidate = trim($_GET['after_date']);
    if ($candidate === '') {
        unset($_SESSION['materials_after_date']); // rimuovi filtro
    } else if (preg_match('/^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/', $candidate)) {
        $_SESSION['materials_after_date'] = $candidate;
    }
}
// valore effettivo del filtro (se presente in sessione)
$filterDate = $_SESSION['materials_after_date'] ?? '';

// 3) Query materiali con filtro opzionale (parametrizzato)
$params = [];
$sql = "SELECT id, nome, descrizione, data, quantita, costo FROM materiali";
if ($filterDate !== '') {
    $sql .= " WHERE data >= :fdate";
    $params[':fdate'] = $filterDate;
}
$sql .= " ORDER BY data DESC, id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materiali = $stmt->fetchAll();

?>