<?php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
require_once __DIR__ . '/../app/initializers/fine_initializer.php';
$pageUrl = BASE_URL . '/fine.php';

render('partials/header.php');
render('pages/fine_view.php');
render('partials/footer.php');
?>


