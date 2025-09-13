<?php  
$lp = $_SESSION['last_purchase'];
$total = number_format($lp['total'], 2, ',', '.');
$newCredit = number_format($lp['new_credit'], 2, ',', '.');
unset($_SESSION['last_purchase']);
?>