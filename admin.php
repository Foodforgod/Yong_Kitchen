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
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>RMS Admin - Dashboard</title>
    <style>
        :root { 
            --primary: #2563eb; 
            --danger: #ef4444; 
            --success: #10b981; 
            --dark-blue: #1a233a; 
            --bg-light: #f4f7fe; 
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; }


        .sidebar { width: 260px; background: var(--dark-blue); color: white; padding: 40px 20px; position: fixed; height: 100vh; }
        .sidebar h2 { color: #4dabf7; margin-bottom: 40px; padding-left: 20px; font-size: 1.5rem; }
        .sidebar a { color: #a0aec0; text-decoration: none; display: block; padding: 15px 20px; border-radius: 10px; margin-bottom: 10px; transition: 0.3s; }
        .sidebar a.active, .sidebar a:hover { background: var(--primary); color: white; }
        .main-content { flex: 1; margin-left: 260px; padding: 40px; }
        
        .stat-card { background: white; padding: 25px; border-radius: 15px; width: 300px; border-left: 6px solid var(--success); box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .stat-label { color: #718096; font-size: 0.85rem; font-weight: bold; }
        .stat-value { color: var(--success); font-size: 2rem; margin-top: 10px; }

        .content-grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; margin-bottom: 30px; align-items: start; }
        
        .card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .card h3 { margin-bottom: 25px; color: #2d3748; }

        
        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; display: block; }
        .btn-save { background: var(--primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 10px; cursor: pointer; font-weight: bold; }
        .btn-save.update { background: var(--success); }

        
        .menu-table { width: 100%; border-collapse: collapse; }
        .menu-table th { text-align: left; color: #a0aec0; font-size: 0.85rem; padding: 12px; background: #f8fafc; }
        .menu-table td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .thumb { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; }
        
        .action-edit { color: var(--primary); text-decoration: none; font-weight: bold; margin-right: 15px; }
        .action-delete { color: var(--danger); text-decoration: none; font-weight: bold; }

        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
        .bg-paid { background: #dcfce7; color: #166534; }
        .bg-unpaid { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    
    <aside class="sidebar">
        <h2 class="logo">RMS Admin</h2>
        <nav class="menu">
            <a href="admin.php" class="active">Dashboard</a>
            <a href="cashier.php">Cashier</a>
            <a href="kitchen.php">Kitchen</a>
        </nav>
    </aside>

    <main class="main-content">
        
        <div class="top-stats">
            <div class="stat-card">
                <span class="stat-label">TOTAL REVENUE (PAID)</span>
                <h2 class="stat-value">$<?php echo number_format($rev, 2); ?></h2>
            </div>
        </div>

        
        <div class="content-grid">
         
            <section class="card">
                <h3><?php echo $edit_item ? 'Edit Item' : 'Add New Item'; ?></h3>
                <form method="POST">
                    <?php if($edit_item): ?> 
                        <input type="hidden" name="item_id" value="<?php echo $edit_item['id']; ?>"> 
                    <?php endif; ?>
                    
                    <input type="text" name="item_name" placeholder="Name" value="<?php echo $edit_item['name'] ?? ''; ?>" required>
                    <textarea name="description" placeholder="Description"><?php echo $edit_item['description'] ?? ''; ?></textarea>
                    <input type="number" step="0.01" name="price" placeholder="Price" value="<?php echo $edit_item['price'] ?? ''; ?>" required>
                    
                    <select name="category">
                        <option value="Food" <?php echo ($edit_item && $edit_item['category']=='Food')?'selected':''; ?>>Food</option>
                        <option value="Drinks" <?php echo ($edit_item && $edit_item['category']=='Drinks')?'selected':''; ?>>Drinks</option>
                    </select>
                    
                    <input type="number" name="stock" placeholder="Stock" value="<?php echo $edit_item['stock'] ?? ''; ?>" required>
                    <input type="text" name="item_image_url" placeholder="Image URL (e.g. burger.jpg)" value="<?php echo $edit_item['image_path'] ?? ''; ?>">
                    
                    <button type="submit" name="<?php echo $edit_item ? 'update_item' : 'add_item'; ?>" 
                            class="btn-save <?php echo $edit_item ? 'update' : ''; ?>">
                        <?php echo $edit_item ? 'Update Item' : 'Save'; ?>
                    </button>
                    
                    <?php if($edit_item): ?> 
                        <a href="admin.php" style="display:block; text-align:center; margin-top:15px; color:#a0aec0; text-decoration:none; font-size:0.9rem;">Cancel Edit</a> 
                    <?php endif; ?>
                </form>
            </section>

            
            <section class="card">
                <h3>Current Menu</h3>
                <table class="menu-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $row['image_path']; ?>" class="thumb" 
                                     onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                            </td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>" class="action-edit">EDIT</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="action-delete" onclick="return confirm('Confirm Delete?')">DELETE</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </div>

        
        <section class="card">
            <h3>Recent Activity & Tracking</h3>
            <table class="menu-table">
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
                                <span class="badge bg-unpaid">READY/UNPAID</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
