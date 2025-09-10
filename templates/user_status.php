<?php
// templates/user_status.php

// Se utente non loggato → mostra login
if (!isset($_SESSION['user_id'])) {
    echo '<div class="user-status">' .
         'Non loggato | Saldo: €0.00 ' .
         '<button id="loginBtn">Login</button>' .
         '</div>';
} else {
    $nick = htmlspecialchars($_SESSION['nick']);
    $credit = htmlspecialchars($_SESSION['credit']);
     echo '<div class="user-status">' .
         $nick . ' | Saldo: €' . $credit . ' ' .
         '<button id="logoutBtn">Logout</button>' .
         '</div>';
}
?>


