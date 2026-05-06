<?php
<<<<<<< HEAD
include 'db.php'; 

$items = $conn->query("SELECT * FROM items WHERE stock > 0");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Menu</title>
    <style>
        .menu-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .item-card { border: 1px solid #ddd; padding: 15px; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>Welcome to our Restaurant</h1>
    <div class="menu-grid">
        <?php while($row = $items->fetch_assoc()): ?>
        <div class="item-card">
            <h3><?php echo $row['name']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
            
            <form method="POST" action="process_order.php">
                <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                quantity: <input type="number" name="qty" value="1" min="1" max="<?php echo $row['stock']; ?>">
                <button type="submit">Order Now</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
=======
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


$items = $conn->query("SELECT * FROM items WHERE stock > 0 ORDER BY id DESC");


$cart_count = 0;
if(isset($_SESSION['customer_cart'])) {
    foreach($_SESSION['customer_cart'] as $c) $cart_count += $c['qty'];
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Menu | Yong Kitchen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2563eb; --bg: #f8fafc; --white: #ffffff; --text: #1e293b; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; padding-bottom: 100px; }
        
        nav { background: var(--white); padding: 1rem 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .logo { font-size: 1.5rem; font-weight: bold; color: var(--primary); }
        
        .cart-link { position: relative; color: var(--primary); font-size: 1.6rem; text-decoration: none; }
        .badge { position: absolute; top: -5px; right: -10px; background: #ef4444; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 50%; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        
        .item-card { background: var(--white); border-radius: 20px; overflow: hidden; box-shadow: 0 10px 15px rgba(0,0,0,0.05); transition: 0.3s; }
        .item-card:hover { transform: translateY(-5px); }

        .image-box { width: 100%; height: 200px; background: #e2e8f0; }
        .image-box img { width: 100%; height: 100%; object-fit: cover; }
        
        .item-info { padding: 20px; }
        .price-row { display: flex; justify-content: space-between; align-items: center; margin: 15px 0; }
        .price { font-size: 1.4rem; font-weight: 700; color: var(--primary); }
        
        .qty-selector { display: flex; align-items: center; gap: 10px; background: #f1f5f9; padding: 5px 10px; border-radius: 10px; margin-bottom: 10px; }
        .qty-selector input { border: none; background: transparent; width: 40px; text-align: center; font-weight: bold; }

        .remarks-input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.85rem; box-sizing: border-box; }

        .btn-add { background: var(--primary); color: white; border: none; width: 100%; padding: 12px; border-radius: 12px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-add:hover { background: #1d4ed8; }

        .bottom-bar { position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 15px 5%; box-shadow: 0 -5px 15px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; z-index: 1000; }
        .btn-checkout { background: #10b981; color: white; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<nav>
    <div class="logo"><i class="fas fa-utensils"></i> Yong Kitchen</div>
    <a href="cart_view.php" class="cart-link">
        <i class="fas fa-shopping-cart"></i>
        <?php if($cart_count > 0) echo "<span class='badge'>$cart_count</span>"; ?>
    </a>
</nav>

<div class="container">
    <div class="menu-grid">
        <?php while($row = $items->fetch_assoc()): ?>
        <div class="item-card">
            <div class="image-box">
                <?php 
                $img = $row['image_path'];
                $src = (strpos($img, 'http') === 0) ? $img : "uploads/" . $img;
                ?>
                <img src="<?php echo $src; ?>" onerror="this.src='https://placehold.co/300x200?text=Food'">
            </div>
            <div class="item-info">
                <h3 style="margin:0;"><?php echo htmlspecialchars($row['name']); ?></h3>
                <p style="color:#64748b; font-size:0.9rem; height:40px; margin:10px 0; overflow:hidden;"><?php echo htmlspecialchars($row['description']); ?></p>
                
                <div class="price-row">
                    <span class="price">$<?php echo number_format($row['price'], 2); ?></span>
                    <span style="font-size:0.8rem; color:#166534;">Stock: <?php echo $row['stock']; ?></span>
                </div>

                <form method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                    <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                    
                    <div class="qty-selector">
                        <small>Qty:</small>
                        <input type="number" name="qty" value="1" min="1" max="<?php echo $row['stock']; ?>">
                    </div>
                    
                
                    <input type="text" name="remarks" placeholder="Notes (e.g. No Veggies)" class="remarks-input">
                    
                    <button type="submit" name="add_to_cart" class="btn-add">
                        <i class="fas fa-plus"></i> ADD TO CART
                    </button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php if($cart_count > 0): ?>
<div class="bottom-bar">
    <span><b><?php echo $cart_count; ?></b> items in cart</span>
    <a href="cart_view.php" class="btn-checkout">GO TO CHECKOUT <i class="fas fa-chevron-right"></i></a>
</div>
<?php endif; ?>

>>>>>>> 6280fc8d0a0b58f6b6169935d5fab08034176079
</body>
</html>