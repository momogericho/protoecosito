<?php
// lista.php
// ---------------------------------------------
// Pagina che elenca i materiali disponibili.
// - Tutti vedono: nome, descrizione
// - Artigiani autenticati vedono anche: quantità, costo unitario
// - Supporta filtro "dopo data" (YYYY-MM-DD)
// ---------------------------------------------

require_once __DIR__ . '/../app/session_helpers.php';
startSecureSession();

require_once __DIR__ . '/../config/db.php';

require_once __DIR__ . '/../templates/header.php'; // include anche user_status

require_once __DIR__ . '/../app/lista_initializer.php';

?>

<link rel="stylesheet" href="/public/css/lista.css">
<?php require_once __DIR__ . '/../templates/lista_view.php'; ?>
<script src="/public/js/lista.js" defer></script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
