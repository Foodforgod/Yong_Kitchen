<?php
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Customer Menu | Order Page</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; padding: 20px; color: #1e293b; }
        .menu-grid { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; width: 200px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); text-align: center; }
        .price { color: #2563eb; font-weight: bold; font-size: 1.2rem; margin: 10px 0; }
        .stock { font-size: 0.85rem; color: #64748b; margin-bottom: 15px; }
        .btn-order { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: bold; }
        .btn-order:hover { background: #1d4ed8; }
        nav { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <nav>
        <strong>Restaurant System</strong> | 
        <a href="admin.php" style="text-decoration: none; color: #2563eb;">Enter Admin Panel</a>
    </nav>

    <h1 style="text-align: center;">menu</h1>

    <div class="menu-grid">
        <?php
     
        $result = $conn->query("SELECT * FROM items WHERE stock > 0");
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()): 
        ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                <p class="stock">in stock: <?php echo $row['stock']; ?> units</p>

               
                <form action="place_order.php" method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                    <input type="hidden" name="table_number" value="T1"> 
                    quantity: <input type="number" name="qty" value="1" min="1" max="<?php echo $row['stock']; ?>" required style="width: 50px; padding: 5px; margin-bottom: 10px;">
                    <button type="submit" class="btn-order">Order Now</button>
                </form>
            </div>
        <?php 
            endwhile; 
        } else {
            echo "<p>暂时没有食物供应，请在 Admin 添加菜单。</p>";
        }
        ?>
    </div>

</body>
</html>