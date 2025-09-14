<main id="mainContent" role="main" class="domanda">
  <h1>Acquisto materiali</h1>
  
  <!-- FILTRO DATA -->
  <section class="card filter print-section">
<?php render('partials/filtro_data_form.php', ['filterDate' => $filterDate ?? ($filterDate ?? ''), 'resetUrl' => 'domanda.php']); ?>  </section>
  
  <!-- LISTA MATERIALI -->
  <section class="card materials print-section">
    <h2>Materiali disponibili</h2>

    <?php if (empty($materiali)): ?>
      <p class="muted">Non ci sono materiali disponibili al momento.</p>
    <?php else: ?>
      <form id="selezioneForm" method="post" action="../conferma.php" >
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

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
              <div class="mat-title"><?= e($m['nome']) ?></div>
              <div class="mat-date"><?= e($m['data']) ?></div>
            </div>
            <div class="mat-body">
              <div class="mat-desc"><?= nl2br(e($m['descrizione'])) ?></div>
              <div class="mat-controls">
                <div class="control-qty">
                  <button type="button" class="btn-decr" aria-label="Diminuire">−</button>
                  <input type="number" class="qty-input" name="qty[<?= $id ?>]" min="0" max="<?= $available ?>" value="<?= $selQty ?>" aria-label="Quantità desiderata">
                  <button type="button" class="btn-incr" aria-label="Aumentare">+</button>
                  <span class="error-msg" aria-live="polite"></span>
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
        <div class="cart-summary card print-section">
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
  <script src="<?= BASE_URL ?>/js/domanda.js" defer></script>
