<?php
include 'db.php';


if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat = $_POST['category'];
    $stock = $_POST['stock'];

    $image_url = $_POST['item_image_url'];


    $sql = "INSERT INTO items (name, description, price, category, stock, image_path) 
            VALUES ('$name', '$desc', '$price', '$cat', '$stock', '$image_url')";
    $conn->query($sql);
    header("Location: admin.php?success=1");
    exit();
}


if (isset($_POST['update_item'])) {
    $id = $_POST['item_id'];
    $name = $_POST['item_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat = $_POST['category'];
    $stock = $_POST['stock'];
    $image_url = $_POST['item_image_url'];

    $conn->query("UPDATE items SET name='$name', description='$desc', price='$price', category='$cat', stock='$stock', image_path='$image_url' WHERE id=$id");
    header("Location: admin.php?updated=1");
    exit();
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $conn->query("DELETE FROM items WHERE id = $id");
    header("Location: admin.php?deleted=1");
    exit();
}


$edit_item = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM items WHERE id = $id");
    $edit_item = $res->fetch_assoc();
}

$revenue_res = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'");
$revenue = $revenue_res->fetch_assoc()['total'] ?? 0;

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM items" . (!empty($search) ? " WHERE name LIKE '%$search%'" : "") . " ORDER BY id DESC";
$items = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | RMS</title>
    <link rel="stylesheet" href="https://cloudflare.com">

    <style>
        :root { --primary: #2563eb; --danger: #ef4444; --warning: #f59e0b; --success: #10b981; --bg: #f8fafc; --dark: #1e293b; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 20px; box-sizing: border-box; }

        .sidebar h2 { color: #38bdf8; font-size: 1.2rem; margin-bottom: 30px; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 10px; }
        .sidebar a:hover, .sidebar a.active { background: #334155; color: white; }
        .sidebar a.active { background: var(--primary); }
        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .revenue-card { background: white; padding: 20px; border-radius: 15px; border-left: 6px solid var(--primary); box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 300px; margin-bottom: 30px; }
        .grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn { border: none; width: 100%; padding: 14px; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 1rem; color: white; }
        .btn-blue { background: var(--primary); }
        .btn-green { background: var(--success); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f1f5f9; color: #475569; font-size: 0.8rem; text-transform: uppercase; }

        td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        .stock-tag { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .in-stock { background: #dcfce7; color: #166534; }
        .low-stock { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
        .out-stock { background: #fee2e2; color: #991b1b; animation: blink 1.5s infinite; }
        @keyframes blink { 50% { opacity: 0.6; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fas fa-utensils"></i> RMS Admin</h2>

        <a href="admin.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="cashier.php"><i class="fas fa-cash-register"></i> Cashier Counter</a>
        <a href="kitchen.php"><i class="fas fa-fire-burner"></i> Kitchen</a>
        <a href="order_index.php" target="_blank"><i class="fas fa-eye"></i> Customer View</a>

    </div>

    <div class="main">
        <div class="revenue-card">
            <small>TOTAL REVENUE</small>
            <h2>$<?php echo number_format($revenue, 2); ?></h2>
        </div>

        <div class="grid">

            <div class="card">
                <h3><i class="fas <?php echo $edit_item ? 'fa-edit' : 'fa-plus-circle'; ?>"></i> 
                <?php echo $edit_item ? 'Edit Item' : 'Add New Item'; ?></h3>
                <form method="POST">
                    <?php if($edit_item): ?>
                        <input type="hidden" name="item_id" value="<?php echo $edit_item['id']; ?>">
                    <?php endif; ?>
                    <input type="text" name="item_name" placeholder="Item Name" value="<?php echo $edit_item['name'] ?? ''; ?>" required>
                    <textarea name="description" placeholder="Description..." rows="3"><?php echo $edit_item['description'] ?? ''; ?></textarea>
                    <input type="number" step="0.01" name="price" placeholder="Price ($)" value="<?php echo $edit_item['price'] ?? ''; ?>" required>
                    <select name="category">
                        <option value="Food" <?php echo ($edit_item && $edit_item['category']=='Food')?'selected':''; ?>>Food</option>
                        <option value="Drinks" <?php echo ($edit_item && $edit_item['category']=='Drinks')?'selected':''; ?>>Drinks</option>
                    </select>
                    <input type="number" name="stock" placeholder="Stock" value="<?php echo $edit_item['stock'] ?? ''; ?>" required>
                    <input type="text" name="item_image_url" placeholder="Image URL" value="<?php echo $edit_item['image_path'] ?? ''; ?>">
                    <?php if($edit_item): ?>
                        <button type="submit" name="update_item" class="btn btn-green">Update Item</button>
                        <a href="admin.php" style="display:block; text-align:center; margin-top:10px; color:#64748b; text-decoration:none;">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_item" class="btn btn-blue">Add to Menu</button>
                    <?php endif; ?>
                </form>
            </div>


            <div class="card" style="padding:0; overflow:hidden;">
                <div style="padding: 20px; border-bottom: 1px solid #eee;">
                    <form method="GET">
                        <input type="text" name="search" placeholder="Search menu..." value="<?php echo htmlspecialchars($search); ?>" style="width: 200px; margin:0;">
                        <button type="submit" class="btn btn-blue" style="width:auto; padding:10px 15px;">Search</button>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr><th>Image</th><th>Details</th><th>Stock</th><th>Price</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $row['image_path']; ?>" class="item-img" onerror="this.src='https://placehold.co'">
                            </td>
                            <td><strong><?php echo $row['name']; ?></strong><br><small><?php echo $row['category']; ?></small></td>
                            <td>
                                <span class="stock-tag <?php echo ($row['stock'] <= 0) ? 'out-stock' : (($row['stock'] <= 5) ? 'low-stock' : 'in-stock'); ?>">
                                    <?php echo ($row['stock'] <= 0) ? 'RESTOCK' : (($row['stock'] <= 5) ? 'LOW: '.$row['stock'] : $row['stock'].' units'); ?>
                                </span>
                            </td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>" style="color:var(--primary); text-decoration:none; margin-right:10px; font-weight:bold;">EDIT</a>
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete?')" style="color:var(--danger); text-decoration:none; font-weight:bold;">DELETE</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>