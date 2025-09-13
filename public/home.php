<?php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
$pageTitle = "Home";
?>

<?php 
render('partials/header.php');
render('pages/home_view.php');
render('partials/footer.php'); 
?>
