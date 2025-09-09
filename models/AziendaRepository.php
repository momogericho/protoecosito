<?php
class AziendaRepository {
    public function __construct(private PDO $pdo) {}

    public function create(int $userId, string $ragione, string $address2): int {
        $st = $this->pdo->prepare(
            "INSERT INTO dati_aziende (id_utente, ragione, address2) VALUES (:u, :r, :a)"
        );
        $st->execute([':u'=>$userId, ':r'=>$ragione, ':a'=>$address2]);
        return (int)$this->pdo->lastInsertId();
    }
}
