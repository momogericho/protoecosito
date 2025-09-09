<?php
 // templates/footer.php

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
    'dashboard.php' => 'Conferma Acquisti',
 ];


 $pageName = $pageTitles[$currentPage] ?? $currentPage;

?>
    <footer id="footer">
        <p>&copy; <?= date('Y') ?> - Autore: <strong>Mohamed el Hadi Hadj Mahmoud</strong></p>
        <p>Stai visualizzando: <em><?= htmlspecialchars($pageName) ?></em></p>
    </footer>
  </body>
</html>
