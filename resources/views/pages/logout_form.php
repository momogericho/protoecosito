<?php ?>
<main id="mainContent"  role="main" class="logout-container">
    <h2>Sei sicuro di voler uscire?</h2>
    <form method="POST" action="<?= BASE_URL ?>/api/logout_controller.php" id="logoutForm">
        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <button type="submit" id="btnLogout">Logout</button>
    </form>
</main>
