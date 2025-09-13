<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/domanda_initializer.php'; 

require BASE_VIEW_PATH.'/partials/header.php';  // <-- header include user_status -->
require BASE_VIEW_PATH.'/pages/domanda_form.php';
require BASE_VIEW_PATH.'/partials/footer.php'; 
?>
