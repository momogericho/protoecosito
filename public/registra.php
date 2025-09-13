<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();

require_once __DIR__ . '/../app/initializers/registra_initializer.php';

render('partials/header.php');
render('pages/registration_form.php');
render('partials/footer.php');
?>
