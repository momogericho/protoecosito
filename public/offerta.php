
<?php
require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/offerta_initializer.php';
// Abilita la compressione gzip se supportata dal client
if (function_exists('ob_gzhandler')) {
    ob_start('ob_gzhandler');
}

if (!isset($materiali, $csrf, $aziendaId)) {
    throw new RuntimeException('Variabili necessarie non disponibili');
}

require_once __DIR__ . '/../templates/header.php';
?>

<link rel="stylesheet" href="/public/css/offerta.css">

<main class="offerta" id="mainContent">
    <?php require_once __DIR__ . '/../templates/offerta_elenco_materiali.php'; ?>
    <?php require_once __DIR__ . '/../templates/offerta_inserimento_nuovo_materiale.php'; ?>
</main>

<?php require_once __DIR__ . '/../app/helpers/offerta_bootstrap_js.php'; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>



 

  



