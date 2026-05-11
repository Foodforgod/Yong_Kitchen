<?php
include 'db.php';

if (isset($_POST['mark_ready'])) {
    $order_id = intval($_POST['order_id']);
    
    $conn->query("UPDATE orders SET status = 'ready' WHERE id = $order_id");
    
    header("Location: kitchen.php");
    exit();
}
?>