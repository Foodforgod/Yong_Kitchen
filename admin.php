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



$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_item = $stmt->get_result()->fetch_assoc();
}


$rev_query = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'");
$rev = $rev_query->fetch_assoc()['total'] ?? 0;


$items = $conn->query("SELECT * FROM items ORDER BY id DESC");


$history = $conn->query("SELECT o.*, GROUP_CONCAT(i.name SEPARATOR ', ') as item_names 
                        FROM orders o 
                        JOIN order_items oi ON o.id = oi.order_id 
                        JOIN items i ON oi.item_id = i.id 
                        WHERE o.status IN ('ready', 'completed') 
                        GROUP BY o.id ORDER BY o.id DESC LIMIT 15");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | RMS</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --primary: #2563eb; --danger: #ef4444; --success: #10b981; --dark: #1e293b; --bg: #f8fafc; }
        body { font-family: sans-serif; background: var(--bg); margin: 0; display: flex; color: #334155; }
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 20px; box-sizing: border-box; }
        .sidebar h2 { color: #38bdf8; text-align: center; margin-bottom: 30px; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 10px; }
        .sidebar a.active { background: var(--primary); color: white; }
        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .rev-card { background: white; padding: 20px; border-radius: 15px; border-left: 6px solid var(--success); width: 250px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn { border: none; width: 100%; padding: 12px; border-radius: 6px; cursor: pointer; color: white; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; background: #f1f5f9; font-size: 0.8rem; color: #64748b; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; }
        .bg-paid { background: #dcfce7; color: #166534; }
        .bg-unpaid { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>RMS Admin</h2>
        <a href="admin.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="cashier.php"><i class="fas fa-cash-register"></i> Cashier</a>
        <a href="kitchen.php"><i class="fas fa-utensils"></i> Kitchen</a>
    </div>

    <div class="main">
        <div class="rev-card">
            <small style="font-weight:bold; color:#64748b;">TOTAL REVENUE (PAID)</small>
            <h2 style="margin:5px 0; color:var(--success);">$<?php echo number_format($rev, 2); ?></h2>
        </div>

        <div class="grid">
            
            <div class="card">
                <h3><?php echo $edit_item ? 'Edit Item' : 'Add New Item'; ?></h3>
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
                    <button type="submit" name="<?php echo $edit_item ? 'update_item' : 'add_item'; ?>" class="btn" style="background:<?php echo $edit_item ? 'var(--success)' : 'var(--primary)'; ?>">Save</button>
                    <?php if($edit_item): ?> <a href="admin.php" style="display:block; text-align:center; margin-top:10px; color:gray; text-decoration:none; font-size:0.8rem;">Cancel</a> <?php endif; ?>
                </form>
            </div>

           
            <div class="card">
                <h3>Current Menu</h3>
                <table>
                    <thead><tr><th>Image</th><th>Name</th><th>Stock</th><th>Price</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?php echo $row['image_path']; ?>" style="width:40px; height:40px; border-radius:4px;" onerror="this.src='https://placehold.co'"></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>" style="color:var(--primary); font-weight:bold; text-decoration:none; margin-right:10px;">EDIT</a>
                                <a href="?delete=<?php echo $row['id']; ?>" style="color:var(--danger); font-weight:bold; text-decoration:none;" onclick="return confirm('Delete?')">DELETE</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="card">
            <h3>Recent Activity & Tracking</h3>
            <table>
                <thead>
                    <tr><th>Order ID</th><th>Table</th><th>Items</th><th>Total</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php while($h = $history->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $h['id']; ?></td>
                        <td><strong>Table <?php echo htmlspecialchars($h['table_number']); ?></strong></td>
                        <td><small><?php echo htmlspecialchars($h['item_names']); ?></small></td>
                        <td>$<?php echo number_format($h['total_price'], 2); ?></td>
                        <td>
                            <?php if($h['status'] == 'completed'): ?>
                                <span class="badge bg-paid">PAID</span>
                            <?php else: ?>
                                <span class="badge bg-unpaid">UNPAID</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
