<?php
include 'db.php';

if (isset($_POST['pay'])) {
    $order_id = intval($_POST['order_id']);
  
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = $order_id");
    header("Location: cashier.php?view=unpaid");
    exit();
}

$view = $_GET['view'] ?? 'unpaid';

$status_filter = ($view == 'completed') ? 'completed' : 'ready';

$orders = $conn->query("SELECT o.*, GROUP_CONCAT(i.name SEPARATOR ', ') as details 
                        FROM orders o 
                        JOIN order_items oi ON o.id = oi.order_id 
                        JOIN items i ON oi.item_id = i.id 
                        WHERE o.status = '$status_filter' 
                        GROUP BY o.id ORDER BY o.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cashier Station</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        body { font-family: sans-serif; background: #f3f4f6; display: flex; margin: 0; }
        .sidebar { width: 260px; background: #111827; height: 100vh; padding: 20px; box-sizing: border-box; position: fixed; color: white; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 10px; }
        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .tabs { margin-bottom: 20px; }
        .tabs a { text-decoration: none; padding: 10px 20px; background: #e5e7eb; border-radius: 5px; margin-right: 10px; color: #4b5563; font-weight: bold; }
        .tabs a.active { background: #2563eb; color: white; }
        .row { background: white; padding: 20px; border-radius: 12px; margin-top: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .pay-btn { background: #10b981; color: white; border: none; padding: 10px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Cashier</h2>
        <a href="admin.php"><i class="fas fa-arrow-left"></i> Admin Dashboard</a>
        <a href="kitchen.php"><i class="fas fa-utensils"></i> Kitchen View</a>
    </div>
    <div class="main">
        <h1>Checkout</h1>
        <div class="tabs">
            <a href="?view=unpaid" class="<?php echo $view=='unpaid'?'active':''; ?>">UNPAID (FROM KITCHEN)</a>
            <a href="?view=completed" class="<?php echo $view=='completed'?'active':''; ?>">PAID HISTORY</a>
        </div>

        <?php if($orders->num_rows > 0): ?>
            <?php while($row = $orders->fetch_assoc()): ?>
            <div class="row">
                <div>
                    <strong style="font-size: 1.2rem;">Table <?php echo $row['table_number']; ?></strong><br>
                    <small style="color: #6b7280;"><?php echo htmlspecialchars($row['details']); ?></small>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 1.3rem; font-weight: bold; margin-right: 20px;">$<?php echo number_format($row['total_price'], 2); ?></span>
                    <?php if($view == 'unpaid'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="pay" class="pay-btn">MARK AS PAID</button>
                    </form>
                    <?php else: ?>
                        <span style="color: #10b981; font-weight: bold;"><i class="fas fa-check-circle"></i> PAID</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="margin-top: 30px; color: #9ca3af;">No orders found in this category.</p>
        <?php endif; ?>
    </div>
</body>
</html>
