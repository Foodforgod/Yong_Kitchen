<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_POST['add_item'])) {
   
    $stmt = $conn->prepare("INSERT INTO items (name, description, price, category, stock, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssi", $_POST['item_name'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['stock'], $_POST['item_image_url']);
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
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root { --primary: #2563eb; --danger: #ef4444; --success: #10b981; --dark: #1e293b; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; color: #334155; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; padding: 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; }
        .sidebar h2 { color: #38bdf8; text-align: center; margin-top: 0; margin-bottom: 30px; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 10px; }
        .sidebar a.active { background: var(--primary); color: white; }
        .logout-btn { background: #334155; color: #f8fafc !important; font-weight: bold; border: 1px solid #475569; }
        .logout-btn:hover { background: var(--danger); }
        
        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .rev-card { background: white; padding: 20px; border-radius: 15px; border-left: 6px solid var(--success); width: 250px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        .grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn { border: none; width: 100%; padding: 12px; border-radius: 6px; cursor: pointer; color: white; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; background: #f1f5f9; font-size: 0.8rem; color: #64748b; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 0.9rem; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; }
        .bg-paid { background: #dcfce7; color: #166534; }
        .bg-unpaid { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="main">
        <h1>Order History</h1>
        
    </div>
</body>
</html>
