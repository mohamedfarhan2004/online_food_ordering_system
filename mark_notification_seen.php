<?php
/**
 * Called via fetch() when the user closes a notification toast.
 * Marks that specific order as notified=1 so it won't show again,
 * but only if it belongs to the logged-in user.
 */
session_start();
include __DIR__ . '/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$order_id = (int) ($_POST['order_id'] ?? 0);

if ($order_id > 0) {
    mysqli_query($connect, "UPDATE orders SET notified=1 WHERE id=$order_id AND user_id=$user_id");
}

echo json_encode(['ok' => true]);
