<?php
 //resources/views/partials/footer.php
 // Ricava il nome file della pagina corrente (es: "login.php")
 $currentPage = basename($_SERVER['PHP_SELF']);


 $pageTitles = [
    'home.php' => 'Home',
    'login.php'     => 'Pagina di Login',
    'logout.php'    => 'Logout',
    'registra.php'     => 'Pagina di Registrazione',
    'lista.php'     => 'Lista Materiali',
    'offerta.php' => 'Offerta',
    'domanda.php'     => 'Domanda',
    'conferma.php' => 'Conferma Acquisti',
    'fine.php' => 'Fine',
 ];


 $pageName = $pageTitles[$currentPage] ?? $currentPage;

?>
    <footer id="footer" role="contentinfo">
        <p>&copy; <?= date('Y') ?> - Autore: <strong>Mohamed el Hadi Hadj Mahmoud</strong></p>
        <p>Stai visualizzando: <em><?= e($pageName) ?></em></p>
    </footer>
  </body>
</html>
