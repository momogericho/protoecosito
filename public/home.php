<?php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
$pageTitle = "Home";
?>

<?php require_once __DIR__ . "/../templates/header.php"; ?>

<?php require_once __DIR__ . "/../templates/home_view.php"; ?>

<?php require_once __DIR__ . "/../templates/footer.php"; ?>
