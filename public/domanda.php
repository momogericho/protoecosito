<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/domanda_initializer.php'; 

render('partials/header.php');  // <-- header include user_status -->
render('pages/domanda_form.php');
render('partials/footer.php')
?>
