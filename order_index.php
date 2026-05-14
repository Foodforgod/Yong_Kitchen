<?php
session_start();
include 'db.php'; 

if (isset($_POST['add_to_cart'])) {
    $id = $_POST['item_id'];
    $name = $_POST['item_name'];
    $price = (float)$_POST['price'];
    $qty = (int)$_POST['qty'];
    $remarks = trim($_POST['remarks']);

    if ($qty <= 0) {
        $_SESSION['error'] = "Please select a valid quantity.";
        header("Location: order_index.php");
        exit();
    }

    if (!isset($_SESSION['customer_cart'])) {
        $_SESSION['customer_cart'] = [];
    }

    $item_exists = false;
    foreach ($_SESSION['customer_cart'] as &$cart_item) {
        if ($cart_item['id'] == $id && $cart_item['remarks'] == $remarks) {
            $cart_item['qty'] += $qty;
            $item_exists = true;
            break;
        }
    }

    if (!$item_exists) {
        $_SESSION['customer_cart'][] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'qty' => $qty,
            'remarks' => $remarks
        ];
    }

    $_SESSION['success'] = "Item added to cart!";
    header("Location: order_index.php");
    exit();
}

$search_term = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : "";
$category_filter = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : "";

$query = "SELECT * FROM items WHERE stock > 0";
if (!empty($search_term)) {
    $query .= " AND (name LIKE '%$search_term%' OR description LIKE '%$search_term%')";
}
if (!empty($category_filter)) {
    $query .= " AND category = '$category_filter'";
}
$query .= " ORDER BY id DESC";
$items = $conn->query($query);

$cart_count = 0;
if(isset($_SESSION['customer_cart'])) {
    foreach($_SESSION['customer_cart'] as $c) $cart_count += $c['qty'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Menu | Restaurant System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
       
        :root {
            --primary-blue: #2563eb;
            --success-green: #10b981;
            --dark-slate: #1e293b;
        }

        .category-wrapper { margin: 25px 0; }
        .category-bar {
            display: flex; gap: 12px; overflow-x: auto; padding: 10px 5px;
            scrollbar-width: none;
        }
        .category-bar::-webkit-scrollbar { display: none; }

        .cat-pill {
            padding: 10px 22px; background: white; border: 2px solid #e2e8f0;
            border-radius: 50px; text-decoration: none; color: #64748b;
            font-weight: 600; white-space: nowrap; transition: 0.3s;
        }
        .cat-pill.active {
            background: var(--primary-blue); color: white; border-color: var(--primary-blue);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        
        .bottom-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 500px;
            background: var(--dark-slate);
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 1000;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { transform: translate(-50%, 100%); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }

        .cart-info { display: flex; align-items: center; gap: 10px; }
        .cart-info i { color: var(--success-green); font-size: 1.2rem; }

        .btn-checkout {
            background: var(--success-green);
            color: white;
            padding: 8px 18px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.2s;
        }
        .btn-checkout:hover { background: #059669; transform: scale(1.05); }

        .search-container {
            margin: 20px 0; background: white; padding: 20px;
            border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .search-container form { display: flex; gap: 10px; }
        .search-input-wrapper { flex: 1; position: relative; }
        .search-input-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .search-input-wrapper input { width: 100%; padding: 12px 12px 12px 45px; border: 1px solid #e2e8f0; border-radius: 10px; }

        .qty-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .qty-row input { width: 60px; padding: 5px; border-radius: 5px; border: 1px solid #ddd; }
        .remarks-input { width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 15px; font-size: 0.85rem; }
    </style>
</head>

<body class="customer-body">

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notification success" id="notif"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <main class="menu-container">
        <div class="menu-header">
            <h1>Digital Menu</h1>
            <p>Select your favorite dishes and order directly.</p>
        </div>

        <div class="card">
            <form method="GET" style="display: flex; gap: 10px;">
                <input type="hidden" name="category" value="<?php echo $category_filter; ?>">
                <div style="flex: 1; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Search dishes..." style="width: 100%; padding: 12px 12px 12px 45px; border: 1px solid #e2e8f0; border-radius: 10px;">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
            </form>
        </div>

        <div style="margin: 25px 0;">
            <div class="category-bar">
                <a href="order_index.php?search=<?php echo urlencode($search_term); ?>" 
                   class="cat-pill <?php echo (empty($category_filter)) ? 'active' : ''; ?>">All</a>
                <?php 
                $cats = ['Main Course', 'Appetizer', 'Drinks', 'Dessert'];
                foreach($cats as $cat): ?>
                    <a href="order_index.php?category=<?php echo $cat; ?>&search=<?php echo urlencode($search_term); ?>" 
                       class="cat-pill <?php echo ($category_filter == $cat) ? 'active' : ''; ?>"><?php echo $cat; ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="menu-grid">
            <?php if($items->num_rows > 0): while($row = $items->fetch_assoc()): ?>
                <div class="item-card">
                    <img src="<?php echo !empty($row['image_path']) ? $row['image_path'] : 'https://placehold.co/400x250?text=No+Image'; ?>" class="item-image" alt="dish">
                    <div class="item-content">
                        <div class="item-name"><?php echo htmlspecialchars($row['name']); ?></div>
                        <p class="item-desc" style="font-size:0.85rem; color:#64748b;"><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div class="price-stock-row" style="display:flex; justify-content:space-between; align-items:center; margin: 15px 0;">
                            <span class="item-price" style="font-weight:800; color:var(--primary-blue); font-size:1.1rem;">$<?php echo number_format($row['price'], 2); ?></span>
                            <span class="badge" style="background:#f1f5f9; color:#475569; padding:4px 8px; border-radius:5px; font-size:0.7rem;">Stock: <?php echo $row['stock']; ?></span>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                            <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                            
                            <div class="qty-row">
                                <label>Qty:</label>
                                <input type="number" name="qty" value="1" min="1" max="<?php echo $row['stock']; ?>">
                            </div>
                            <input type="text" name="remarks" placeholder="Special notes..." class="remarks-input">

                            <button type="submit" name="add_to_cart" class="btn btn-primary" style="width:100%;">
                                <i class="fas fa-plus"></i> ADD TO CART
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <p style="text-align:center; grid-column: 1/-1; padding:50px;">No items found.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php if($cart_count > 0): ?>
    <div class="bottom-bar">
        <div class="cart-info">
            <i class="fas fa-shopping-cart"></i>
            <span><b><?php echo $cart_count; ?></b> <?php echo ($cart_count > 1) ? 'items' : 'item'; ?> added</span>
        </div>
        <a href="cart_view.php" class="btn-checkout">
            VIEW CART <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div style="height: 100px;"></div> <?php endif; ?>

    <script>
        
        setTimeout(() => {
            const notif = document.getElementById('notif');
            if(notif) notif.style.display = 'none';
        }, 3000);
    </script>
</body>
</html>