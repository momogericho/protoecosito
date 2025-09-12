<?php
// Connessione al DB con PDO (sicuro)
$dsn = "mysql:host=localhost;dbname=eco_scambio;charset=utf8mb4";
$user = "modificatore";
$pass = "Str0ng#Admin9";

$caCert = getenv('MYSQL_CA_CERT');
if (!$caCert) {
    die('Certificato CA non configurato.');
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_CA => $caCert,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true    ]);
} catch (PDOException $e) {
    die("Errore di connessione DB: " . $e->getMessage());
}

