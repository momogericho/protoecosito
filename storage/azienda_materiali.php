<?php
/**
 * Storage helper per la mappa azienda→materiali.
 */
function loadAziendaMateriali(): array
{
    // Carica la mappa da file JSON
    // Restituisce array associativo aziendaId => [materialiIds]
    $file = __DIR__ . '/azienda_materiali.json';
    if (!file_exists($file)) {
        file_put_contents($file, json_encode(new stdClass(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
    $map = json_decode(@file_get_contents($file), true);
    return is_array($map) ? $map : [];
}

function saveAziendaMateriali(array $map): void
{
    // Salva la mappa su file JSON in modo atomico
    // Nota: è necessario un lock esclusivo sul file durante tutta
    // l'operazione di salvataggio.
    $file = __DIR__ . '/azienda_materiali.json';
    $tmp  = $file . '.tmp';
    
    $fh = @fopen($file, 'c');
    if ($fh === false) {
        throw new RuntimeException("Impossibile aprire '$file'");
    }
    if (!flock($fh, LOCK_EX)) {
        fclose($fh);
        throw new RuntimeException("Impossibile ottenere il lock su '$file'");
    }

    $tmpFh = @fopen($tmp, 'wb');
    if ($tmpFh === false) {
        flock($fh, LOCK_UN);
        fclose($fh);
        throw new RuntimeException("Impossibile aprire il file temporaneo '$tmp'");
    }
    if (!flock($tmpFh, LOCK_EX)) {
        fclose($tmpFh);
        flock($fh, LOCK_UN);
        fclose($fh);
        throw new RuntimeException("Impossibile ottenere il lock su '$tmp'");
    }

    $json = json_encode($map, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    if ($json === false || fwrite($tmpFh, $json) === false) {
        flock($tmpFh, LOCK_UN);
        fclose($tmpFh);
        flock($fh, LOCK_UN);
        fclose($fh);
        throw new RuntimeException('Errore nella scrittura del file temporaneo');
    }

    fflush($tmpFh);
    flock($tmpFh, LOCK_UN);
    fclose($tmpFh);

    if (!rename($tmp, $file)) {
        flock($fh, LOCK_UN);
        fclose($fh);
        throw new RuntimeException("Impossibile rinominare '$tmp' in '$file'");
    }

    flock($fh, LOCK_UN);
    fclose($fh);
}