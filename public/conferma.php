<?php
// conferma.php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/conferma_initializer.php';
$pageUrl = BASE_URL . '/conferma.php';

render('partials/header.php', ['pageStyles' => ['domanda.css']]);
render('pages/conferma_form.php');
render('partials/footer.php');
?>