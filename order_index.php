<?php
session_start();
include 'db.php'; 

if (isset($_POST['add_to_cart'])) {
    $id = $_POST['item_id'];
    $name = $_POST['item_name'];
    $price = $_POST['price'];
    $qty = (int)$_POST['qty'];
    $remarks = $_POST['remarks']; 
    
    if (!isset($_SESSION['customer_cart'])) { $_SESSION['customer_cart'] = []; }
    
    $_SESSION['customer_cart'][] = [
        'id' => $id, 
        'name' => $name, 
        'price' => $price, 
        'qty' => $qty,
        'remarks' => $remarks 
    ];

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
    <title>Menu | Restaurant System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
     
        .category-wrapper {
            margin: 20px 0;
            padding-bottom: 5px;
        }
        .category-bar {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            scrollbar-width: none;
            padding: 5px 2px;
        }
        .category-bar::-webkit-scrollbar { display: none; }

        .cat-pill {
            padding: 12px 25px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .cat-pill.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        }

        .cat-pill:hover:not(.active) {
            border-color: var(--primary);
            color: var(--primary);
        }

        .search-container {
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="customer-body">

<div class="menu-container">
    <div class="menu-header">
        <h1>Digital Menu</h1>
        <p>Choose your meal and enjoy your stay!</p>
    </div>

    <div class="card search-container" style="padding: 15px;">
        <form method="GET" action="order_index.php" style="display: flex; gap: 10px;">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
            <div style="flex-grow: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 15px; color: #94a3b8;"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" 
                       placeholder="Search dishes..." 
                       style="padding-left: 45px; margin: 0; width: 100%;">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <div class="category-wrapper">
        <div class="category-bar">
            <a href="order_index.php?search=<?php echo urlencode($search_term); ?>" 
               class="cat-pill <?php echo (empty($category_filter)) ? 'active' : ''; ?>">All</a>
            
            <a href="order_index.php?category=Main Course&search=<?php echo urlencode($search_term); ?>" 
               class="cat-pill <?php echo ($category_filter == 'Main Course') ? 'active' : ''; ?>">Main Course</a>
            
            <a href="order_index.php?category=Appetizer&search=<?php echo urlencode($search_term); ?>" 
               class="cat-pill <?php echo ($category_filter == 'Appetizer') ? 'active' : ''; ?>">Appetizer</a>
            
            <a href="order_index.php?category=Drinks&search=<?php echo urlencode($search_term); ?>" 
               class="cat-pill <?php echo ($category_filter == 'Drinks') ? 'active' : ''; ?>">Drinks</a>

            <a href="order_index.php?category=Dessert&search=<?php echo urlencode($search_term); ?>" 
               class="cat-pill <?php echo ($category_filter == 'Dessert') ? 'active' : ''; ?>">Dessert</a>
        </div>
    </div>

    <div class="menu-grid">
        <?php if($items->num_rows > 0): ?>
            <?php while($row = $items->fetch_assoc()): ?>
            <div class="item-card">
                <img src="<?php echo !empty($row['image_path']) ? $row['image_path'] : 'https://placehold.co/400x250?text=No+Image'; ?>" class="item-image" alt="dish">
                
                <div class="item-content">
                    <div class="item-name"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="item-desc"><?php echo htmlspecialchars($row['description']); ?></div>
                    
                    <div class="price-stock-row">
                        <span class="item-price">$<?php echo number_format($row['price'], 2); ?></span>
                        <span class="badge bg-completed" style="font-size:0.65rem;">Stock: <?php echo $row['stock']; ?></span>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                        
                        <div class="qty-remarks-area" style="background: #f8fafc; padding: 10px; border-radius: 8px; margin-bottom: 12px;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                                <small>Qty:</small>
                                <input type="number" name="qty" value="1" min="1" max="<?php echo $row['stock']; ?>" style="margin:0; padding:5px; width:60px;">
                            </div>
                            <input type="text" name="remarks" placeholder="Notes (e.g. less ice)" class="remarks-input" style="font-size:0.8rem;">
                        </div>
                        
                        <button type="submit" name="add_to_cart" class="btn btn-primary" style="width:100%; border-radius: 8px;">
                            <i class="fas fa-plus"></i> ADD TO CART
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                <i class="fas fa-utensils" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                <h3>No dishes found</h3>
                <p>Try a different category or search term.</p>
                <a href="order_index.php" class="btn btn-primary">Refresh Menu</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if($cart_count > 0): ?>
<div class="bottom-bar">
    <div class="cart-info">
        <i class="fas fa-shopping-basket"></i>
        <span><b><?php echo $cart_count; ?></b> items selected</span>
    </div>
    <a href="cart_view.php" class="btn-checkout">
        VIEW CART <i class="fas fa-arrow-right"></i>
    </a>
</div>
<?php endif; ?>

<div style="height: 100px;"></div> 
</body>
</html>