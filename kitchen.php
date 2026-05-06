<?php
include 'db.php';

// 只获取待处理 (pending) 的订单[cite: 8]
$sql_orders = "SELECT * FROM orders WHERE status = 'pending' ORDER BY id ASC";
$result_orders = $conn->query($sql_orders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kitchen Display System</title>
    <meta http-equiv="refresh" content="10"> <!-- 每10秒自动刷新[cite: 8] -->
    <!-- 引入 FontAwesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #1e1e1e; color: #fff; padding: 20px; margin: 0; }
        
        /* 新增：顶部导航栏样式 */
        .top-nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: #2d2d2d; 
            padding: 10px 20px; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .btn-back { 
            background: #4b5563; 
            color: white; 
            text-decoration: none; 
            padding: 8px 15px; 
            border-radius: 6px; 
            font-size: 0.9rem; 
            transition: 0.3s;
        }
        .btn-back:hover { background: #374151; }

        .order-card { background: #333; border: 2px solid #555; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        .order-header { display: flex; justify-content: space-between; border-bottom: 1px solid #555; padding-bottom: 10px; margin-bottom: 10px; }
        .table-no { font-size: 1.5rem; font-weight: bold; color: #facc15; }
        .btn-done { background: #22c55e; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 1.1rem; border-radius: 5px; transition: 0.2s; }
        .btn-done:hover { background: #16a34a; }
    </style>
</head>
<body>

    <!-- 新增：顶部控制区域 -->
    <div class="top-nav">
        <h1 style="margin: 0; font-size: 1.5rem;">👨‍🍳 Kitchen Display</h1>
        <!-- 返回 Admin Dashboard 的链接[cite: 7] -->
        <a href="admin.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Admin
        </a>
    </div>

    <?php if ($result_orders->num_rows > 0): ?>
        <?php while ($order = $result_orders->fetch_assoc()): ?>
            <div class="order-card">
                <div class="order-header">
                    <span class="table-no">Table: <?php echo htmlspecialchars($order['table_number']); ?></span>
                    <span>Order #<?php echo $order['id']; ?></span>
                </div>

                <ul style="list-style: none; padding: 0;">
                    <?php
                    $order_id = $order['id'];
                    $sql_items = "SELECT oi.quantity, oi.remarks, i.name 
                                  FROM order_items oi 
                                  JOIN items i ON oi.item_id = i.id 
                                  WHERE oi.order_id = $order_id";
                    $result_items = $conn->query($sql_items);
                    while ($item = $result_items->fetch_assoc()):
                    ?>
                        <li style="font-size: 1.2rem; margin-bottom: 8px;">
                            <strong><?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['name']); ?></strong>
                            <?php if (!empty($item['remarks'])): ?>
                                <small style="color: #fb7185; margin-left: 10px;">(<?php echo htmlspecialchars($item['remarks']); ?>)</small>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>

                <!-- 提交到更新状态的脚本 -->
                <form action="update_status.php" method="POST" style="text-align: right;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <button type="submit" name="mark_done" class="btn-done">Mark as Done</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; color: #888;">
            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 10px;"></i>
            <p style="font-size: 1.5rem;">No pending orders. 🎉</p>
        </div>
    <?php endif; ?>

</body>
</html>