<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST['item_id'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $table_number = $_POST['table_number'];
    
    
    $total = $price * $qty;
    $status = 'pending'; 

   
    $sql_order = "INSERT INTO orders (table_number, total_price, status) VALUES ('$table_number', '$total', '$status')";
    
    if ($conn->query($sql_order)) {
       
        $order_id = $conn->insert_id;

        
        $sql_items = "INSERT INTO order_items (order_id, item_id, quantity) VALUES ('$order_id', '$item_id', '$qty')";
        $conn->query($sql_items);

        
        $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");
        
       
        echo "<script>
                alert('Order Success! Total: $$total');
                window.location.href = 'order_index.php';
              </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>