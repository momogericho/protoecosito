<!-- SEZIONE: inserimento nuovo materiale -->
  <section class="card">
    <div class="card__head">
      <h2>Inserisci nuovo materiale</h2>
    </div>

    <form id="nuovoMaterialeForm" class="grid" method="post" action="/../app/inserisciMateriale.php" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

      <label>Nome (10–40, lettere/numeri/spazi)
        <input type="text" name="nome" id="nome" minlength="10" maxlength="40" required>
      </label>

      <label>Descrizione (max 250)
        <textarea name="descrizione" id="descrizione" maxlength="250"></textarea>
      </label>

      <div class="grid-3">
        <label>Data (aaaa-mm-gg)
          <input type="date" name="data" id="data" required>
        </label>

        <label>Quantità (intero)
          <input type="number" name="quantita" id="quantita" min="1" required>
        </label>

        <label>Costo (€)
          <input type="number" name="costo" id="costo" step="0.05" min="0" required>
        </label>
      </div>

      <div class="actions">
        <button type="reset">Cancella</button>
        <button type="submit">Aggiungi</button>
      </div>
    </form>
  </section>
</main>