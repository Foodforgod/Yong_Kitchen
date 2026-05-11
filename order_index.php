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

$search_term = "";
$query = "SELECT * FROM items WHERE stock > 0";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = $conn->real_escape_string(trim($_GET['search']));
    $query .= " AND (name LIKE '%$search_term%' OR category LIKE '%$search_term%')";
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
    <title>Menu | Digital Ordering System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="customer-body">

<div class="menu-container">
    <div class="menu-header">
        <h1>Welcome to Our Menu</h1>
        <p>Select your favorites and we'll handle the rest.</p>
    </div>

    <div class="card" style="margin-bottom: 30px; padding: 15px;">
        <form method="GET" action="order_index.php" style="display: flex; gap: 10px;">
            <div style="flex-grow: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 15px; color: #94a3b8;"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" 
                       placeholder="Search for dishes or categories..." 
                       style="padding-left: 45px; margin-bottom: 0;">
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0 25px;">Search</button>
            <?php if(!empty($search_term)): ?>
                <a href="order_index.php" class="btn btn-danger" style="text-decoration:none;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="menu-grid">
        <?php if($items->num_rows > 0): ?>
            <?php while($row = $items->fetch_assoc()): ?>
            <div class="item-card">
                <img src="<?php echo !empty($row['image_path']) ? $row['image_path'] : 'https://placehold.co/400x250?text=Food+Image'; ?>" class="item-image" alt="Dish">
                
                <div class="item-content">
                    <div class="item-name"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="item-desc"><?php echo htmlspecialchars($row['description']); ?></div>
                    
                    <div class="price-stock-row">
                        <span class="item-price">$<?php echo number_format($row['price'], 2); ?></span>
                        <span class="badge bg-completed" style="font-size:0.6rem;">Stock: <?php echo $row['stock']; ?></span>
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
                            <input type="text" name="remarks" placeholder="Add notes (e.g. No onion)" class="remarks-input" style="font-size:0.8rem;">
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
                <h3>No items found</h3>
                <p>Try searching for something else!</p>
                <a href="order_index.php" class="btn btn-primary">Refresh Menu</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if($cart_count > 0): ?>
<div class="bottom-bar">
    <div class="cart-info">
        <i class="fas fa-shopping-basket"></i>
        <span><b><?php echo $cart_count; ?></b> items in selection</span>
    </div>
    <a href="cart_view.php" class="btn-checkout">
        VIEW CART <i class="fas fa-arrow-right"></i>
    </a>
</div>
<?php endif; ?>

<div style="height: 100px;"></div> </body>
</html>