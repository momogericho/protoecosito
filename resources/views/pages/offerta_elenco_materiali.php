<h1>Gestione materiali (Azienda)</h1>
<!-- SEZIONE: elenco materiali esistenti -->
<section class="card">
  <div class="card__head">
    <h2>I tuoi materiali</h2>
    <small class="muted">Modifica descrizione, quantità, costo</small>
  </div>

  <?php if (!$materiali): ?>
    <p class="muted">Nessun materiale associato al tuo account.</p>
  <?php else: ?>
    <div class="list">
      <?php foreach ($materiali as $m): ?>
        <form class="item" method="post" action="<?= BASE_URL ?>/api/modificaMateriale.php" >
          <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
          <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">

          <div class="item__title">
            <strong>#<?= (int)$m['id'] ?></strong> — <?= e($m['nome']) ?>
            <span class="badge">ins. <?= e($m['data']) ?></span>
          </div>

          <label>Descrizione (max 250)
            <textarea name="descrizione" maxlength="250"><?= e($m['descrizione']) ?></textarea>
            <span class="error-msg" aria-live="polite"></span>
          </label>

          <div class="grid-3">
            <label>Quantità
              <input type="number" name="quantita" min="1" required value="<?= (int)$m['quantita'] ?>">
              <span class="error-msg" aria-live="polite"></span>
            </label>
            <label>Costo (€)
              <input type="number" name="costo" step="0.05" min="0" required value="<?= number_format((float)$m['costo'], 2, '.', '') ?>">
              <span class="error-msg" aria-live="polite"></span>
            </label>
            <div class="actions">
              <button type="submit" name="update">Aggiorna</button>
            </div>
          </div>
        </form>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>