<?php
// ---  CSRF token per i form e le azioni AJAX
$csrf = generateCsrfToken();

// ---  filtro data (GET)
$filter_date = trim($_GET['after_date'] ?? '');
$params = [];
$sql = "SELECT id, nome, descrizione, data, quantita, costo FROM materiali";
if ($filter_date !== '') {
    // validazione lato server della data (YYYY-MM-DD)
    if (preg_match('/^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/', $filter_date)) {
        $sql .= " WHERE data >= :fdate";
        $params[':fdate'] = $filter_date;
    } else {
        $filter_date = ''; // ignora filtro non valido
    }
}
$sql .= " ORDER BY data DESC, id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materiali = $stmt->fetchAll();

// --- 4) carica selezione corrente dalla sessione (cart: [id => qty])
$cart = $_SESSION['cart'] ?? [];

?>
<link rel="stylesheet" href="/public/css/domanda.css">