<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();

require_once __DIR__ . '/../app/initializers/registra_initializer.php';

require BASE_VIEW_PATH.'/partials/header.php';

require BASE_VIEW_PATH.'/pages/registration_form.php';

require BASE_VIEW_PATH.'/partials/footer.php';
?>
