<?php
include 'db.php';

if (isset($_POST['mark_ready'])) {
    $order_id = $_POST['order_id'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = 'ready' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        header("Location: kitchen.php?success=1");
    } else {
        echo "Error updating order: " . $conn->error;
    }
    exit();
}
?>