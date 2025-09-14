<?php
// Attiva la sessione se non già avviata
require_once __DIR__ . '/../helpers/validation.php';
require_once __DIR__ . '/../helpers/session/AccessControl.php';

AccessControl::requireArtigiano();


// filtro data (GET)
$filterDate = trim($_GET['after_date'] ?? '');
$params = [];
$sql = "SELECT id, nome, descrizione, data, quantita, costo FROM materiali";
if ($filterDate !== '') {
   if (Validation::date($filterDate) === null) {
        $sql .= " WHERE data >= :fdate";
        $params[':fdate'] = $filterDate;
    } else {
        $filterDate = ''; 
    }
}
$sql .= " ORDER BY data DESC, id DESC";
$stmt = Db::prepareRead($sql);
$stmt->execute($params);
$materiali = $stmt->fetchAll();

// carica selezione corrente dalla sessione (cart: [id => qty])
$cart = $_SESSION['cart'] ?? [];

?>