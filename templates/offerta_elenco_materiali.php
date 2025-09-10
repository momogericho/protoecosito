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
        <form class="item" method="post" action="/../app/modificaMateriale.php" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
          <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">

          <div class="item__title">
            <strong>#<?= (int)$m['id'] ?></strong> — <?= htmlspecialchars($m['nome']) ?>
            <span class="badge">ins. <?= htmlspecialchars($m['data']) ?></span>
          </div>

          <label>Descrizione (max 250)
            <textarea name="descrizione" maxlength="250"><?= htmlspecialchars($m['descrizione']) ?></textarea>
          </label>

          <div class="grid-3">
            <label>Quantità
              <input type="number" name="quantita" min="1" required value="<?= (int)$m['quantita'] ?>">
            </label>
            <label>Costo (€)
              <input type="number" name="costo" step="0.05" min="0" required value="<?= number_format((float)$m['costo'], 2, '.', '') ?>">
            </label>
            <div class="actions">
              <button type="submit">Aggiorna</button>
            </div>
          </div>
        </form>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>