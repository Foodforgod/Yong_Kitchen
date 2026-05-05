<?php
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

</body>
</html>