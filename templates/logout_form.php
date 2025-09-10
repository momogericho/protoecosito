<?php

require_once __DIR__ . "/../security/csrf.php";


// Genero token CSRF per il logout
$csrf_token = generateCsrfToken();
?>

<main class="logout-container">
    <h2>Sei sicuro di voler uscire?</h2>
    <form method="POST" action="logout_controller.php">
        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <button type="submit">Logout</button>
    </form>
</main>
