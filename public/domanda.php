<?php
require_once __DIR__ . '/../app/session_helpers.php';
startSecureSession();
?>
<?php require_once __DIR__ . '/../config/db.php'; ?>
<?php require_once __DIR__ . '/../security/csrf.php'; ?>


<?php require_once __DIR__ . '/../templates/header.php'; ?> <!-- header include user_status -->

<?php require_once __DIR__ . '/../templates/domanda_form.php'; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
