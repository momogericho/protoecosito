<?php
require_once __DIR__ . '/../app/session_helpers.php';
startSecureSession();
$pageTitle = "Home";
?>

<?php include __DIR__ . "/../templates/header.php"; ?>

<?php include __DIR__ . "/../templates/home_view.php"; ?>

<?php include __DIR__ . "/../templates/footer.php"; ?>
