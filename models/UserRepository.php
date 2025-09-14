<?php
class UserRepository {

    public function findByNick(string $nick): ?array {
        $st = Db::prepareRead("SELECT * FROM utenti WHERE nick = :n LIMIT 1");
        $st->execute([':n' => $nick]);
        $u = $st->fetch();
        return $u ?: null;
    }

    public function create(string $nick, string $passwordHash, bool $isArtigiano): int {
        $st = Db::prepareWrite(
            "INSERT INTO utenti (nick, password, artigiano) VALUES (:n, :p, :a)"
        );
        $st->execute([
            ':n' => $nick,
            ':p' => $passwordHash,
            ':a' => $isArtigiano ? 1 : 0
        ]);
        return (int)Db::lastInsertId();
    }
}
