<?php
require_once __DIR__ . '/../../app/helpers/session/SessionManager.php';

session_save_path(sys_get_temp_dir());
SessionManager::startSecureSession();

if (session_status() !== PHP_SESSION_ACTIVE) {
    echo "Session not active\n";
    exit(1);
}

echo "Session started\n";