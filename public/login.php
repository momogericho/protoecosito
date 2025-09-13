
<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/login_initializer.php';
render('partials/header.php');
render('pages/login_form.php');
render('partials/footer.php');
?>
