<?php
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/app/helpers/credential_store.php';
require_once BASE_PATH . '/models/UserRepository.php';

$st = Db::prepareRead('SELECT id, password FROM utenti');
$st->execute();
$users = $st->fetchAll(PDO::FETCH_ASSOC);

$userRepo = new UserRepository();
$updated = 0;

// Migrazione delle credenziali
Db::beginTransaction();
try {
    foreach ($users as $user) {
        $userId = (int)$user['id'];
        $stored = (string)$user['password'];

        if (CredentialStore::isCredentialId($stored)) {
            if (!CredentialStore::fetch($stored)) {
                fprintf(STDERR, "Attenzione: credenziale mancante per utente %d.\n", $userId);
            }
            continue;
        }

        $generated = CredentialStore::generateCredential($stored);
        $credentialId = $generated['credentialId'];

        try {
            $userRepo->updateCredentialId($userId, $credentialId);
            CredentialStore::store($credentialId, $generated['record']);
            $updated++;
        } catch (Throwable $ex) {
            CredentialStore::delete($credentialId);
            $userRepo->updateCredentialId($userId, $stored);
            throw $ex;
        }
    }
    Db::commit();
} catch (Throwable $ex) {
    Db::rollBack();
    throw $ex;
}

printf("Credenziali migrate: %d\n", $updated);