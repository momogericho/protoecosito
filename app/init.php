<?php
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/security/csrf.php';

class AppInitializer
{
    /**
     * Prepare common resources for each request.
     * Starts the session (handled in included files), ensures a CSRF token
     * is available and returns it so controllers can embed it in forms.
     */
    public static function init(): string
    {
        return generateCsrfToken();
    }
}