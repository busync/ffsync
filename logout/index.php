<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
session_destroy();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    header('Location: ../login/');
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Logout successful'
]);