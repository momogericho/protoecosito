<?php
/**
 * Verifica che la directory di storage abbia i permessi corretti.
 */
function checkStoragePermissions(string $dir): void {
    $expectedPerm = 0750;
    $info = stat($dir);
    if ($info === false) {
        error_log("Directory di storage non trovata: $dir");
        return;
    }
    $perm = $info['mode'] & 0777;
    $uid = $info['uid'];
    $gid = $info['gid'];

    // Controlla che i permessi siano 0750 e che il proprietario sia l'utente web server
    // Nota: posix_* richiede l'estensione POSIX abilitata in PHP
    $hasPosix = function_exists('posix_getuid') && function_exists('posix_getgid');


    if ($hasPosix) {
        $currentUid = posix_getuid();
        $currentGid = posix_getgid();
        if ($perm !== $expectedPerm || $uid !== $currentUid || $gid !== $currentGid) {
            error_log("Permessi/ownership errati per $dir");
        }
    } else {
        if ($perm !== $expectedPerm) {
            error_log("Permessi errati per $dir");
        }
    }
}