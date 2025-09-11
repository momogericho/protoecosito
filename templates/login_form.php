<?php
require_once __DIR__ . "/../security/csrf.php";
$csrf_token = generateCsrfToken();
?>

<main class="login-container">
  <h2>Login</h2>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" action="../login.php" id="loginForm">
    <label for="user">Username:</label>
    <input type="text" id="user" name="user" required>

    <label for="pwd">Password:</label>
    <input type="password" id="pwd" name="pwd" required>
    
    <!-- Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">


    <label>
      <input type="checkbox" name="remember"> Ricordami per 72 ore
    </label>

    <div class="buttons">
      <button type="reset">Cancella</button>
      <button type="submit">Invia</button>
    </div>
  </form>
</main>