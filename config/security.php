<?php
if (!defined('PEPPER')) {
    $pepper = getenv('PEPPER');
    if ($pepper === false) {
        throw new RuntimeException('PEPPER environment variable not set');
    }
    define('PEPPER', $pepper);
}

if (!defined('REMEMBER_KEY')) {
    $rememberKey = getenv('REMEMBER_KEY');
    if ($rememberKey === false) {
        throw new RuntimeException('REMEMBER_KEY environment variable not set');
    }
    define('REMEMBER_KEY', $rememberKey);
}