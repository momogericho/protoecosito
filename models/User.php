<?php
class User {
    
    // Recupera utente da username
    public function getByNick($nick) {
        $stmt = Db::prepare("SELECT * FROM utenti WHERE nick = :nick LIMIT 1");
        $stmt->execute(['nick' => $nick]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Recupera saldo se artigiano, altrimenti zero
    public function getCredit($userId, $isArtigiano) {
        if ($isArtigiano) {
            $stmt = Db::prepare("SELECT credit FROM dati_artigiani WHERE id_utente = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? number_format($row['credit'], 2, ',', '.') : "0.00";
        }
        return "0.00";
    }

    // Salva token "ricordami" per l'utente
    public function storeRememberToken($userId, $token) {
        $hash = hash('sha256', $token);
        $stmt = Db::prepare("UPDATE utenti SET remember_token = :token WHERE id = :id");
        $stmt->execute(['token' => $hash, 'id' => $userId]);
    }

    // Recupera utente tramite token "ricordami"
    public function getByRememberToken($token) {
        $hash = hash('sha256', $token);
        $stmt = Db::prepare("SELECT * FROM utenti WHERE remember_token = :token LIMIT 1");
        $stmt->execute(['token' => $hash]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cancella il token "ricordami"
    public function clearRememberToken($userId) {
        $stmt = Db::prepare("UPDATE utenti SET remember_token = NULL WHERE id = :id");
        $stmt->execute(['id' => $userId]);
    }
}
?>