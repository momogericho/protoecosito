<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/domanda_initializer.php'; 

require_once __DIR__ . '/../templates/header.php';  // <-- header include user_status -->
require_once __DIR__ . '/../templates/domanda_form.php';
require_once __DIR__ . '/../templates/footer.php'; ?>
