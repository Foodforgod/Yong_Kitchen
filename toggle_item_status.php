<?php
include 'db.php';
header('Content-Type: application/json');

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $res = $conn->query("SELECT item_status FROM order_items WHERE id = $id");
    if($row = $res->fetch_assoc()) {
        $new_status = ($row['item_status'] == 'done') ? 'pending' : 'done';
        
        $stmt = $conn->prepare("UPDATE order_items SET item_status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $id);
        
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'new_status' => $new_status]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}
?>