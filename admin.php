<?php
include 'db.php';


if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat = $_POST['category'];
    $stock = $_POST['stock'];

    
    $image_name = "";
    if (!empty($_FILES['item_image']['name'])) {
        $image_name = time() . "_" . $_FILES['item_image']['name'];
        move_uploaded_file($_FILES['item_image']['tmp_name'], "uploads/" . $image_name);
    }

    $sql = "INSERT INTO items (name, description, price, category, stock, image_path) 
            VALUES ('$name', '$desc', '$price', '$cat', '$stock', '$image_name')";
    $conn->query($sql);
    header("Location: admin.php?success=1");
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    
    $res = $conn->query("SELECT image_path FROM items WHERE id = $id");
    $img_data = $res->fetch_assoc();
    if($img_data['image_path']) {
        unlink("uploads/" . $img_data['image_path']);
    }

    $conn->query("DELETE FROM items WHERE id = $id");
    header("Location: admin.php?deleted=1");
}


$revenue_res = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'");
$revenue = $revenue_res->fetch_assoc()['total'] ?? 0;


$items = $conn->query("SELECT * FROM items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Restaurant Management</title>
    <link href="https://cloudflare.com" rel="stylesheet">
    <style>
        :root { --primary: #2563eb; --danger: #ef4444; --bg: #f8fafc; --dark: #1e293b; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 20px; box-sizing: border-box; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 30px; color: #38bdf8; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 10px; transition: 0.3s; }
        .sidebar a:hover { background: #334155; color: white; }
        .sidebar a.active { background: var(--primary); color: white; }

        
        .main { margin-left: 260px; padding: 40px; width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
       
        .revenue-card { background: white; padding: 20px; border-radius: 15px; border-left: 6px solid var(--primary); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 300px; }
        .revenue-card small { color: #64748b; font-weight: bold; }
        .revenue-card h2 { margin: 5px 0 0; font-size: 2rem; color: var(--dark); }

      
        .grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        
    
        input, select, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn-add { background: var(--primary); color: white; border: none; width: 100%; padding: 14px; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 1rem; }
        .btn-add:hover { background: #1d4ed8; }

      
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; background: #f1f5f9; color: #475569; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .stock-tag { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .in-stock { background: #dcfce7; color: #166534; }
        .out-stock { background: #fee2e2; color: #991b1b; }
        .trash-btn { color: var(--danger); font-size: 1.2rem; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fas fa-utensils"></i> RMS Admin</h2>
        <a href="admin.php" class="active"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="kitchen.php"><i class="fas fa-fire-burner"></i> Kitchen View</a>
        <a href="index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Customer View</a>
    </div>

    <div class="main">
        <div class="header">
            <h1>Restaurant Dashboard</h1>
            <div class="revenue-card">
                <small>TOTAL REVENUE</small>
                <h2>$<?php echo number_format($revenue, 2); ?></h2>
            </div>
        </div>

        <div class="grid">
           
            <div class="card">
                <h3 style="margin-top:0;"><i class="fas fa-plus-circle"></i> Add New Item</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="item_name" placeholder="Item Name (e.g., Spicy Burger)" required>
                    <textarea name="description" placeholder="Description..." rows="3"></textarea>
                    <input type="number" step="0.01" name="price" placeholder="Price ($)" required>
                    <select name="category">
                        <option value="Food">Food</option>
                        <option value="Drinks">Drinks</option>
                        <option value="Desserts">Desserts</option>
                    </select>
                    <input type="number" name="stock" placeholder="Initial Stock Quantity" required>
                    
                    <label style="font-size: 0.85rem; color: #64748b;">Upload Food Image:</label>
                    <input type="file" name="item_image" accept="image/*">
                    
                    <button type="submit" name="add_item" class="btn-add">Add to Menu</button>
                </form>
            </div>

           
            <div class="card" style="padding: 0; overflow: hidden;">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Item Details</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if($row['image_path']): ?>
                                    <img src="uploads/<?php echo $row['image_path']; ?>" width="60" height="60" style="object-fit:cover; border-radius:10px;">
                                <?php else: ?>
                                    <div style="width:60px; height:60px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:0.7rem;">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo $row['name']; ?></strong><br>
                                <small style="color:#94a3b8"><?php echo $row['category']; ?></small>
                            </td>
                            <td>
                                <span class="stock-tag <?php echo ($row['stock'] <= 0) ? 'out-stock' : 'in-stock'; ?>">
                                    <?php echo $row['stock']; ?> units
                                </span>
                            </td>
                            <td><strong>$<?php echo number_format($row['price'], 2); ?></strong></td>
                            <td>
                                <a href="?delete=<?php echo $row['id']; ?>" class="trash-btn" onclick="return confirm('Are you sure you want to delete this item?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
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
