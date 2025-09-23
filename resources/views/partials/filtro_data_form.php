
  <?php
    $formId = $formId ?? 'filterForm';
    $submitId = $formId . '-submit';
  ?>
  <form method="get" class="filter-form" id="<?= e($formId) ?>" aria-label="Filtro per data di inserimento">
      <label for="after_date">Mostra inseriti dal:</label>
      <input type="date" id="after_date" name="after_date" value="<?= e($filterDate) ?>">
      <button type="submit" class="btn" id="<?= e($submitId) ?>">Applica</button>
      <a class="btn btn-link" href="<?= e($resetUrl) ?>?after_date=" id="btn-reset-filtro">Rimuovi filtro</a>
    </form>