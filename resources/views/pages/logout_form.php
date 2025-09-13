<?php ?>
<main id="mainContent"  role="main" class="logout-container">
    <h2>Sei sicuro di voler uscire?</h2>
    <form method="POST" action="../app/controllers/logout_controller.php">
        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <button type="submit">Logout</button>
    </form>
</main>
