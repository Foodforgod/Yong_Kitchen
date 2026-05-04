<?php
include 'db.php';


if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat = $_POST['category'];
    $stock = $_POST['stock'];
<<<<<<< HEAD

    
    $image_name = "";
    if (!empty($_FILES['item_image']['name'])) {
        $image_name = time() . "_" . $_FILES['item_image']['name'];
        move_uploaded_file($_FILES['item_image']['tmp_name'], "uploads/" . $image_name);
    }

    $sql = "INSERT INTO items (name, description, price, category, stock, image_path) 
            VALUES ('$name', '$desc', '$price', '$cat', '$stock', '$image_name')";
    $conn->query($sql);
    header("Location: admin.php?success=1");
=======
    
    $sql = "INSERT INTO items (name, description, price, category, stock) 
            VALUES ('$name', '$desc', '$price', '$cat', '$stock')";
    $conn->query($sql);
    header("Location: admin.php");
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
<<<<<<< HEAD
    
    
    $res = $conn->query("SELECT image_path FROM items WHERE id = $id");
    $img_data = $res->fetch_assoc();
    if($img_data['image_path']) {
        unlink("uploads/" . $img_data['image_path']);
    }

    $conn->query("DELETE FROM items WHERE id = $id");
    header("Location: admin.php?deleted=1");
=======
    $conn->query("DELETE FROM items WHERE id = $id");
    header("Location: admin.php");
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6
}


$revenue_res = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'");
$revenue = $revenue_res->fetch_assoc()['total'] ?? 0;

<<<<<<< HEAD
=======
$count_res = $conn->query("SELECT COUNT(*) as total_items FROM items");
$total_items = $count_res->fetch_assoc()['total_items'];
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6

$items = $conn->query("SELECT * FROM items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
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
=======
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Restaurant System</title>
    <link href="https://cloudflare.com" rel="stylesheet">
    <style>
        :root { --primary: #2563eb; --bg: #f8fafc; --text: #1e293b; --danger: #ef4444; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; }
        
      
        .sidebar { width: 250px; height: 100vh; background: #1e293b; color: white; position: fixed; padding: 20px; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 30px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover { background: #334155; color: white; }

       
        .main-content { margin-left: 250px; padding: 40px; width: 100%; }
        
     
        .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border-left: 5px solid var(--primary); }
        .card h3 { margin: 0; font-size: 0.9rem; color: #64748b; text-transform: uppercase; }
        .card p { margin: 10px 0 0; font-size: 1.8rem; font-weight: bold; color: var(--text); }

        .dashboard-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }

    
        .form-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); height: fit-content; }
        .form-card input, .form-card select, .form-card textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { background: var(--primary); color: white; border: none; width: 100%; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        
      
        .table-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; padding: 15px; text-align: left; font-size: 0.85rem; color: #64748b; }
        td { padding: 15px; border-top: 1px solid #f1f5f9; }
        .badge-stock { background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; }
        .badge-out { background: #fee2e2; color: #991b1b; }
        .btn-delete { color: var(--danger); text-decoration: none; font-size: 0.9rem; }
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fas fa-utensils"></i> RMS Admin</h2>
<<<<<<< HEAD
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
=======
        <a href="#"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="kitchen.php"><i class="fas fa-hat-chef"></i> Kitchen View</a>
        <a href="index.php" target="_blank"><i class="fas fa-eye"></i> Customer View</a>
    </div>

    <div class="main-content">
        <h1>Management Dashboard</h1>

        <div class="stats-container">
            <div class="card">
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($revenue, 2); ?></p>
            </div>
            <div class="card" style="border-left-color: #10b981;">
                <h3>Total Menu Items</h3>
                <p><?php echo $total_items; ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            
            <div class="form-card">
                <h3><i class="fas fa-plus-circle"></i> Add New Item</h3>
                <form method="POST">
                    <input type="text" name="item_name" placeholder="Item Name" required>
                    <textarea name="description" placeholder="Short description..." rows="2"></textarea>
                    <input type="number" step="0.01" name="price" placeholder="Price (e.g. 8.50)" required>
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6
                    <select name="category">
                        <option value="Food">Food</option>
                        <option value="Drinks">Drinks</option>
                        <option value="Desserts">Desserts</option>
                    </select>
<<<<<<< HEAD
                    <input type="number" name="stock" placeholder="Initial Stock Quantity" required>
                    
                    <label style="font-size: 0.85rem; color: #64748b;">Upload Food Image:</label>
                    <input type="file" name="item_image" accept="image/*">
                    
                    <button type="submit" name="add_item" class="btn-add">Add to Menu</button>
=======
                    <input type="number" name="stock" placeholder="Initial Stock" required>
                    <button type="submit" name="add_item" class="btn-submit">Add to Menu</button>
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6
                </form>
            </div>

           
<<<<<<< HEAD
            <div class="card" style="padding: 0; overflow: hidden;">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Item Details</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Action</th>
=======
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>ITEM</th>
                            <th>CATEGORY</th>
                            <th>STOCK</th>
                            <th>PRICE</th>
                            <th>ACTION</th>
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
<<<<<<< HEAD
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
=======
                            <td><strong><?php echo $row['name']; ?></strong></td>
                            <td><?php echo $row['category']; ?></td>
                            <td>
                                <span class="badge-stock <?php echo ($row['stock'] <= 0) ? 'badge-out' : ''; ?>">
                                    <?php echo $row['stock']; ?> units
                                </span>
                            </td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this item?')">
                                    <i class="fas fa-trash"></i> Delete
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6
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
