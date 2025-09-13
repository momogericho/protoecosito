
<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();

render('partials/header.php');
render('pages/logout_form.php');
render('partials/footer.php');
?>

