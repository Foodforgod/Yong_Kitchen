<?php
session_start();
include 'db.php'; 


if (isset($_POST['process_payment'])) {
    $order_id = intval($_POST['order_id']);
    $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND status = 'done'");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Order #$order_id Paid!'); window.location='cashier.php';</script>";
    }
}


$query = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.quantity, 'x ', i.name) SEPARATOR ', ') as item_details 
          FROM orders o 
          JOIN order_items oi ON o.id = oi.order_id 
          JOIN items i ON oi.item_id = i.id 
          WHERE o.status = 'done' 
          GROUP BY o.id ORDER BY o.id ASC";
$ready_orders = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Counter</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --primary: #6366f1; --success: #10b981; --dark: #111827; --bg: #f3f4f6; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; }
        
        
        .sidebar { width: 80px; background: var(--dark); display: flex; flex-direction: column; align-items: center; padding: 20px 0; }
        .sidebar a { color: #4b5563; font-size: 1.5rem; margin-bottom: 30px; transition: 0.3s; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { color: white; }

        .main-content { flex: 1; padding: 40px; overflow-y: auto; }
        
       
        .btn-back { 
            display: inline-flex; align-items: center; gap: 8px;
            color: #4b5563; text-decoration: none; font-weight: bold; 
            margin-bottom: 20px; transition: 0.2s;
        }
        .btn-back:hover { color: var(--primary); }

        .order-card { 
            background: white; border-radius: 15px; padding: 25px; margin-bottom: 20px; 
            display: flex; justify-content: space-between; align-items: center; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 8px solid var(--success);
        }
        .btn-pay { background: var(--success); color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

    <nav class="sidebar">
        
        <a href="admin.php" title="Back to Admin"><i class="fas fa-arrow-left"></i></a>
        <a href="cashier.php" class="active"><i class="fas fa-cash-register"></i></a>
        <a href="kitchen.php"><i class="fas fa-fire-burner"></i></a>
    </nav>

    <div class="main-content">
        
        <a href="admin.php" class="btn-back"><i class="fas fa-chevron-left"></i> Back to Dashboard</a>

        <h1>Ready for Payment</h1>

        <?php if ($ready_orders->num_rows > 0): ?>
            <?php while($row = $ready_orders->fetch_assoc()): ?>
                <div class="order-card">
                    <div>
                        <h3>Order #<?php echo $row['id']; ?> — Table <?php echo $row['table_number']; ?></h3>
                        <p><?php echo $row['item_details']; ?></p>
                    </div>
                    <div style="text-align:right;">
                        <h2 style="margin:0;">$<?php echo number_format($row['total_price'], 2); ?></h2>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="process_payment" class="btn-pay">COMPLETE PAYMENT</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align:center; margin-top:100px; color:#9ca3af;">
                <i class="fas fa-clock" style="font-size: 3rem;"></i>
                <h2>No orders ready for payment yet.</h2>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
