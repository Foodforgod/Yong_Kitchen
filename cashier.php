<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['complete_order'])) {
    $order_id = intval($_POST['order_id']);
    $amount_paid = floatval($_POST['amount_paid']); 
   
    $conn->query("UPDATE orders SET status = 'completed', amount_paid = $amount_paid WHERE id = $order_id");
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
    <style>
        .payment-box { background: #f8fafc; padding: 10px; border-radius: 8px; margin-top: 10px; border: 1px solid #e2e8f0; }
        .payment-input { width: 100px; padding: 5px; border-radius: 4px; border: 1px solid #cbd5e1; margin-bottom: 5px; }
        .change-display { display: block; font-weight: bold; color: #16a34a; margin-bottom: 10px; }
        .change-invalid { color: #dc2626; }
    </style>
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
                            <td><strong style="color:var(--primary);">$<span id="total-<?php echo $o['id']; ?>"><?php echo number_format($o['total_price'], 2); ?></span></strong></td>
                            <td><span class="badge bg-<?php echo $o['status']; ?>"><?php echo strtoupper($o['status']); ?></span></td>
                            <td>
                                <?php if($o['status'] == 'ready'): ?>
                                    <div class="payment-box">
                                        <label style="font-size: 0.8rem;">Amount Received:</label><br>
                                        <input type="number" step="0.01" class="payment-input" placeholder="0.00"
                                               oninput="calculateChange(<?php echo $o['id']; ?>, <?php echo $o['total_price']; ?>, this.value)">
                                        
                                        <span class="change-display" id="change-display-<?php echo $o['id']; ?>">Change: $0.00</span>

                                        <form method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                            <input type="hidden" name="amount_paid" id="hidden-paid-<?php echo $o['id']; ?>" value="0">
                                            
                                            <button type="submit" name="complete_order" id="btn-<?php echo $o['id']; ?>" class="btn btn-primary" style="width:100%">
                                                <i class="fas fa-check"></i> Complete Order
                                            </button>
                                        </form>
                                    </div>
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

    <script>
        function calculateChange(orderId, total, received) {
            const display = document.getElementById('change-display-' + orderId);
            const submitBtn = document.getElementById('btn-' + orderId);
            const hiddenInput = document.getElementById('hidden-paid-' + orderId);
            
            if (received === "" || parseFloat(received) === 0) {
                display.innerText = "Change: $0.00";
                display.classList.remove('change-invalid');
                hiddenInput.value = 0;
                return;
            }

            const change = parseFloat(received) - parseFloat(total);
            hiddenInput.value = received; 
            if (change >= 0) {
                display.innerText = "Change: $" + change.toFixed(2);
                display.classList.remove('change-invalid');
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
            } else {
                display.innerText = "Insufficient Amount";
                display.classList.add('change-invalid');
                submitBtn.disabled = true; 
                submitBtn.style.opacity = "0.5";
            }
        }
    </script>
</body>
</html>