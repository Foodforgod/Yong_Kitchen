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

    $_SESSION['success'] = "Item added to cart successfully!";
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
    <meta name="description" content="Digital Menu - Order delicious food from our restaurant">
    <meta name="theme-color" content="#2563eb">
    <title>Digital Menu | Restaurant System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.5s ease-in forwards';
                    setTimeout(() => notification.remove(), 500);
                }, 4000);
            });
        });

        
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                        submitBtn.disabled = true;
                    }
                });
            });
        });

       
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        });
    </script>
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.5s ease-in forwards';
                    setTimeout(() => notification.remove(), 500);
                }, 4000);
            });
        });

        
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                        submitBtn.disabled = true;
                    }
                });
            });
        });
    </script>
    <style>
     
        .category-wrapper {
            margin: 25px 0;
            padding-bottom: 10px;
        }

        .category-bar {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scrollbar-width: none;
            padding: 8px 4px;
        }

        .category-bar::-webkit-scrollbar {
            display: none;
        }

        .cat-pill {
            padding: 14px 28px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .cat-pill.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
        }

        .cat-pill:hover:not(.active) {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .search-container {
            margin-bottom: 25px;
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #f1f5f9;
        }

        .search-container form {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-container .search-input-wrapper {
            flex: 1;
            position: relative;
        }

        .search-container i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 2;
        }

        .search-container input[type="text"] {
            width: 100%;
            padding: 14px 14px 14px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafbfc;
        }

        .search-container input[type="text"]:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .search-container .btn-primary {
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
    </style>
</head>
<body class="customer-body">
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="notification success" id="success-notification" role="alert" aria-live="assertive">
        <i class="fas fa-check-circle" aria-hidden="true"></i>
        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="notification error" id="error-notification" role="alert" aria-live="assertive">
        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <main id="main-content">
    <div class="menu-container">
    <div class="menu-header">
        <h1>Digital Menu</h1>
        <p>Choose your meal and enjoy your stay!</p>
    </div>

    <div class="search-container">
        <form method="GET" action="order_index.php">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>"
                       placeholder="Search dishes...">
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
                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk0YTNiOCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkxvYWRpbmcuLi48L3RleHQ+PC9zdmc+" data-src="<?php echo !empty($row['image_path']) ? $row['image_path'] : 'https://placehold.co/400x250?text=No+Image'; ?>" class="item-image lazy" alt="<?php echo htmlspecialchars($row['name']); ?>">
                
                <div class="item-content">
                    <div class="item-name"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="item-desc"><?php echo htmlspecialchars($row['description']); ?></div>
                    
                    <div class="price-stock-row">
                        <span class="item-price">$<?php echo number_format($row['price'], 2); ?></span>
                        <span class="badge bg-completed" style="font-size:0.65rem;">Stock: <?php echo $row['stock']; ?></span>
                    </div>

                    <form method="POST" class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                        
                        <div class="qty-remarks-area">
                            <div class="qty-row">
                                <label for="qty-<?php echo $row['id']; ?>">Qty:</label>
                                <input type="number" id="qty-<?php echo $row['id']; ?>" name="qty" value="1" min="1" max="<?php echo $row['stock']; ?>">
                            </div>
                            <input type="text" id="remarks-<?php echo $row['id']; ?>" name="remarks" placeholder="Notes (e.g. less ice)" class="remarks-input">
                        </div>

                        <button type="submit" name="add_to_cart" class="btn btn-primary">
                            <i class="fas fa-plus"></i> ADD TO CART
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 80px 40px; background: white; border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.08);">
                <div style="margin-bottom: 30px;">
                    <i class="fas fa-search" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
                    <h3 style="color: #475569; margin-bottom: 10px; font-size: 1.5rem;">No dishes found</h3>
                    <p style="color: #64748b; font-size: 1rem;">Try adjusting your search or browse different categories.</p>
                </div>
                <a href="order_index.php" class="btn btn-primary" style="padding: 14px 28px; font-size: 1rem;">
                    <i class="fas fa-refresh"></i> Browse All Menu
                </a>
            </div>
        <?php endif; ?>
    </div>
    </main>
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

<div style="height: 120px;"></div> 
</body>
</html>