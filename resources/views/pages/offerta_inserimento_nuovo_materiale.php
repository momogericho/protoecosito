<!-- SEZIONE: inserimento nuovo materiale -->
  <section class="card print-section">
    <div class="card__head">
      <h2>Inserisci nuovo materiale</h2>
    </div>

    <p class="microcopy" role="note" id="nuovoMaterialeHint">
      Compila tutti i campi: il materiale sarà subito visibile agli artigiani una volta aggiunto.
      Specifica dettagli utili (dimensioni, stato d'uso) per ridurre richieste di chiarimento.
    </p>

    <form id="nuovoMaterialeForm" class="grid" method="post" action="<?= BASE_URL ?>/api/inserisciMateriale.php" >
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

      <label>Nome (10–40, lettere/numeri/spazi)
        <input type="text" name="nome" id="nome" minlength="10" maxlength="40" required aria-describedby="nuovoMaterialeHint">
        <span class="error-msg" aria-live="polite"></span>
      </label>

      <label>Descrizione (max 250)
        <textarea name="descrizione" id="descrizione" maxlength="250" aria-describedby="nuovoMaterialeHint"></textarea>
        <span class="error-msg" aria-live="polite"></span>
      </label>

      <div class="grid-3">
        <label>Data (aaaa-mm-gg)
          <input type="date" name="data" id="data" required aria-describedby="nuovoMaterialeHint">
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <label>Quantità (intero)
          <input type="number" name="quantita" id="quantita" min="1" required aria-describedby="nuovoMaterialeHint">
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <label>Costo (€)
          <input type="number" name="costo" id="costo" step="0.05" min="0" required aria-describedby="nuovoMaterialeHint">
          <span class="error-msg" aria-live="polite"></span>
        </label>
      </div>

      <div class="actions">
        <button type="reset" id="btnNuovoReset">Cancella</button>
        <button type="submit" id="btnNuovoSubmit">Aggiungi</button>
      </div>
    </form>
  </section>
