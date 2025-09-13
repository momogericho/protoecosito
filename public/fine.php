<?php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
require_once __DIR__ . '/../app/initializers/fine_initializer.php';

require BASE_VIEW_PATH.'/partials/header.php';
require BASE_VIEW_PATH.'/pages/fine_view.php';
require BASE_VIEW_PATH.'/partials/footer.php';
?>


