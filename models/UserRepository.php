<?php
require_once __DIR__ . '/../app/helpers/credential_store.php';

class UserRepository {

    // Cerca utente per nick, ritorna null se non trovato
    public function findByNick(string $nick): ?array {
        $st = Db::prepareRead("SELECT * FROM utenti WHERE nick = :n LIMIT 1");
        $st->execute([':n' => $nick]);
        $u = $st->fetch();
        return $u ?: null;
    }

    // Crea un nuovo utente e ritorna il suo ID e credentialId
    public function create(string $nick, string $password, bool $isArtigiano): array {
        $generated = CredentialStore::generateCredential($password);
        $credentialId = $generated['credentialId'];
        $record = $generated['record'];

        $st = Db::prepareWrite(
            "INSERT INTO utenti (nick, password, artigiano) VALUES (:n, :p, :a)"
        );
        $st->execute([
            ':n' => $nick,
            ':p' => $credentialId,
            ':a' => $isArtigiano ? 1 : 0
        ]);
        $userId = (int)Db::lastInsertId();

        try {
            CredentialStore::store($credentialId, $record);
        } catch (Throwable $ex) {
            if (!Db::inTransaction()) {
                $this->deleteById($userId);
            }
            throw $ex;
        }

        return [
            'userId' => $userId,
            'credentialId' => $credentialId,
        ];
    }

    // Aggiorna il credentialId di un utente
    public function updateCredentialId(int $userId, string $credentialId): void {
        $st = Db::prepareWrite("UPDATE utenti SET password = :p WHERE id = :id");
        $st->execute([':p' => $credentialId, ':id' => $userId]);
    }

    // Elimina utente per ID
    private function deleteById(int $userId): void {
        $st = Db::prepareWrite('DELETE FROM utenti WHERE id = :id LIMIT 1');
        $st->execute([':id' => $userId]);
    }
}
