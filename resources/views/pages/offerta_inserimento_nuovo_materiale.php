<!-- SEZIONE: inserimento nuovo materiale -->
  <section class="card print-section">
    <div class="card__head">
      <h2>Inserisci nuovo materiale</h2>
    </div>

    <form id="nuovoMaterialeForm" class="grid" method="post" action="/app/controllers/inserisciMateriale.php" >
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

      <label>Nome (10–40, lettere/numeri/spazi)
        <input type="text" name="nome" id="nome" minlength="10" maxlength="40" required>
        <span class="error-msg" aria-live="polite"></span>
      </label>

      <label>Descrizione (max 250)
        <textarea name="descrizione" id="descrizione" maxlength="250"></textarea>
        <span class="error-msg" aria-live="polite"></span>
      </label>

      <div class="grid-3">
        <label>Data (aaaa-mm-gg)
          <input type="date" name="data" id="data" required>
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <label>Quantità (intero)
          <input type="number" name="quantita" id="quantita" min="1" required>
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <label>Costo (€)
          <input type="number" name="costo" id="costo" step="0.05" min="0" required>
          <span class="error-msg" aria-live="polite"></span>
        </label>
      </div>

      <div class="actions">
        <button type="reset">Cancella</button>
        <button type="submit">Aggiungi</button>
      </div>
    </form>
  </section>
