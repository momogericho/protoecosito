<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Recupera utente da username
    public function getByNick($nick) {
        $stmt = $this->pdo->prepare("SELECT * FROM utenti WHERE nick = :nick LIMIT 1");
        $stmt->execute(['nick' => $nick]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Recupera saldo se artigiano, altrimenti zero
    public function getCredit($userId, $isArtigiano) {
        if ($isArtigiano) {
            $stmt = $this->pdo->prepare("SELECT credit FROM dati_artigiani WHERE id_utente = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? number_format($row['credit'], 2, ',', '.') : "0.00";
        }
        return "0.00";
    }
}
