<main role="main" id="mainContent">
<div class="reg-wrapper">
  
  <nav class="quick-links" aria-label="Scorciatoie registrazione">
    <a href="#aziendaForm">Vai al modulo aziende</a>
    <a href="#artigianoForm">Vai al modulo artigiani</a>
    <a href="#mainContent">Torna all'inizio</a>
  </nav>

  <h1>Registrazione</h1>
  <p class="intro">
    Registrati come <strong>Azienda</strong> per offrire materiali,
    oppure come <strong>Artigiano/Designer</strong> per acquistare materiali riciclati.
  </p>

  <div class="reg-grid">
    <!-- Sezione AZIENDA -->
    <section class="card">
      <h2>Azienda</h2>
      <?php if ($okA): ?>
        <div class="success" role="status" aria-live="polite">Registrazione completata! Ora puoi <a href="login.php">accedere</a>.</div>
      <?php endif; ?>
      <?php if ($errorsA): ?>
        <div class="errorbox" role="alert" aria-live="assertive">
          <?php foreach ($errorsA as $e) echo '<div>'.e($e).'</div>'; ?>
        </div>
      <?php endif; ?>

      <form method="post" id="aziendaForm">
        <input type="hidden" name="type" value="azienda">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

        <label>Ragione sociale
          <input type="text" name="ragione" maxlength="30" required
                 value="<?= e($oldA['ragione'] ?? '') ?>"
                 placeholder="Es. EcoTex & Co" autocomplete="organization">
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <label>Indirizzo (Via/Corso Nome numero, Città)
          <input type="text" name="address2" required
                 value="<?= e($oldA['address2'] ?? '') ?>"
                 placeholder="Via Roma 12, Torino" autocomplete="street-address">
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <label>Username (nick)
          <input type="text" name="nick" required
                 value="<?= e($oldA['nick'] ?? '') ?>"
                 placeholder="es. Green_Art" autocomplete="username">
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <label>Password
          <input type="password" name="password" required placeholder="8-16, con . ; + =" autocomplete="new-password">
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <div class="actions">
          <button type="reset" id="btnAziendaReset">Cancella</button>
          <button type="submit" id="btnAziendaSubmit">Registra Azienda</button>
        </div>
      </form>
    </section>

    <!-- Sezione ARTIGIANO -->
    <section class="card">
      <h2>Artigiano / Designer</h2>
      <?php if ($okR): ?>
        <div class="success" role="status" aria-live="polite">Registrazione completata! Ora puoi <a href="login.php">accedere</a>.</div>
      <?php endif; ?>
      <?php if ($errorsR): ?>
        <div class="errorbox" role="alert" aria-live="assertive">
          <?php foreach ($errorsR as $e) echo '<div>'.e($e).'</div>'; ?>
        </div>
      <?php endif; ?>

      <form method="post" id="artigianoForm">
        <input type="hidden" name="type" value="artigiano">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

        <div class="grid-2">
          <label>Nome
            <input type="text" name="name" required
                   value="<?= e($oldR['name'] ?? '') ?>"
                   placeholder="Min 4, max 14"  autocomplete="given-name">
            <span class="error-msg" aria-live="polite"></span>
          </label>
          <label>Cognome
            <input type="text" name="surname" required
                   value="<?= e($oldR['surname'] ?? '') ?>"
                   placeholder="Min 4, max 16" autocomplete="family-name">
            <span class="error-msg" aria-live="polite"></span>
          </label>
        </div>

        <div class="grid-2">
          <label>Data di nascita (aaaa-mm-gg)
            <input type="text" name="birthdate" required
                   value="<?= e($oldR['birthdate'] ?? '') ?>"
                   placeholder="1990-07-15"  autocomplete="bday">
            <span class="error-msg" aria-live="polite"></span>
          </label>
          <label>Credito (€)
            <input type="number" name="credit" required
                   value="<?= e($oldR['credit'] ?? '') ?>"
                   placeholder="es. 12.50 (multipli di 0.05)" step="0.05" min="0" pattern="\d+(\.\d{2})" autocomplete="off">            
            <span class="error-msg" aria-live="polite"></span>
          </label>
        </div>

        <label>Indirizzo (Via/Corso Nome numero, Città)
          <input type="text" name="address" required
                 value="<?= e($oldR['address'] ?? '') ?>"
                 placeholder="Corso Garibaldi 5, Milano"  autocomplete="street-address">
          <span class="error-msg" aria-live="polite"></span>
        </label>

        <div class="grid-2">
          <label>Username (nick)
            <input type="text" name="nick" required
                   value="<?= e($oldR['nick'] ?? '') ?>" autocomplete="username">
            <span class="error-msg" aria-live="polite"></span>
          </label>
          <label>Password
            <input type="password" name="password" required placeholder="8-16, con . ; + =">
            <span class="error-msg" aria-live="polite"></span>
          </label>
        </div>

        <div class="actions">
          <button type="reset" id="btnArtigianoReset">Cancella</button>
          <button type="submit" id="btnArtigianoSubmit">Registra Artigiano</button>
        </div>
      </form>
    </section>
  </div>
</div>
</main>