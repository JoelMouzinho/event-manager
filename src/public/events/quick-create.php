<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    $userId = currentUserId();
    $eventId = createEvent($pdo, $userId, 'Mein Event');
    $_SESSION['current_event_id'] = $eventId;
    header('Location: ../home/index.php');
    exit;
}

header('Location: index.php');
exit;