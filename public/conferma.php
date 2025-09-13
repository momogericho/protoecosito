<?php
// conferma.php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/conferma_initializer.php';
render('partials/header.php');
render('pages/conferma_form.php');
render('partials/footer.php');
?>