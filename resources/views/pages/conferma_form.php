<main id="mainContent" role="main" class="card print-section">
  <h2>Riepilogo acquisto</h2>

  <?php if (empty($items)): ?>
    <p>Nessun materiale disponibile tra quelli selezionati. <a href="../domanda.php">Torna a lista</a></p>
  <?php else: ?>
    <table class="confirm-table print-table">
      <thead><tr><th>Nome</th><th>Qtà</th><th>Prezzo unit.</th><th>Subtotale</th></tr></thead>
      <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?= e($it['nome']) ?></td>
            <td><?= (int)$it['qty'] ?> (disp <?= (int)$it['available'] ?>)</td>
            <td><?= number_format($it['unit'],2,',','.') ?> €</td>
            <td><?= number_format($it['subtotal'],2,',','.') ?> €</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><th colspan="3">Totale</th><th><?= number_format($total,2,',','.') ?> €</th></tr>
        <tr><th colspan="3">Il tuo credito</th><th><?= number_format($credit,2,',','.') ?> €</th></tr>
      </tfoot>
    </table>

    <?php if ($total > $credit): ?>
      <p class="error">Credito insufficiente per completare l'acquisto.</p>
      <form method="get" action="../domanda.php" style="display:inline">
        <button type="submit" class="btn">Indietro</button>
      </form>
      <form method="post" action="<?= BASE_URL ?>/api/reset_selection.php" style="display:inline">
        <input type="hidden" name="csrf_token" value="<?= e($csrf2) ?>">
        <button type="submit" class="btn btn-secondary">Reset</button>
      </form>
    <?php else: ?>
      <!-- Totale <= credito -->
      <form method="get" action="../domanda.php" style="display:inline">
        <button type="submit" class="btn">Indietro</button>
      </form>
      <form method="post" action="<?= BASE_URL ?>/api/process_purchase.php" style="display:inline">
        <input type="hidden" name="csrf_token" value="<?= e($csrf2) ?>">
        <button type="submit" class="btn btn-primary">Concludi</button>
      </form>
      <form method="post" action="<?= BASE_URL ?>/api/reset_selection.php" style="display:inline">
        <input type="hidden" name="csrf_token" value="<?= e($csrf2) ?>">
        <button type="submit" class="btn btn-secondary">Reset</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</main>