<main id="mainContent"  role="main" class="login-container">
  <nav class="quick-links" aria-label="Collegamenti rapidi">
    <a href="#loginForm">Vai direttamente al modulo di accesso</a>
    <a href="registration.php#aziendaForm">Registrazione aziende</a>
    <a href="registration.php#artigianoForm">Registrazione artigiani</a>
  </nav>
  <h2>Login</h2>

  <p class="muted" id="loginIntro">
    Accedi per continuare a gestire scorte e richieste. Se hai perso l'accesso,
    <a href="registration.php">registrati nuovamente</a> o contatta il supporto.
  </p>

  <div id="loginErrors" class="error" role="alert" aria-live="assertive">
    <?php if ($error): ?>
      <span><?= e($error) ?></span>
    <?php endif; ?>
  </div>

  <form method="post" action="../login.php" id="loginForm" aria-describedby="loginIntro">
    <label for="user">Username:</label>
    <input type="text" id="user" name="user" placeholder="Username" autocomplete="username" required>

    <label for="pwd">Password:</label>
    <input type="password" id="pwd" name="pwd" autocomplete="current-password" required aria-describedby="rememberHint">
    
    <!-- Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">


    <label>
      <input type="checkbox" name="remember" aria-describedby="rememberHint"> Ricordami per 72 ore
    </label>
    <p class="hint" id="rememberHint">
      L'opzione ricordami crea un token monouso cifrato: puoi revocarlo in ogni momento effettuando il logout.
    </p>

    <div class="buttons">
      <button type="reset" id="btnLoginReset">Cancella</button>
      <button type="submit" id="btnLoginSubmit">Invia</button>
    </div>
  </form>
</main>