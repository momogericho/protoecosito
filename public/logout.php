
<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
$pageUrl = BASE_URL . '/logout.php';

render('partials/header.php');
render('pages/logout_form.php');
render('partials/footer.php');
?>

