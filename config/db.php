<?php
// Connessione al DB con PDO (sicuro)
$dsn = "mysql:host=localhost;dbname=sito;charset=utf8mb4";
$user = "root";
$pass = "";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Errore di connessione DB: " . $e->getMessage());
}

