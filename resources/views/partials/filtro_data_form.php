
  <form method="get" class="filter-form" aria-label="Filtro per data di inserimento">
      <label for="after_date">Mostra inseriti dal:</label>
      <input type="date" id="after_date" name="after_date" value="<?= e($filterDate) ?>">
      <button type="submit" class="btn">Applica</button>
      <a class="btn btn-link" href="lista.php?after_date=" id="btn-reset-filtro">Rimuovi filtro</a>
    </form>