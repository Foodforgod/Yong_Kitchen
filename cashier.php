<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['complete_order'])) {
    $order_id = intval($_POST['order_id']);
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = $order_id");
    header("Location: cashier.php?paid=1");
    exit();
}

$orders = $conn->query("SELECT * FROM orders WHERE status != 'completed' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Station | RMS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <h2>RMS CASHIER</h2>
        <a href="admin.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="cashier.php" class="active"><i class="fas fa-cash-register"></i> Cashier</a>
        <a href="kitchen.php"><i class="fas fa-utensils"></i> Kitchen</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <h1>Cashier Station</h1>
            <span><i class="fas fa-clock"></i> Live Billing</span>
        </div>

        <?php if(isset($_GET['paid'])) echo "<div class='alert success'>Payment processed successfully!</div>"; ?>

        <div class="card">
            <h3>Active Bills</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Table</th>
                            <th>Items & Qty</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($o = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $o['id']; ?></td>
                            <td><b>Table <?php echo $o['table_number']; ?></b></td>
                            <td>
                                <?php
                                $oid = $o['id'];
                                $items = $conn->query("SELECT oi.quantity, i.name FROM order_items oi JOIN items i ON oi.item_id=i.id WHERE oi.order_id=$oid");
                                while($item = $items->fetch_assoc()) {
                                    echo "<div>" . $item['quantity'] . "x " . htmlspecialchars($item['name']) . "</div>";
                                }
                                ?>
                            </td>
                            <td><strong style="color:var(--primary);">$<?php echo number_format($o['total_price'], 2); ?></strong></td>
                            <td>
                                <span class="badge bg-<?php echo $o['status']; ?>">
                                    <?php echo strtoupper($o['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($o['status'] == 'ready'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <button type="submit" name="complete_order" class="btn btn-primary">
                                            <i class="fas fa-check"></i> Collect Payment
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <small style="color:var(--text-muted);">Cooking in progress...</small>
                                <?php endif; ?>
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