<main id="mainContent"  role="main" class="lista container">
  <header class="page-head">
    <h1>Materiali disponibili</h1>
    <?php require BASE_VIEW_PATH.'/partials/filtro_data_form.php'; ?>
  </header>

  <?php if ($filterDate !== ''): ?>
    <p class="filter-note">Filtro attivo: materiali inseriti dal <strong><?= e($filterDate) ?></strong> in poi.</p>
  <?php endif; ?>

  <?php if (empty($materiali)): ?>
    <section class="card print-section">
      <p class="muted">Nessun materiale da mostrare.</p>
    </section>
  <?php else: ?>
    <!-- Tabella responsive: si trasforma in cards su mobile -->
    <section class="card table-wrapper print-section">
      <table class="materials-table print-table" aria-describedby="materials-caption">
        <caption id="materials-caption" class="visually-hidden">Elenco materiali disponibili</caption>
        <thead>
          <tr>
            <th scope="col">Nome</th>
            <th scope="col">Descrizione</th>
            <?php if ($isArtigiano): ?>
              <th scope="col">Quantità</th>
              <th scope="col">Costo unitario</th>
            <?php endif; ?>
            <th scope="col">Data</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($materiali as $m): ?>
          <tr>
            <td data-label="Nome"><?= e($m['nome']) ?></td>
            <td data-label="Descrizione"><?= nl2br(e($m['descrizione'])) ?></td>
            <?php if ($isArtigiano): ?>
              <td data-label="Quantità"><?= (int)$m['quantita'] ?></td>
              <td data-label="Costo unitario"><?= number_format((float)$m['costo'], 2, ',', '.') ?> €</td>
            <?php endif; ?>
            <td data-label="Data"><?= e($m['data']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  <?php endif; ?>
</main>