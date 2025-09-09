<?php
if (empty($_SESSION['last_purchase'])) {
    echo '<main class="card"><p>Nessuna transazione da visualizzare.</p></main>';
    require_once __DIR__ . '/templates/footer.php';
    exit;
}
?>

<?php require_once __DIR__ . '/../app/fine_initializer.php'; ?>

<main class="card">
  <h2>Acquisto eseguito</h2>
  <p>La transazione è andata a buon fine.</p>
  <p>Totale addebitato: <strong><?= $total ?> €</strong></p>
  <p>Credito residuo: <strong><?= $newCredit ?> €</strong></p>
  <p><a href="domanda.php">Torna alla lista</a></p>
</main>