<?php
require_once __DIR__ . '/../helpers/validation.php';
require_once __DIR__ . '/../helpers/credential_store.php';
require_once __DIR__ . '/../../models/UserRepository.php';
require_once __DIR__ . '/../../models/AziendaRepository.php';
require_once __DIR__ . '/../../models/ArtigianoRepository.php';
require_once __DIR__ . '/../../security/csrf.php';

class RegistrationController {
   
    // Gestione registrazione azienda
    public function handleAzienda(array $post): array {
        $errors = [];

        // CSRF
        if ($e = $this->checkCsrf($post['csrf_token'] ?? '')) return $e;

        // Validazioni
        if ($e = Validation::ragione($post['ragione'] ?? null))  $errors['ragione']  = $e;
        if ($e = Validation::indirizzo($post['address2'] ?? null)) $errors['address2'] = $e;
        if ($e = Validation::nick($post['nick'] ?? null))        $errors['nick']     = $e;
        if ($e = Validation::password($post['password'] ?? null))$errors['password'] = $e;

        if ($errors) return ['ok'=>false, 'errors'=>$errors];

        // Nick univoco
        $userRepo = new UserRepository();
        if ($userRepo->findByNick($post['nick'])) {
            return ['ok'=>false, 'errors'=>['nick'=>"Username già in uso."]];
        }

        // Creazione utente + azienda (transazione)
        Db::beginTransaction();
        $credentialId = null;
        try {
            $created = $userRepo->create($post['nick'], $post['password'], false); // azienda => artigiano = false
            $userId = $created['userId'];
            $credentialId = $created['credentialId'];

            $azRepo = new AziendaRepository();
            $azRepo->create($userId, $post['ragione'], $post['address2']);

            Db::commit();
            return ['ok'=>true];
        } catch (Throwable $ex) {
            Db::rollBack();
            if ($credentialId) {
                CredentialStore::delete($credentialId);
            }
            return ['ok'=>false, 'errors'=>['general'=>"Errore salvataggio: ".$ex->getMessage()]];
        }
    }

    // Gestione registrazione artigiano
    public function handleArtigiano(array $post): array {
        $errors = [];

        // CSRF
        if ($e = $this->checkCsrf($post['csrf_token'] ?? '')) return $e;

        // Validazioni
        if ($e = Validation::name($post['name'] ?? null))        $errors['name']     = $e;
        if ($e = Validation::surname($post['surname'] ?? null))  $errors['surname']  = $e;
        if ($e = Validation::birthdate($post['birthdate'] ?? null)) $errors['birthdate'] = $e;
        if ($e = Validation::credit($post['credit'] ?? null))    $errors['credit']   = $e;
        if ($e = Validation::indirizzo($post['address'] ?? null))$errors['address']  = $e;
        if ($e = Validation::nick($post['nick'] ?? null))        $errors['nick']     = $e;
        if ($e = Validation::password($post['password'] ?? null))$errors['password'] = $e;

        if ($errors) return ['ok'=>false, 'errors'=>$errors];

        $userRepo = new UserRepository();
        if ($userRepo->findByNick($post['nick'])) {
            return ['ok'=>false, 'errors'=>['nick'=>"Username già in uso."]];
        }

        Db::beginTransaction();
        $credentialId = null;
        try {
            $created = $userRepo->create($post['nick'], $post['password'], true); // artigiano = true
            $userId = $created['userId'];
            $credentialId = $created['credentialId'];

            $arRepo = new ArtigianoRepository();
            $arRepo->create(
                $userId,
                $post['name'], $post['surname'],
                $post['birthdate'], $post['credit'], $post['address']
            );

            Db::commit();
            return ['ok'=>true];
        } catch (Throwable $ex) {
            Db::rollBack();
            if ($credentialId) {
                CredentialStore::delete($credentialId);
            }
            return ['ok'=>false, 'errors'=>['general'=>"Errore salvataggio: ".$ex->getMessage()]];
        }
    }

    // Controllo token CSRF
    private function checkCsrf(string $token): ?array {
        if (!validateCsrfToken($token)) {
            return ['ok'=>false, 'errors'=>['general'=>"⚠️ Richiesta non valida: Token CSRF non valido."]];
        }
        return null;
    }
}
