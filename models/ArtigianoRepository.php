<?php
class ArtigianoRepository {
    public function __construct(private PDO $pdo) {}

    public function create(int $userId, string $name, string $surname, string $birthdate, string $credit, string $address): int {
        $st = $this->pdo->prepare(
            "INSERT INTO dati_artigiani (id_utente, name, surname, birthdate, credit, address)
             VALUES (:u,:n,:s,:b,:c,:a)"
        );
        $st->execute([
            ':u'=>$userId, ':n'=>$name, ':s'=>$surname, ':b'=>$birthdate,
            ':c'=>$credit, ':a'=>$address
        ]);
        return (int)$this->pdo->lastInsertId();
    }
}
