<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['add_item'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $cat = $_POST['category'];
    $img = trim($_POST['image_url']);
    $desc = trim($_POST['description']);

    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO items (name, description, price, category, stock, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssi", $name, $desc, $price, $cat, $stock, $img);
        if ($stmt->execute()) {
            $message = "<div class='alert success'>Item '$name' added successfully!</div>";
        } else {
            $message = "<div class='alert danger'>Error: " . $conn->error . "</div>";
        }
    }
}

if (isset($_POST['update_item'])) {
    $id = intval($_POST['item_id']);
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $cat = $_POST['category'];
    $img = trim($_POST['image_url']);
    $desc = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE items SET name=?, description=?, price=?, category=?, stock=?, image_path=? WHERE id=?");
    $stmt->bind_param("ssdssii", $name, $desc, $price, $cat, $stock, $img, $id);
    
    if ($stmt->execute()) {
        $message = "<div class='alert success'>Item updated successfully!</div>";
    } else {
        $message = "<div class='alert danger'>Update failed: " . $conn->error . "</div>";
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM items WHERE id = $id");
    header("Location: admin.php?deleted=1");
    exit();
}

$total_revenue = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status='completed'")->fetch_assoc()['total'] ?? 0;
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='completed'")->fetch_assoc()['count'] ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='pending'")->fetch_assoc()['count'] ?? 0;
$items = $conn->query("SELECT * FROM items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | RMS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }
        .modal-content { background: white; margin: 5% auto; padding: 25px; width: 450px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .close-btn { float: right; cursor: pointer; font-size: 24px; color: #64748b; }
        .close-btn:hover { color: #ef4444; }
        .img-preview-table { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>RMS ADMIN</h2>
        <a href="admin.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="history.php"><i class="fas fa-history"></i> History</a> 
        <a href="kitchen.php"><i class="fas fa-utensils"></i> Kitchen</a>
        <a href="cashier.php"><i class="fas fa-cash-register"></i> Cashier</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <h1>Dashboard Overview</h1>
            <div class="user-info">Logged in: <b><?php echo htmlspecialchars($_SESSION['admin_user']); ?></b></div>
        </div>

        <?php echo $message; ?>
        <?php if(isset($_GET['deleted'])) echo "<div class='alert success'>Item removed successfully.</div>"; ?>

        <div class="stats-row">
            <a href="history.php" style="text-decoration: none; color: inherit; flex: 1;">
                <div class="stat-card">
                    <small>TOTAL REVENUE</small>
                    <h2 style="color: #10b981;">$<?php echo number_format($total_revenue, 2); ?></h2>
                </div>
            </a>
            <a href="history.php" style="text-decoration: none; color: inherit; flex: 1;">
                <div class="stat-card" style="border-left-color: #3b82f6;">
                    <small>COMPLETED ORDERS</small>
                    <h2><?php echo $total_orders; ?></h2>
                </div>
            </a>
            <div class="stat-card" style="border-left-color: #f59e0b; flex: 1;">
                <small>PENDING ORDERS</small>
                <h2><?php echo $pending_orders; ?></h2>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            
            <div class="card">
                <h3><i class="fas fa-plus-circle"></i> Add New Product</h3>
                <form method="POST">
                    <label>Product Name</label>
                    <input type="text" name="name" required placeholder="e.g. Burger">
                    
                    <div style="display:flex; gap:10px;">
                        <div style="flex:1">
                            <label>Price ($)</label>
                            <input type="number" step="0.01" name="price" required>
                        </div>
                        <div style="flex:1">
                            <label>Stock</label>
                            <input type="number" name="stock" value="50" required>
                        </div>
                    </div>

                    <label>Category</label>
                    <select name="category">
                        <option>Main Course</option>
                        <option>Appetizer</option>
                        <option>Drinks</option>
                        <option>Dessert</option>
                    </select>

                    <label>Image Filename/URL</label>
                    <input type="text" name="image_url" placeholder="e.g. image_9489fb.png">

                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>

                    <button type="submit" name="add_item" class="btn btn-primary" style="width:100%">
                        <i class="fas fa-save"></i> Save to Menu
                    </button>
                </form>
            </div>

            <div class="card">
                <h3><i class="fas fa-boxes"></i> Menu Inventory</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>IMG</th>
                                <th>Item Details</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($items->num_rows > 0): ?>
                                <?php while($row = $items->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo $row['image_path'] ?: 'https://placehold.co/50'; ?>" class="img-preview-table">
                                    </td>
                                    <td>
                                        <b><?php echo htmlspecialchars($row['name']); ?></b><br>
                                        <small><?php echo htmlspecialchars($row['category']); ?></small>
                                    </td>
                                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                                    <td><?php echo $row['stock']; ?></td>
                                    <td>
                                        <button class="btn btn-primary" onclick='openEditModal(<?php echo json_encode($row); ?>)' style="padding: 5px 10px;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this item?')" style="padding: 5px 10px;">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center;">No items found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3><i class="fas fa-edit"></i> Edit Product</h3>
            <form method="POST">
                <input type="hidden" name="item_id" id="edit_id">
                
                <label>Product Name</label>
                <input type="text" name="name" id="edit_name" required>
                
                <div style="display:flex; gap:10px;">
                    <div style="flex:1">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" id="edit_price" required>
                    </div>
                    <div style="flex:1">
                        <label>Stock</label>
                        <input type="number" name="stock" id="edit_stock" required>
                    </div>
                </div>

                <label>Category</label>
                <select name="category" id="edit_category">
                    <option>Main Course</option>
                    <option>Appetizer</option>
                    <option>Drinks</option>
                    <option>Dessert</option>
                </select>

                <label>Image Filename/URL</label>
                <input type="text" name="image_url" id="edit_image">

                <label>Description</label>
                <textarea name="description" id="edit_desc" rows="3"></textarea>

                <button type="submit" name="update_item" class="btn btn-primary" style="width:100%; margin-top:10px;">
                    Update Product
                </button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(item) {
            document.getElementById('edit_id').value = item.id;
            document.getElementById('edit_name').value = item.name;
            document.getElementById('edit_price').value = item.price;
            document.getElementById('edit_stock').value = item.stock;
            document.getElementById('edit_category').value = item.category;
            document.getElementById('edit_image').value = item.image_path;
            document.getElementById('edit_desc').value = item.description;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) closeModal();
        }
    </script>
</body>
</html>