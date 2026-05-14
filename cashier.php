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

function renderTableNumber($table_number) {
    if (preg_match('/^\s*Table\s+/i', $table_number)) {
        return htmlspecialchars($table_number);
    }
    return 'Table ' . htmlspecialchars($table_number);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Station | RMS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        .cashier-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }
        .cashier-table th,
        .cashier-table td {
            padding: 15px 18px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .cashier-table th {
            background: #f8fafc;
            color: var(--text-muted);
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .cashier-table tbody tr:hover {
            background: #f8fafc;
        }
        .item-list {
            display: grid;
            gap: 6px;
            font-size: 0.95rem;
            color: var(--dark);
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            background: rgba(59, 130, 246, 0.12);
            color: #2563eb;
        }
        .status-pill.ready { background: rgba(16, 185, 129, 0.12); color: #047857; }
        .status-pill.pending { background: rgba(249, 115, 22, 0.12); color: #c2410c; }
        .payment-box { background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .payment-input { width: 100%; max-width: 150px; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; margin: 8px 0; }
        .change-display { display: block; font-weight: 700; color: #16a34a; margin-top: 8px; }
        .change-invalid { color: #dc2626; }
        .btn-complete { width: 100%; margin-top: 10px; }
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
                <table class="cashier-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Table</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($o = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $o['id']; ?></td>
                            <td><?php echo renderTableNumber($o['table_number']); ?></td>
                            <td>
                                <div class="item-list">
                                <?php
                                $oid = $o['id'];
                                $items = $conn->query("SELECT oi.quantity, i.name FROM order_items oi JOIN items i ON oi.item_id=i.id WHERE oi.order_id=$oid");
                                while($item = $items->fetch_assoc()) {
                                    echo "<span>" . $item['quantity'] . "x " . htmlspecialchars($item['name']) . "</span>";
                                }
                                ?>
                                </div>
                            </td>
                            <td><strong style="color:var(--primary);">$<?php echo number_format($o['total_price'], 2); ?></strong></td>
                            <td><span class="status-pill <?php echo $o['status']; ?>"><?php echo strtoupper($o['status']); ?></span></td>
                            <td>
                                <?php if($o['status'] == 'ready'): ?>
                                <div class="payment-box">
                                    <label style="font-size: 0.85rem; font-weight: 600;">Amount Received</label>
                                    <input type="number" step="0.01" class="payment-input" placeholder="0.00"
                                           oninput="calculateChange(<?php echo $o['id']; ?>, <?php echo $o['total_price']; ?>, this.value)">
                                    <span class="change-display" id="change-display-<?php echo $o['id']; ?>">Change: $0.00</span>

                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <input type="hidden" name="amount_paid" id="hidden-paid-<?php echo $o['id']; ?>" value="0">
                                        <button type="submit" name="complete_order" id="btn-<?php echo $o['id']; ?>" class="btn btn-primary btn-complete" disabled>
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                    </form>
                                </div>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">Cooking in progress...</span>
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