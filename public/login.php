
<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/login_initializer.php';
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/login_form.php';
require_once __DIR__ . '/../templates/footer.php';
?>
