<?php
session_start();
include 'db.php';


if (isset($_GET['remove'])) {
    $remove_key = $_GET['remove'];
    if (isset($_SESSION['customer_cart'][$remove_key])) {
        unset($_SESSION['customer_cart'][$remove_key]);
        
        $_SESSION['customer_cart'] = array_values($_SESSION['customer_cart']);
    }
    header("Location: cart_view.php");
    exit();
}


if (isset($_POST['confirm_order'])) {
    if (empty($_SESSION['customer_cart'])) {
        echo "<script>alert('购物车是空的！'); window.location='order_index.php';</script>";
        exit();
    }

    $table_no = $_POST['table_no'];
    $total = 0;
    foreach($_SESSION['customer_cart'] as $item) {
        $total += ($item['qty'] * $item['price']);
    }

    
    $sql_order = "INSERT INTO orders (table_number, total_price, status) VALUES ('$table_no', '$total', 'pending')";
    
    if ($conn->query($sql_order)) {
        $order_id = $conn->insert_id;

       
        foreach ($_SESSION['customer_cart'] as $item) {
            $item_id = $item['id'];
            $qty = $item['qty'];
        
            $remarks = isset($item['remarks']) ? $conn->real_escape_string($item['remarks']) : '';

            
            $sql_item = "INSERT INTO order_items (order_id, item_id, quantity, remarks) 
                         VALUES ($order_id, $item_id, $qty, '$remarks')";
            
            $conn->query($sql_item);
            
           
            $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");
        }

        
        unset($_SESSION['customer_cart']);
        echo "<script>alert('下单成功！订单号: #$order_id'); window.location='order_index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>my Cart | Yong Kitchen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; padding: 20px; color: #1e293b; }
        .cart-card { max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .item-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 15px 0; border-bottom: 1px solid #eee; }
        .item-details { flex-grow: 1; }
        .remarks-text { font-size: 0.85rem; color: #6366f1; font-style: italic; margin-top: 4px; display: block; }
        .total-section { margin-top: 20px; text-align: right; font-size: 1.5rem; font-weight: bold; color: #2563eb; }
        .btn-pay { background: #2563eb; color: white; border: none; width: 100%; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer; font-size: 1.1rem; margin-top: 20px; transition: 0.2s; }
        .btn-pay:hover { background: #1d4ed8; }
        .btn-back { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; }
        select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; appearance: none; background-color: #fff; }
    </style>
</head>
<body>

<div class="cart-card">
    <h2><i class="fas fa-shopping-cart"></i> Confirm Order</h2>
    <hr>

    <?php if(!empty($_SESSION['customer_cart'])): ?>
        <?php 
        $grand_total = 0;
        foreach($_SESSION['customer_cart'] as $key => $item): 
            $subtotal = $item['qty'] * $item['price'];
            $grand_total += $subtotal;
        ?>
        <div class="item-row">
            <div class="item-details">
                <strong><?php echo $item['qty']; ?>x <?php echo htmlspecialchars($item['name']); ?></strong><br>
                
              
                <?php if(!empty($item['remarks'])): ?>
                    <span class="remarks-text">Note: <?php echo htmlspecialchars($item['remarks']); ?></span>
                <?php endif; ?>
                
                <a href="?remove=<?php echo $key; ?>" style="color:#ef4444; font-size:0.8rem; text-decoration:none;"><i class="fas fa-trash"></i> Remove</a>
            </div>
            <span style="font-weight: bold;">$<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <?php endforeach; ?>

        <div class="total-section">TOTAL: $<?php echo number_format($grand_total, 2); ?></div>

        <form method="POST" style="margin-top: 20px;">
            <p><strong><i class="fas fa-chair"></i> Select Table Number:</strong></p>
            <select name="table_no" required>
                <option value="" disabled selected>-- Please Select --</option>
                <option value="T1">Table 1</option>
                <option value="T2">Table 2</option>
                <option value="T3">Table 3</option>
                <option value="T4">Table 4</option>
                <option value="T5">Table 5</option>
            </select>
            <button type="submit" name="confirm_order" class="btn-pay">Confirm Order (Send to Kitchen)</button>
        </form>

    <?php else: ?>
        <div style="text-align:center; padding:40px;">
            <i class="fas fa-box-open" style="font-size:3rem; color:#d1d5db;"></i>
            <p style="color:#9ca3af;">Your cart is empty</p>
        </div>
    <?php endif; ?>

    <a href="order_index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Menu</a>
</div>

</body>
</html>