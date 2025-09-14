<?php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
$pageTitle = "Home";
$pageUrl = BASE_URL . '/home.php';

?>

<?php 
render('partials/header.php');
render('pages/home_view.php');
render('partials/footer.php'); 
?>
