<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$revenue_query = "SELECT SUM(total_price) as total FROM orders WHERE status='completed' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$total_revenue = $conn->query($revenue_query)->fetch_assoc()['total'] ?? 0;

$order_count_query = "SELECT COUNT(*) as count FROM orders WHERE status='completed' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$total_orders = $conn->query($order_count_query)->fetch_assoc()['count'] ?? 0;

$history_query = "SELECT * FROM orders 
                  WHERE status = 'completed' 
                  AND DATE(created_at) BETWEEN '$start_date' AND '$end_date' 
                  ORDER BY created_at DESC";
$history = $conn->query($history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales History | RMS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <h2>RMS ADMIN</h2>
        <a href="admin.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="history.php" class="active"><i class="fas fa-history"></i> History</a> 
        <a href="kitchen.php"><i class="fas fa-utensils"></i> Kitchen</a>
        <a href="cashier.php"><i class="fas fa-cash-register"></i> Cashier</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <h1>Sales & Order History</h1>
            <div class="user-info">Report Period: <b><?php echo $start_date; ?></b> to <b><?php echo $end_date; ?></b></div>
        </div>

        <div class="card" style="margin-bottom: 25px;">
            <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label>From Date</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" style="margin-bottom:0; width: 100%;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label>To Date</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" style="margin-bottom:0; width: 100%;">
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="height: 45px; padding: 0 25px;">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="history.php" class="btn btn-danger" style="height: 45px; text-decoration:none; display: flex; align-items: center; justify-content: center; padding: 0 20px;">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <small>REVENUE (PERIOD)</small>
                <h2 style="color: var(--primary);">$<?php echo number_format($total_revenue, 2); ?></h2>
            </div>
            <div class="stat-card" style="border-left-color: #10b981;">
                <small>ORDERS COMPLETED</small>
                <h2><?php echo $total_orders; ?></h2>
            </div>
            <div class="stat-card" style="border-left-color: #f59e0b;">
                <small>AVG. TICKET SIZE</small>
                <h2>$<?php echo ($total_orders > 0) ? number_format($total_revenue / $total_orders, 2) : '0.00'; ?></h2>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-list"></i> Transaction Logs</h3>
            <div class="table-container">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 15px; text-align: left;">Date/Time</th>
                            <th style="padding: 15px; text-align: left;">Order ID</th>
                            <th style="padding: 15px; text-align: left;">Table</th>
                            <th style="padding: 15px; text-align: left;">Items Consumed</th>
                            <th style="padding: 15px; text-align: left;">Total</th>
                            <th style="padding: 15px; text-align: left;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($history && $history->num_rows > 0): ?>
                            <?php while($row = $history->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 15px;">
                                    <strong><?php echo date('M d, Y', strtotime($row['created_at'])); ?></strong><br>
                                    <small style="color: #64748b;"><?php echo date('h:i A', strtotime($row['created_at'])); ?></small>
                                </td>
                                <td style="padding: 15px;">#<?php echo $row['id']; ?></td>
                                <td style="padding: 15px;"><b><?php echo htmlspecialchars($row['table_number']); ?></b></td>
                                <td style="padding: 15px;">
                                    <?php
                                    $order_id = $row['id'];
                                    $items_query = "SELECT oi.quantity, i.name 
                                                   FROM order_items oi 
                                                   JOIN items i ON oi.item_id = i.id 
                                                   WHERE oi.order_id = $order_id";
                                    $items_result = $conn->query($items_query);
                                    while($item = $items_result->fetch_assoc()) {
                                        echo "<div style='font-size: 0.85rem;'>{$item['quantity']}x " . htmlspecialchars($item['name']) . "</div>";
                                    }
                                    ?>
                                </td>
                                <td style="padding: 15px;"><b style="color: #10b981;">$<?php echo number_format($row['total_price'], 2); ?></b></td>
                                <td style="padding: 15px;">
                                    <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold;">
                                        PAID
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 50px; color: #94a3b8;">
                                    <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                    No completed orders found for this date range.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>