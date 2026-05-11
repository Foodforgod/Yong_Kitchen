<?php
include 'db.php';
$pending_orders = $conn->query("SELECT * FROM orders WHERE status = 'pending' ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kitchen Display | RMS</title>
    <meta http-equiv="refresh" content="10">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="kitchen-body">

    <div class="kitchen-header">
        <h1><i class="fas fa-fire-alt"></i> KITCHEN QUEUE</h1>
        <div style="display:flex; align-items:center; gap:20px;">
            <span class="badge bg-pending"><?php echo $pending_orders->num_rows; ?> PENDING</span>
            <a href="admin.php" class="btn btn-primary">Admin Panel</a>
        </div>
    </div>

    <div class="kitchen-grid">
        <?php if($pending_orders->num_rows > 0): ?>
            <?php while($o = $pending_orders->fetch_assoc()): ?>
            <div class="order-card">
                <div class="order-card-header">
                    <span>TABLE <?php echo $o['table_number']; ?></span>
                    <small>#<?php echo $o['id']; ?></small>
                </div>
                
                <div class="order-items-list">
                    <ul>
                        <?php
                        $oid = $o['id'];
                        $items = $conn->query("SELECT oi.quantity, oi.remarks, i.name 
                                               FROM order_items oi 
                                               JOIN items i ON oi.item_id = i.id 
                                               WHERE oi.order_id = $oid");
                        
                        while($i = $items->fetch_assoc()):
                        ?>
                            <li style="flex-direction: column; align-items: flex-start; gap: 5px;">
                                <div>
                                    <b><?php echo $i['quantity']; ?>x</b> <?php echo htmlspecialchars($i['name']); ?>
                                </div>
                                <?php if(!empty($i['remarks'])): ?>
                                    <div style="font-size: 0.85rem; color: #fbbf24; background: rgba(251, 191, 36, 0.1); padding: 5px 8px; border-radius: 4px; border-left: 3px solid #fbbf24; margin-top: 4px; width: 100%;">
                                        <i class="fas fa-sticky-note"></i> Note: <?php echo htmlspecialchars($i['remarks']); ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <form action="update_status.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                    <button type="submit" name="mark_ready" class="btn-ready">
                        <i class="fas fa-check"></i> READY
                    </button>
                </form>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <h2 style="color: #64748b;">No pending orders!</h2>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>