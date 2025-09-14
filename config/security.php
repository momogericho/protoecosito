<?php
if (!defined('PEPPER')) {
    $pepper = getenv('PEPPER');
    if ($pepper === false) {
        throw new RuntimeException('PEPPER environment variable not set');
    }
    define('PEPPER', $pepper);
}