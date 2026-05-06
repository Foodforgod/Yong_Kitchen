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


$sql = "SELECT o.*, 
               (SELECT GROUP_CONCAT(CONCAT(total_qty, 'x ', i.name) SEPARATOR ', ')
                FROM (
                    SELECT order_id, item_id, SUM(quantity) as total_qty 
                    FROM order_items 
                    GROUP BY order_id, item_id
                ) as summarized_items
                JOIN items i ON summarized_items.item_id = i.id
                WHERE summarized_items.order_id = o.id
               ) as details
        FROM orders o 
        WHERE o.status = '$status_filter' 
        ORDER BY o.id DESC";

$orders = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cashier Station</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .order-time { font-size: 0.9rem; color: #9ca3af; margin-left: 15px; font-weight: normal; }
        .food-details { color: #4b5563; font-size: 1rem; margin-top: 8px; display: block; }
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
                
                <div style="flex-grow: 1; padding-right: 20px;">
                    <strong style="font-size: 1.3rem; color: #111827;">
                        Table <?php echo $row['table_number']; ?>
                    </strong>
                    
                    <span class="order-time">
                        <i class="far fa-clock"></i> 
                        <?php echo isset($row['created_at']) ? $row['created_at'] : 'Unknown'; ?>
                    </span>
                    <br>

                   
                    <span class="food-details">
                        <i class="fas fa-utensils" style="color: #9ca3af; font-size: 0.8rem;"></i> 
                        <?php echo htmlspecialchars($row['details']); ?>
                    </span>
                </div>

                
                <div style="text-align: right; min-width: 200px;">
                    <span style="font-size: 1.5rem; font-weight: bold; margin-right: 20px; color: #dc2626;">
                        $<?php echo number_format($row['total_price'], 2); ?>
                    </span>
                    
                    <?php if($view == 'unpaid'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="pay" class="pay-btn">MARK AS PAID</button>
                    </form>
                    <?php else: ?>
                        <span style="color: #10b981; font-weight: bold; font-size: 1.1rem;">
                            <i class="fas fa-check-circle"></i> PAID
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="margin-top: 30px; color: #9ca3af; font-size: 1.2rem;">
                <i class="fas fa-inbox"></i> No orders found in this category.
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
