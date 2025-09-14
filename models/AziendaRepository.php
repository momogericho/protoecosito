<?php
class AziendaRepository {
    
    public function create(int $userId, string $ragione, string $address2): int {
        $st = Db::prepareWrite(
            "INSERT INTO dati_aziende (id_utente, ragione, address2) VALUES (:u, :r, :a)"
        );
        $st->execute([':u'=>$userId, ':r'=>$ragione, ':a'=>$address2]);
        return (int)Db::lastInsertId();
    }
}
