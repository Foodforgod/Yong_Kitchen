<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $item_id = $_POST['item_id'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $table_number = $_POST['table_number'] ?? 'T1'; 
    
    $total = $price * $qty;
    $status = 'pending'; 

    $sql_order = "INSERT INTO orders (table_number, total_price, status) 
                  VALUES ('$table_number', '$total', '$status')";
    
    if ($conn->query($sql_order)) {
        
        $order_id = $conn->insert_id;

        
        $sql_details = "INSERT INTO order_items (order_id, item_id, quantity) 
                        VALUES ('$order_id', '$item_id', '$qty')";
        $conn->query($sql_details);

        
        $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");
        
        echo "<h1>Order Success!</h1>";
        echo "<p>Order ID: #$order_id</p>";
<<<<<<< HEAD
        echo "<a href='index.php'>Back to Menu</a>";
=======
        echo "<a href='order_index.php'>Back to Menu</a>";
>>>>>>> 6280fc8d0a0b58f6b6169935d5fab08034176079
    } else {
        echo "Error: " . $conn->error;
    }
}
?>