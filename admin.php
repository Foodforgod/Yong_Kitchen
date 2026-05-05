<?php
include 'db.php';


if (isset($_POST['add_item'])) {
    $stmt = $conn->prepare("INSERT INTO items (name, description, price, category, stock, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $_POST['item_name'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['stock'], $_POST['item_image_url']);
    $stmt->execute();
    header("Location: admin.php?success=1");
    exit();
}


if (isset($_POST['update_item'])) {
    $stmt = $conn->prepare("UPDATE items SET name=?, description=?, price=?, category=?, stock=?, image_path=? WHERE id=?");
    $stmt->bind_param("ssdsisi", $_POST['item_name'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['stock'], $_POST['item_image_url'], $_POST['item_id']);
    $stmt->execute();
    header("Location: admin.php?updated=1");
    exit();
}


if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: admin.php?deleted=1");
    exit();
}


if (isset($_POST['clear_history'])) {
   
    $conn->query("DELETE FROM order_items"); 
    $conn->query("DELETE FROM orders"); 
    header("Location: admin.php?history_cleared=1");
    exit();
}


$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_item = $stmt->get_result()->fetch_assoc();
}


$rev_res = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'");
$revenue = $rev_res->fetch_assoc()['total'] ?? 0;


$history_query = "SELECT o.id, o.total_price, o.table_number, GROUP_CONCAT(CONCAT(oi.quantity, 'x ', i.name) SEPARATOR ', ') as summary 
                 FROM orders o 
                 JOIN order_items oi ON o.id = oi.order_id 
                 JOIN items i ON oi.item_id = i.id 
                 WHERE o.status = 'completed' 
                 GROUP BY o.id ORDER BY o.id DESC LIMIT 15";
$history = $conn->query($history_query);


$search = $_GET['search'] ?? '';
$query = "SELECT * FROM items" . (!empty($search) ? " WHERE name LIKE ?" : "") . " ORDER BY id DESC";
$stmt = $conn->prepare($query);
if(!empty($search)) {
    $term = "%$search%";
    $stmt->bind_param("s", $term);
}
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Yong Kitchen</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --primary: #2563eb; --danger: #ef4444; --success: #10b981; --bg: #f8fafc; --dark: #1e293b; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 20px; box-sizing: border-box; }
        .sidebar h2 { color: #38bdf8; font-size: 1.2rem; margin-bottom: 30px; text-align: center; border-bottom: 1px solid #334155; padding-bottom: 15px; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 10px; }
        .sidebar a:hover, .sidebar a.active { background: var(--primary); color: white; }
        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .revenue-card { background: white; padding: 20px; border-radius: 15px; border-left: 6px solid var(--success); box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 250px; margin-bottom: 30px; }
        .grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn { border: none; width: 100%; padding: 14px; border-radius: 8px; cursor: pointer; font-weight: bold; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; background: #f1f5f9; color: #475569; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .item-img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
        .status-paid { color: var(--success); font-weight: bold; background: #dcfce7; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>RMS ADMIN</h2>
        <a href="admin.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="cashier.php"><i class="fas fa-cash-register"></i> Cashier</a>
        <a href="kitchen.php"><i class="fas fa-fire-burner"></i> Kitchen</a>
        <a href="order_index.php" style="background:#475569; margin-top:20px;"><i class="fas fa-plus-circle"></i> Create New Order</a>
    </div>

    <div class="main">
        <div class="revenue-card">
            <small>TOTAL PAID REVENUE</small>
            <h2 style="margin:5px 0;">$<?php echo number_format($revenue, 2); ?></h2>
        </div>

        <div class="grid">
         
            <div class="card">
                <h3><i class="fas <?php echo $edit_item ? 'fa-edit' : 'fa-plus-circle'; ?>"></i> <?php echo $edit_item ? 'Edit' : 'Add'; ?> Item</h3>
                <form method="POST">
                    <?php if($edit_item): ?> <input type="hidden" name="item_id" value="<?php echo $edit_item['id']; ?>"> <?php endif; ?>
                    <input type="text" name="item_name" placeholder="Name" value="<?php echo $edit_item['name'] ?? ''; ?>" required>
                    <textarea name="description" placeholder="Description"><?php echo $edit_item['description'] ?? ''; ?></textarea>
                    <input type="number" step="0.01" name="price" placeholder="Price" value="<?php echo $edit_item['price'] ?? ''; ?>" required>
                    <select name="category">
                        <option value="Food" <?php echo ($edit_item && $edit_item['category']=='Food')?'selected':''; ?>>Food</option>
                        <option value="Drinks" <?php echo ($edit_item && $edit_item['category']=='Drinks')?'selected':''; ?>>Drinks</option>
                    </select>
                    <input type="number" name="stock" placeholder="Stock" value="<?php echo $edit_item['stock'] ?? ''; ?>" required>
                    <input type="text" name="item_image_url" placeholder="Image URL" value="<?php echo $edit_item['image_path'] ?? ''; ?>">
                    <button type="submit" name="<?php echo $edit_item ? 'update_item' : 'add_item'; ?>" class="btn" style="background:<?php echo $edit_item ? 'var(--success)' : 'var(--primary)'; ?>">
                        <?php echo $edit_item ? 'Update Item' : 'Add to Menu'; ?>
                    </button>
                    <?php if($edit_item): ?> <a href="admin.php" style="display:block; text-align:center; margin-top:10px; color:gray; text-decoration:none;">Cancel</a> <?php endif; ?>
                </form>
            </div>

           
            <div class="card">
                <form method="GET" style="display:flex; gap:10px; margin-bottom:20px;">
                    <input type="text" name="search" placeholder="Search menu..." value="<?php echo htmlspecialchars($search); ?>" style="margin:0;">
                    <button type="submit" class="btn" style="width:100px; background:var(--dark)">Search</button>
                </form>
                <table>
                    <thead><tr><th>Image</th><th>Details</th><th>Stock</th><th>Price</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?php echo $row['image_path']; ?>" class="item-img" onerror="this.src='https://placehold.co'"></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>" style="color:var(--primary); margin-right:15px;"><i class="fas fa-edit"></i></a>
                                <a href="?delete=<?php echo $row['id']; ?>" style="color:var(--danger);" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

      
        <div class="card" style="margin-top:30px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3><i class="fas fa-history"></i> Sales History</h3>
                
                
                <form method="POST" onsubmit="return confirm('WARNING: This will permanently delete all sales history and reset the revenue. Continue?');">
                    <button type="submit" name="clear_history" class="btn" style="background:var(--danger); padding: 8px 15px; font-size: 0.8rem; width: auto;">
                        <i class="fas fa-trash-alt"></i> CLEAR ALL HISTORY
                    </button>
                </form>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Table</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($history && $history->num_rows > 0): ?>
                        <?php while($h = $history->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $h['id']; ?></td>
                            <td><strong>Table <?php echo htmlspecialchars($h['table_number']); ?></strong></td>
                            <td style="font-size:0.85rem; color:#475569;"><?php echo htmlspecialchars($h['summary']); ?></td>
                            <td><strong>$<?php echo number_format($h['total_price'], 2); ?></strong></td>
                            <td><span class="status-paid">PAID</span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding:20px; color:#94a3b8;">No history records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
