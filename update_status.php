<?php
include 'db.php';

if (isset($_POST['mark_done'])) {
    $order_id = intval($_POST['order_id']);
    $sql = "UPDATE orders SET status = 'ready' WHERE id = $order_id";
    
    if ($conn->query($sql)) {
        header("Location: kitchen.php"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>