<?php
session_start();
include 'db.php'; // 确保 db.php 文件就在同一个文件夹下

// 1. 处理收银员手动点击添加的逻辑 (保留原有的 Session 功能)
if (isset($_POST['add_to_order'])) {
    $id = $_POST['item_id'];
    $name = $_POST['item_name'];
    $price = $_POST['item_price'];
    
    $found = false;
    if (!isset($_SESSION['cashier_cart'])) { $_SESSION['cashier_cart'] = []; }
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

// 2. 清除当前选中的订单
if (isset($_GET['clear'])) { 
    unset($_SESSION['cashier_cart']); 
    header("Location: cashier.php"); 
    exit(); 
}

// 3. 结账逻辑
if (isset($_POST['checkout_order'])) {
    $total = $_POST['grand_total'];
    // 插入订单
    $conn->query("INSERT INTO orders (table_number, total_price, status) VALUES ('Counter', '$total', 'completed')");
    $order_id = $conn->insert_id;

    if(isset($_SESSION['cashier_cart'])){
        foreach ($_SESSION['cashier_cart'] as $item) {
            $item_id = $item['id']; $qty = $item['qty'];
            $conn->query("INSERT INTO order_items (order_id, item_id, quantity) VALUES ($order_id, $item_id, $qty)");
            $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");
        }
    }
    unset($_SESSION['cashier_cart']);
    echo "<script>alert('结账成功！订单号: #$order_id'); window.location='cashier.php';</script>";
}

// 获取菜单列表
$items = $conn->query("SELECT * FROM items WHERE stock > 0 ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Cashier Counter | Yong Kitchen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --bg: #f3f4f6; --dark: #111827; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; overflow: hidden; }
        
        /* 侧边导航 */
        .sidebar { width: 80px; background: var(--dark); display: flex; flex-direction: column; align-items: center; padding: 20px 0; }
        .sidebar a { color: #4b5563; font-size: 1.5rem; margin-bottom: 30px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: white; }

        /* 左侧菜单区 */
        .menu-section { flex: 1; padding: 30px; overflow-y: auto; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
        .item-btn { background: white; border: none; border-radius: 15px; padding: 12px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 100%; }
        .item-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 12px rgba(0,0,0,0.1); }
        .item-btn img { width: 100%; height: 100px; object-fit: cover; border-radius: 10px; margin-bottom: 8px; }
        .item-btn h4 { margin: 5px 0; font-size: 0.9rem; }

        /* 右侧收银面板 */
        .billing-panel { width: 400px; background: white; border-left: 1px solid #e5e7eb; display: flex; flex-direction: column; padding: 25px; }
        .receipt-header { border-bottom: 2px dashed #d1d5db; padding-bottom: 15px; margin-bottom: 20px; text-align: center; }
        .order-scroll { flex: 1; overflow-y: auto; }
        .order-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 5px; }
        
        .total-box { border-top: 2px solid #f3f4f6; padding-top: 20px; }
        .total-row { display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 800; margin-bottom: 20px; }
        
        .btn-pay { background: #10b981; color: white; border: none; width: 100%; padding: 16px; border-radius: 12px; font-weight: bold; cursor: pointer; font-size: 1rem; }
        .btn-clear { display: block; text-align: center; color: #ef4444; margin-top: 15px; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="admin.php"><i class="fas fa-home"></i></a>
        <a href="cashier.php" class="active"><i class="fas fa-cash-register"></i></a>
        <a href="order_index.php" target="_blank"><i class="fas fa-eye"></i></a>
    </div>

    <div class="menu-section">
        <h1>Cashier Counter</h1>
        <div class="menu-grid">
            <?php while($row = $items->fetch_assoc()): ?>
                <form method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="item_name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="item_price" value="<?php echo $row['price']; ?>">
                    <button type="submit" name="add_to_order" class="item-btn">
                        <?php 
                        $img = $row['image_path'];
                        $src = (strpos($img, 'http') === 0) ? $img : "uploads/" . $img;
                        ?>
                        <img src="<?php echo $src; ?>" onerror="this.src='https://placehold.co/100x100?text=Food'">
                        <h4><?php echo $row['name']; ?></h4>
                        <b style="color:var(--primary)">$<?php echo number_format($row['price'], 2); ?></b>
                    </button>
                </form>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="billing-panel">
        <div class="receipt-header">
            <h3>Current Order</h3>
            <small><?php echo date('Y-m-d H:i'); ?></small>
        </div>

        <div class="order-scroll">
            <?php 
            $grand_total = 0;
            // 优先检查数据库里是否有待结账的客户订单
            $db_order = $conn->query("SELECT * FROM orders WHERE status = 'pending' ORDER BY id DESC LIMIT 1");
            
            if (!empty($_SESSION['cashier_cart'])) {
                // 显示收银员手动点击的东西
                foreach($_SESSION['cashier_cart'] as $item) {
                    $subtotal = $item['qty'] * $item['price'];
                    $grand_total += $subtotal;
                    echo "<div class='order-row'><span>{$item['qty']}x {$item['name']}</span><strong>$".number_format($subtotal, 2)."</strong></div>";
                }
            } elseif ($db_order->num_rows > 0) {
                // 如果 Session 是空的，自动抓取数据库里最新的客户下单
                $order_info = $db_order->fetch_assoc();
                $o_id = $order_info['id'];
                $grand_total = $order_info['total_price'];
                echo "<p style='color:var(--primary); font-size:0.8rem;'>客户订单: #$o_id (桌号: {$order_info['table_number']})</p>";
                
                $order_items = $conn->query("SELECT oi.*, i.name FROM order_items oi JOIN items i ON oi.item_id = i.id WHERE oi.order_id = $o_id");
                while($oi = $order_items->fetch_assoc()) {
                    echo "<div class='order-row'><span>{$oi['quantity']}x {$oi['name']}</span></div>";
                }
            } else {
                echo "<div style='text-align:center; margin-top:50px; color:#9ca3af;'>等待下单...</div>";
            }
            ?>
        </div>

        <div class="total-box">
            <div class="total-row">
                <span>Total</span>
                <span>$<?php echo number_format($grand_total, 2); ?></span>
            </div>
            <form method="POST">
                <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">
                <button type="submit" name="checkout_order" class="btn-pay" <?php echo ($grand_total <= 0) ? 'disabled' : ''; ?>>
                    PROCESS PAYMENT
                </button>
            </form>
            <a href="?clear=1" class="btn-clear">CLEAR ORDER</a>
        </div>
    </div>
</body>
</html>