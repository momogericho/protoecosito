<?php
require_once __DIR__ . "/../security/csrf.php";
require_once __DIR__ . "/../app/remember.php";
$csrf_token = generateCsrfToken();
$rememberedUser = e(getRememberToken());

?>

<main id="mainContent"  role="main" class="login-container">
  <h2>Login</h2>

  <?php if ($error): ?>
    <p class="error"><?= e($error) ?></p>
  <?php endif; ?>

  <form method="post" action="../login.php" id="loginForm">
    <label for="user">Username:</label>
    <input type="text" id="user" name="user" value="<?= $rememberedUser ?>" placeholder="Username" autocomplete="username" required>

    <label for="pwd">Password:</label>
    <input type="password" id="pwd" name="pwd" required>
    
    <!-- Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">


    <label>
      <input type="checkbox" name="remember"> Ricordami per 72 ore
    </label>

    <div class="buttons">
      <button type="reset">Cancella</button>
      <button type="submit">Invia</button>
    </div>
  </form>
</main>