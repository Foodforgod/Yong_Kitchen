<?php
include 'db.php';


if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat = $_POST['category'];
    $stock = $_POST['stock'];
    
    $sql = "INSERT INTO items (name, description, price, category, stock) 
            VALUES ('$name', '$desc', '$price', '$cat', '$stock')";
    $conn->query($sql);
    header("Location: admin.php");
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM items WHERE id = $id");
    header("Location: admin.php");
}


$revenue_res = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'");
$revenue = $revenue_res->fetch_assoc()['total'] ?? 0;

$count_res = $conn->query("SELECT COUNT(*) as total_items FROM items");
$total_items = $count_res->fetch_assoc()['total_items'];

$items = $conn->query("SELECT * FROM items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fas fa-utensils"></i> RMS Admin</h2>
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
                    <select name="category">
                        <option value="Food">Food</option>
                        <option value="Drinks">Drinks</option>
                        <option value="Desserts">Desserts</option>
                    </select>
                    <input type="number" name="stock" placeholder="Initial Stock" required>
                    <button type="submit" name="add_item" class="btn-submit">Add to Menu</button>
                </form>
            </div>

           
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>ITEM</th>
                            <th>CATEGORY</th>
                            <th>STOCK</th>
                            <th>PRICE</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
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
