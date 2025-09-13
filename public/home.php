<?php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
$pageTitle = "Home";
?>

<?php 
require BASE_VIEW_PATH.'/partials/header.php';
require BASE_VIEW_PATH.'/pages/home_view.php'; 
require BASE_VIEW_PATH.'/partials/footer.php'; 
?>
