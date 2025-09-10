<?php
// ---  controllo accesso: solo artigiani
if (empty($_SESSION['user_id']) || !isset($_SESSION['artigiano']) || (int)$_SESSION['artigiano'] !== 1) {
    // Utente non autorizzato: mostra solo avviso
    ?>
    <main class="domanda card">
      <h1>Attenzione!</h1>
      <p>Questa pagina è riservata agli artigiani registrati. Inserisci le credenziali prima di procedere all'acquisto.</p>
      <p><a href="login.php">Vai al login</a></p>
    </main>
    <?php
    require_once __DIR__ . '/footer.php';
    exit;
}
?>

<?php require_once __DIR__ . '/../app/domanda_initializer.php'; ?>

<main class="domanda">
  <h1>Acquisto materiali</h1>
  
  <!-- FILTRO DATA -->
  <section class="card filter">
  <?php require_once __DIR__ . '/filtro_data_form.php'; ?>
  </section>
  
  <!-- LISTA MATERIALI -->
  <section class="card materials">
    <h2>Materiali disponibili</h2>

    <?php if (empty($materiali)): ?>
      <p class="muted">Non ci sono materiali disponibili al momento.</p>
    <?php else: ?>
      <form id="selezioneForm" method="post" action="../conferma.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <div class="materials-list">
          <?php foreach ($materiali as $m): 
            $id = (int)$m['id'];
            $available = (int)$m['quantita'];
            $unit = (float)$m['costo'];
            $selQty = isset($cart[$id]) ? (int)$cart[$id] : 0;
            if ($selQty < 0) $selQty = 0;
            if ($selQty > $available) $selQty = $available;
          ?>
          <div class="material-item" data-id="<?= $id ?>" data-available="<?= $available ?>" data-unit="<?= number_format($unit, 2, '.', '') ?>">
            <div class="mat-header">
              <div class="mat-title"><?= htmlspecialchars($m['nome']) ?></div>
              <div class="mat-date"><?= htmlspecialchars($m['data']) ?></div>
            </div>
            <div class="mat-body">
              <div class="mat-desc"><?= nl2br(htmlspecialchars($m['descrizione'])) ?></div>
              <div class="mat-controls">
                <div class="control-qty">
                  <button type="button" class="btn-decr" aria-label="Diminuire">−</button>
                  <input type="number" class="qty-input" name="qty[<?= $id ?>]" min="0" max="<?= $available ?>" value="<?= $selQty ?>" aria-label="Quantità desiderata">
                  <button type="button" class="btn-incr" aria-label="Aumentare">+</button>
                </div>
                <div class="mat-meta">
                  <div>Disponibilità: <strong><?= $available ?></strong></div>
                  <div>Prezzo unitario: <strong><?= number_format($unit,2,',','.') ?> €</strong></div>
                  <div>Subtotale: <strong class="line-subtotal"><?= number_format($selQty * $unit, 2, ',', '.') ?> €</strong></div>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- total & actions -->
        <div class="cart-summary card">
          <div>Totale selezione: <span id="cart-total">0,00</span> €</div>
          <div class="form-actions">
            <button type="button" id="btn-cancella" class="btn btn-secondary">Annulla</button>
            <button type="submit" id="btn-acquista" class="btn btn-primary">Acquista</button>
          </div>
        </div>
      </form>
    <?php endif; ?>
  </section>
</main>

<!-- Bootstrap dati per JS -->
<script>
  window.__CSRF__ = <?= json_encode($csrf) ?>;
</script>
<script src="/public/js/domanda.js" defer></script>