<?php
// Attiva la sessione se non già avviata
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../security/csrf.php';
require_once __DIR__ . '/../helpers/validation.php';


// CSRF token per i form e le azioni AJAX
$csrf = generateCsrfToken();

// filtro data (GET)
$filter_date = trim($_GET['after_date'] ?? '');
$params = [];
$sql = "SELECT id, nome, descrizione, data, quantita, costo FROM materiali";
if ($filter_date !== '') {
   if (Validation::date($filter_date) === null) {
        $sql .= " WHERE data >= :fdate";
        $params[':fdate'] = $filter_date;
    } else {
        $filter_date = ''; 
    }
}
$sql .= " ORDER BY data DESC, id DESC";
$stmt = Db::prepare($sql);
$stmt->execute($params);
$materiali = $stmt->fetchAll();

// carica selezione corrente dalla sessione (cart: [id => qty])
$cart = $_SESSION['cart'] ?? [];

?>
<link rel="stylesheet" href="/public/css/domanda.css">