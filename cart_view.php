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
        header("Location: order_index.php");
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
            
            $remarks = $conn->real_escape_string($item['remarks']); 
           
            $sql_items = "INSERT INTO order_items (order_id, item_id, quantity, remarks) 
                          VALUES ('$order_id', '$item_id', '$qty', '$remarks')";
            $conn->query($sql_items);

            $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");
        }

        unset($_SESSION['customer_cart']);
        echo "<script>
                alert('Order Sent! Your Order ID is #$order_id. Please wait while we prepare your food.');
                window.location.href = 'order_index.php';
              </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order | Checkout</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="customer-body" style="background:#f1f5f9;">

<div style="max-width: 600px; margin: 40px auto; padding: 0 20px;">
    <a href="order_index.php" style="text-decoration:none; color:var(--text-muted); font-weight:bold; display: inline-block; margin-bottom: 20px;">
        <i class="fas fa-chevron-left"></i> Back to Menu
    </a>

    <h1 style="margin-bottom: 20px; color: var(--dark);">Checkout</h1>

    <?php if(!empty($_SESSION['customer_cart'])): ?>
        
        <div class="card" style="padding:0; overflow:hidden; border-radius: 15px;">
            <?php 
            $grand_total = 0;
            foreach($_SESSION['customer_cart'] as $key => $item): 
                $subtotal = $item['qty'] * $item['price'];
                $grand_total += $subtotal;
            ?>
            <div style="padding:20px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background: #fff;">
                <div>
                    <div style="font-weight:bold; font-size: 1.1rem; color: var(--dark);">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </div>
                    <small style="color:var(--text-muted);">
                        Qty: <?php echo $item['qty']; ?> × $<?php echo number_format($item['price'], 2); ?>
                    </small>
                    
                    <?php if(!empty($item['remarks'])): ?>
                        <div style="font-size:0.8rem; color:var(--warning); margin-top:5px; background: #fffbeb; padding: 4px 8px; border-radius: 5px; border-left: 3px solid var(--warning);">
                            <i class="fas fa-sticky-note"></i> <b>Note:</b> <?php echo htmlspecialchars($item['remarks']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div style="text-align:right;">
                    <div style="font-weight:bold; color: var(--dark);">$<?php echo number_format($subtotal, 2); ?></div>
                    <a href="?remove=<?php echo $key; ?>" style="color:var(--danger); font-size:0.8rem; text-decoration:none; margin-top: 5px; display: block;">
                        <i class="fas fa-trash-alt"></i> Remove
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <div style="padding:25px; background:#f8fafc; display:flex; justify-content:space-between; align-items:center; border-top: 2px solid #e2e8f0;">
                <span style="font-size:1.2rem; font-weight:bold; color: var(--text-main);">Total Amount</span>
                <span style="font-size:1.8rem; font-weight:800; color:var(--primary);">$<?php echo number_format($grand_total, 2); ?></span>
            </div>
        </div>

        <div class="card" style="margin-top: 25px; border-radius: 15px;">
            <h3 style="margin-top: 0;"><i class="fas fa-chair"></i> Final Details</h3>
            <form method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600;">Which table are you at?</label>
                    <select name="table_no" required style="font-size:1.1rem; padding:15px; background: #fff; border: 2px solid var(--border);">
                        <option value="" disabled selected>-- Select Your Table --</option>
                        <?php 
                        
                        for($i=1; $i<=15; $i++): ?>
                            <option value="Table <?php echo $i; ?>">Table <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <button type="submit" name="confirm_order" class="btn btn-primary" style="width:100%; padding:20px; font-size:1.2rem; border-radius:12px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);">
                    <i class="fas fa-paper-plane"></i> CONFIRM & SEND ORDER
                </button>
            </form>
        </div>

    <?php else: ?>
        <div class="card" style="text-align:center; padding:60px; border-radius: 15px;">
            <i class="fas fa-shopping-basket" style="font-size:4rem; color:#cbd5e1; margin-bottom:20px;"></i>
            <h2 style="color: #64748b;">Your cart is empty</h2>
            <p style="color: #94a3b8; margin-bottom: 30px;">Looks like you haven't added any food yet.</p>
            <a href="order_index.php" class="btn btn-primary" style="padding: 15px 40px; text-decoration: none;">
                Browse Our Menu
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>