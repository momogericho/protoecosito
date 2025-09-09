<?php
class Validation {
    // Ragione sociale (max 30, inizia con maiuscola, lettere/numeri/&/spazio)
    public static function ragione(?string $s): ?string {
        if (!is_string($s) || $s === '') return "Ragione richiesta.";
        if (mb_strlen($s) > 30) return "Ragione max 30 caratteri.";
        if (!preg_match('/^\p{Lu}[\p{L}\p{Nd}& ]{0,29}$/u', $s)) {
            return "Ragione non valida (inizia con maiuscola, ammessi lettere/numeri/&/spazio).";
        }
        return null;
    }

    // Indirizzo "Via|Corso Nome nr, Città"
    public static function indirizzo(?string $s): ?string {
        if (!is_string($s) || $s === '') return "Indirizzo richiesto.";
        $ok = preg_match('/^(Via|Corso)\s+[\p{L} ]+\s+\d{1,3},\s*[\p{L} ]+$/u', $s);
        return $ok ? null : "Indirizzo non valido. Es: \"Via Roma 12, Torino\"";
    }

    // Nick 4-10, inizia con lettera, ammessi [a-zA-Z0-9_-]
    public static function nick(?string $s): ?string {
        if (!is_string($s) || $s === '') return "Username richiesto.";
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_-]{3,9}$/', $s)) {
            return "Username 4-10, solo lettere/numeri/-/_ e inizia con lettera.";
        }
        return null;
    }

    // Password 8-16, solo [A-Za-z0-9.;+=] + requisiti composizione
    public static function password(?string $s): ?string {
        if (!is_string($s) || $s === '') return "Password richiesta.";
        if (!preg_match('/^[A-Za-z0-9.;+=]{8,16}$/', $s)) {
            return "Password 8-16, solo lettere/numeri e . ; + =";
        }
        if (!preg_match('/[A-Z]/', $s))   return "Password: manca una maiuscola.";
        if (!preg_match('/[a-z]/', $s))   return "Password: manca una minuscola.";
        if (!preg_match('/\d/', $s))      return "Password: manca un numero.";
        if (!preg_match('/[.;+=]/', $s))  return "Password: manca un simbolo tra . ; + =";
        return null;
    }

    // Nome 4-14, lettere + spazio
    public static function name(?string $s): ?string {
        if (!is_string($s) || $s === '') return "Nome richiesto.";
        return preg_match('/^[\p{L} ]{4,14}$/u', $s) ? null : "Nome 4-14, solo lettere e spazio.";
    }

    // Cognome 4-16, lettere + spazio/apostrofo
    public static function surname(?string $s): ?string {
        if (!is_string($s) || $s === '') return "Cognome richiesto.";
        return preg_match('/^[\p{L} \']{4,16}$/u', $s) ? null : "Cognome 4-16, solo lettere, spazio o apostrofo.";
    }

    // Birthdate YYYY-M-D (o con zeri) e data reale
    public static function birthdate(?string $s): ?string {
        if (!is_string($s) || $s === '') return "Data di nascita richiesta.";
        if (!preg_match('/^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/', $s)) {
            return "Data non valida (formato aaaa-mm-gg).";
        }
        [$y,$m,$d] = array_map('intval', explode('-', $s));
        if (!checkdate($m,$d,$y)) return "Data inesistente.";
        return null;
    }

    // Credit: 2 decimali, multipli di 0.05
    public static function credit($s): ?string {
        if (!is_numeric($s)) return "Credito non valido.";
        $str = (string)$s;
        if (!preg_match('/^\d+(?:\.\d{2})$/', $str)) return "Credito con 2 decimali (es. 12.50).";
        $cents = (int)round(((float)$s)*100);
        if ($cents % 5 !== 0) return "Credito a multipli di 0.05.";
        if ($cents < 0) return "Credito non negativo.";
        return null;
    }
}
