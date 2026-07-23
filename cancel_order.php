<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$order_id = (int) ($_POST['order_id'] ?? 0);

if ($order_id > 0) {
    $res = mysqli_query($connect, "SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id");

    if ($res && mysqli_num_rows($res) > 0) {
        $order = mysqli_fetch_assoc($res);

        if (in_array($order['order_status'], ['Delivered', 'Cancelled'], true)) {
            $_SESSION['order_msg'] = "This order can no longer be cancelled.";
        } else {
            mysqli_query($connect, "UPDATE orders SET order_status='Cancelled' WHERE id=$order_id");
            $_SESSION['order_msg'] = "Order #$order_id has been cancelled.";
        }
    } else {
        $_SESSION['order_msg'] = "Order not found.";
    }
}

header("Location: my_orders.php");
exit;
