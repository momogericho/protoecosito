<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/config/Db.php';
require_once BASE_PATH . '/app/helpers/session_helpers.php';
startSecureSession();