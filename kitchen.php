<?php
include 'db.php';

if (isset($_POST['mark_done'])) {
    $order_id = intval($_POST['order_id']);
    
    $conn->query("UPDATE orders SET status = 'ready' WHERE id = $order_id");
    header("Location: kitchen.php");
    exit();
}


$orders = $conn->query("SELECT o.*, GROUP_CONCAT(i.name SEPARATOR '<br>') as items 
                        FROM orders o 
                        JOIN order_items oi ON o.id = oi.order_id 
                        JOIN items i ON oi.item_id = i.id 
                        WHERE o.status = 'pending' 
                        GROUP BY o.id ORDER BY o.id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kitchen Queue</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        body { font-family: sans-serif; background: #0f172a; color: white; display: flex; margin: 0; }
        .sidebar { width: 260px; background: #111827; height: 100vh; padding: 20px; box-sizing: border-box; position: fixed; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 10px; }
        .main { margin-left: 260px; padding: 40px; width: 100%; }
        .order-grid { display: flex; gap: 20px; flex-wrap: wrap; }
        .card { background: #1e293b; padding: 20px; border-radius: 12px; width: 280px; border-top: 5px solid #fb923c; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .btn { width: 100%; background: #10b981; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 6px; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Kitchen</h2>
        <a href="admin.php"><i class="fas fa-arrow-left"></i> Admin Panel</a>
        <a href="cashier.php"><i class="fas fa-cash-register"></i> Cashier View</a>
    </div>
    <div class="main">
        <h1>Orders to Cook</h1>
        <div class="order-grid">
            <?php if($orders->num_rows > 0): ?>
                <?php while($row = $orders->fetch_assoc()): ?>
                <div class="card">
                    <h3>Table <?php echo htmlspecialchars($row['table_number']); ?></h3>
                    <p style="font-size: 1.1rem; min-height: 60px;"><?php echo $row['items']; ?></p>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="mark_done" class="btn">SEND TO CASHIER</button>
                    </form>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #64748b;">No pending orders. Take a break!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

