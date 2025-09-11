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
    $file = __DIR__ . '/azienda_materiali.json';
    $tmp  = $file . '.tmp';
    file_put_contents($tmp, json_encode($map, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    rename($tmp, $file);
}