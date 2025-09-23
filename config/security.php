<?php
if (!defined('PEPPER')) {
    define('PEPPER', '');
}

if (!defined('REMEMBER_KEY')) {
    $rememberKey = getenv('REMEMBER_KEY');
    if ($rememberKey === false) {
        throw new RuntimeException('REMEMBER_KEY environment variable not set');
    }
    define('REMEMBER_KEY', $rememberKey);
}