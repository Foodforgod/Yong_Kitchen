<?php
session_start();
include 'db.php';


if (!isset($_SESSION['cashier_cart'])) { $_SESSION['cashier_cart'] = []; }


if (isset($_POST['add_to_order'])) {
    $id = $_POST['item_id'];
    $name = $_POST['item_name'];
    $price = $_POST['item_price'];
    
    $found = false;
    foreach ($_SESSION['cashier_cart'] as &$item) {
        if ($item['id'] == $id) {
            $item['qty']++;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cashier_cart'][] = ['id' => $id, 'name' => $name, 'price' => $price, 'qty' => 1];
    }
}


if (isset($_GET['clear'])) { unset($_SESSION['cashier_cart']); header("Location: cashier.php"); exit(); }


if (isset($_POST['checkout_order'])) {
    $total = $_POST['grand_total'];
    
    
    $conn->query("INSERT INTO orders (table_number, total_price, status) VALUES ('Counter', '$total', 'completed')");
    $order_id = $conn->insert_id;

    foreach ($_SESSION['cashier_cart'] as $item) {
        $item_id = $item['id']; $qty = $item['qty'];
        $conn->query("INSERT INTO order_items (order_id, item_id, quantity) VALUES ($order_id, $item_id, $qty)");
        $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");
    }
    
    unset($_SESSION['cashier_cart']);
    echo "<script>alert('Success! Order #$order_id processed.'); window.location='cashier.php';</script>";
}

$items = $conn->query("SELECT * FROM items WHERE stock > 0 ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Counter | RMS</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --primary: #6366f1; --bg: #f3f4f6; --dark: #111827; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; overflow: hidden; }
        
       
        .sidebar-nav { width: 80px; background: var(--dark); display: flex; flex-direction: column; align-items: center; padding: 20px 0; }
        .sidebar-nav a { color: #4b5563; font-size: 1.5rem; margin-bottom: 30px; transition: 0.3s; }
        .sidebar-nav a:hover { color: white; }
        .sidebar-nav a.active { color: var(--primary); }

        
        .menu-section { flex: 1; padding: 30px; overflow-y: auto; }
        .back-link { text-decoration: none; color: var(--primary); font-weight: bold; font-size: 0.85rem; display: block; margin-bottom: 10px; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
        .item-btn { background: white; border: 2px solid transparent; border-radius: 15px; padding: 12px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 100%; text-align: center; }
        .item-btn:hover { border-color: var(--primary); transform: translateY(-3px); }
        .item-btn img { width: 100%; height: 110px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; }
        .item-btn h4 { margin: 5px 0; font-size: 0.9rem; color: #1f2937; }
        .item-btn b { color: var(--primary); }

       
        .billing-panel { width: 400px; background: white; border-left: 1px solid #e5e7eb; display: flex; flex-direction: column; padding: 25px; }
        .receipt-header { border-bottom: 2px dashed #d1d5db; padding-bottom: 15px; margin-bottom: 20px; text-align: center; }
        .order-scroll { flex: 1; overflow-y: auto; }
        .order-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem; }
        
        .checkout-box { border-top: 2px solid #f3f4f6; padding-top: 20px; }
        .total-row { display: flex; justify-content: space-between; font-size: 1.4rem; font-weight: 800; margin-bottom: 20px; }
        
        .btn { width: 100%; padding: 16px; border: none; border-radius: 12px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-bottom: 10px; }
        .btn-pay { background: #10b981; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); }
        .btn-pay:disabled { background: #d1d5db; box-shadow: none; cursor: not-allowed; }
        .btn-clear { background: #fef2f2; color: #ef4444; text-decoration: none; text-align: center; display: block; }
    </style>
</head>
<body>

    <div class="sidebar-nav">
        <a href="admin.php" title="Admin Dashboard"><i class="fas fa-chart-line"></i></a>
        <a href="cashier.php" class="active" title="Cashier Counter"><i class="fas fa-cash-register"></i></a>
        <a href="kitchen.php" title="Kitchen View"><i class="fas fa-fire-burner"></i></a>
        <a href="index.php" target="_blank" title="Customer View"><i class="fas fa-eye"></i></a>
    </div>

    <div class="menu-section">
        <a href="admin.php" class="back-link"><i class="fas fa-arrow-left"></i> BACK TO DASHBOARD</a>
        <h1 style="margin-top: 0;">Cashier Counter</h1>

        <div class="menu-grid">
            <?php while($row = $items->fetch_assoc()): ?>
                <form method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="item_name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="item_price" value="<?php echo $row['price']; ?>">
                    <button type="submit" name="add_to_order" class="item-btn">
                        <img src="<?php echo $row['image_path']; ?>" onerror="this.src='https://placehold.co'">
                        <h4><?php echo $row['name']; ?></h4>
                        <b>$<?php echo number_format($row['price'], 2); ?></b>
                    </button>
                </form>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="billing-panel">
        <div class="receipt-header">
            <h3 style="margin: 0;">Current Order</h3>
            <small style="color: #6b7280;"><?php echo date('l, j F Y'); ?></small>
        </div>

        <div class="order-scroll">
            <?php 
            $grand_total = 0;
            if(!empty($_SESSION['cashier_cart'])): 
                foreach($_SESSION['cashier_cart'] as $item): 
                    $subtotal = $item['qty'] * $item['price'];
                    $grand_total += $subtotal;
            ?>
                <div class="order-row">
                    <span><?php echo $item['qty']; ?>x <?php echo $item['name']; ?></span>
                    <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                </div>
            <?php endforeach; else: ?>
                <div style="text-align: center; color: #9ca3af; margin-top: 50px;">
                    <i class="fas fa-receipt fa-3x" style="margin-bottom: 10px;"></i>
                    <p>Order is empty</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="checkout-box">
            <div class="total-row">
                <span>Total</span>
                <span>$<?php echo number_format($grand_total, 2); ?></span>
            </div>

            <form method="POST">
                <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">
                <button type="submit" name="checkout_order" class="btn btn-pay" <?php echo empty($_SESSION['cashier_cart']) ? 'disabled' : ''; ?>>
                    <i class="fas fa-check"></i> PROCESS PAYMENT
                </button>
            </form>
            
            <a href="?clear=1" class="btn btn-clear">CLEAR ORDER</a>
        </div>
    </div>

</body>
</html>
