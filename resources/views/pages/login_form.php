<?php
require_once BASE_PATH . '/app/helpers/remember.php';
$credentials = getRememberedCredentials();
$rememberedUser = $credentials['user'] ?? '';
$rememberedPwd = $credentials['pwd'] ?? '';
?>

<main id="mainContent"  role="main" class="login-container">
  <h2>Login</h2>

  <?php if ($error): ?>
    <p class="error"><?= e($error) ?></p>
  <?php endif; ?>

  <form method="post" action="../login.php" id="loginForm">
    <label for="user">Username:</label>
    <input type="text" id="user" name="user" value="<?= e($rememberedUser) ?>" placeholder="Username" autocomplete="username" required>

    <label for="pwd">Password:</label>
    <input type="password" id="pwd" name="pwd" value="<?= e($rememberedPwd) ?>" autocomplete="current-passwor" required>
    
    <!-- Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">


    <label>
      <input type="checkbox" name="remember"> Ricordami per 72 ore
    </label>

    <div class="buttons">
      <button type="reset">Cancella</button>
      <button type="submit">Invia</button>
    </div>
  </form>
</main>