<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/domanda_initializer.php';

$isArtigiano = !empty($_SESSION['user_id']) && isset($_SESSION['artigiano']) && (int)$_SESSION['artigiano'] === 1;

render('partials/header.php');  // <-- header include user_status -->
if (!$isArtigiano) {
    echo '<p class="warning">Per inviare una domanda devi essere autenticato.</p>';
} else {
    render('pages/domanda_form.php');
}
render('partials/footer.php');
?>
