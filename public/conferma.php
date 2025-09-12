<?php
// conferma.php
require_once __DIR__ . '/../app/session_helpers.php';
startSecureSession();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../security/csrf.php';

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../app/conferma_initializer.php';
require_once __DIR__ . '/../templates/conferma_form.php';
require_once __DIR__ . '/../templates/footer.php';
?>
