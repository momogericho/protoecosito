<?php
require_once __DIR__ . '/../app/validation.php';
require_once __DIR__ . '/../models/UserRepository.php';
require_once __DIR__ . '/../models/AziendaRepository.php';
require_once __DIR__ . '/../models/ArtigianoRepository.php';

class RegistrationController {
    public function __construct(private PDO $pdo) {}

    public function handleAzienda(array $post): array {
        $errors = [];

        // CSRF
        
        if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                     die("⚠️ Richiesta non valida: Token CSRF non valido.");
            }

        // Validazioni
        if ($e = Validation::ragione($post['ragione'] ?? null))  $errors['ragione']  = $e;
        if ($e = Validation::indirizzo($post['address2'] ?? null)) $errors['address2'] = $e;
        if ($e = Validation::nick($post['nick'] ?? null))        $errors['nick']     = $e;
        if ($e = Validation::password($post['password'] ?? null))$errors['password'] = $e;

        if ($errors) return ['ok'=>false, 'errors'=>$errors];

        // Nick univoco
        $userRepo = new UserRepository($this->pdo);
        if ($userRepo->findByNick($post['nick'])) {
            return ['ok'=>false, 'errors'=>['nick'=>"Username già in uso."]];
        }

        // Creazione utente + azienda (transazione)
        $this->pdo->beginTransaction();
        try {
            $hash = password_hash($post['password'], PASSWORD_DEFAULT);
            $userId = $userRepo->create($post['nick'], $hash, false); // azienda => artigiano = false

            $azRepo = new AziendaRepository($this->pdo);
            $azRepo->create($userId, $post['ragione'], $post['address2']);

            $this->pdo->commit();
            return ['ok'=>true];
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            return ['ok'=>false, 'errors'=>['general'=>"Errore salvataggio: ".$ex->getMessage()]];
        }
    }

    public function handleArtigiano(array $post): array {
        $errors = [];

         if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                     die("⚠️ Richiesta non valida: Token CSRF non valido.");
            }

        if ($e = Validation::name($post['name'] ?? null))        $errors['name']     = $e;
        if ($e = Validation::surname($post['surname'] ?? null))  $errors['surname']  = $e;
        if ($e = Validation::birthdate($post['birthdate'] ?? null)) $errors['birthdate'] = $e;
        if ($e = Validation::credit($post['credit'] ?? null))    $errors['credit']   = $e;
        if ($e = Validation::indirizzo($post['address'] ?? null))$errors['address']  = $e;
        if ($e = Validation::nick($post['nick'] ?? null))        $errors['nick']     = $e;
        if ($e = Validation::password($post['password'] ?? null))$errors['password'] = $e;

        if ($errors) return ['ok'=>false, 'errors'=>$errors];

        $userRepo = new UserRepository($this->pdo);
        if ($userRepo->findByNick($post['nick'])) {
            return ['ok'=>false, 'errors'=>['nick'=>"Username già in uso."]];
        }

        $this->pdo->beginTransaction();
        try {
            $hash = password_hash($post['password'], PASSWORD_DEFAULT);
            $userId = $userRepo->create($post['nick'], $hash, true); // artigiano = true

            $arRepo = new ArtigianoRepository($this->pdo);
            $arRepo->create(
                $userId,
                $post['name'], $post['surname'],
                $post['birthdate'], $post['credit'], $post['address']
            );

            $this->pdo->commit();
            return ['ok'=>true];
        } catch (Throwable $ex) {
            $this->pdo->rollBack();
            return ['ok'=>false, 'errors'=>['general'=>"Errore salvataggio: ".$ex->getMessage()]];
        }
    }

    private function checkCsrf(string $token): bool {
        return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }
}
