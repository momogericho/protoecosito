<?php
// lista.php
// ---------------------------------------------
// Pagina che elenca i materiali disponibili.
// - Tutti vedono: nome, descrizione
// - Artigiani autenticati vedono anche: quantità, costo unitario
// - Supporta filtro "dopo data" (YYYY-MM-DD)
// ---------------------------------------------

require_once __DIR__ . '/../app/init.php';
$csrf = AppInitializer::init();
require_once __DIR__ . '/../app/initializers/lista_initializer.php';

render('partials/header.php'); // include anche user_status

?>

<link rel="stylesheet" href="/public/css/lista.css">
<?php render('pages/lista_view.php'); ?>
<script src="/public/js/lista.js" defer></script>

<?php render('partials/footer.php'); ?>
