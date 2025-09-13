
<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/login_initializer.php';
require BASE_VIEW_PATH.'/partials/header.php';
require BASE_VIEW_PATH.'/pages/login_form.php';
require BASE_VIEW_PATH.'/partials/footer.php';
?>
