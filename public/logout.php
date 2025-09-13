
<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();

require BASE_VIEW_PATH.'/partials/header.php';
require BASE_VIEW_PATH.'/pages/logout_form.php';
require BASE_VIEW_PATH.'/partials/footer.php';
?>

