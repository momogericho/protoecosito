<?php
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/app/helpers/remember.php';

$removed = remember_tokens_prune();
printf("Token remember-me scaduti rimossi: %d\n", $removed);