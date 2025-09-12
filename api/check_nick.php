<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/UserRepository.php';

header('Content-Type: application/json; charset=utf-8');

$nick = $_GET['nick'] ?? '';

$userRepo = new UserRepository($pdo);
$exists = false;
if ($nick !== '') {
    $exists = $userRepo->findByNick($nick) !== null;
}

echo json_encode(['exists' => $exists]);