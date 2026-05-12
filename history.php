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

$most_ordered_query = "SELECT i.name, SUM(oi.quantity) as total_qty 
                       FROM order_items oi 
                       JOIN items i ON oi.item_id = i.id 
                       JOIN orders o ON oi.order_id = o.id 
                       WHERE o.status = 'completed' 
                       AND DATE(o.created_at) BETWEEN '$start_date' AND '$end_date' 
                       GROUP BY i.id 
                       ORDER BY total_qty DESC 
                       LIMIT 1";
$most_ordered_result = $conn->query($most_ordered_query)->fetch_assoc();
$popular_item = $most_ordered_result['name'] ?? 'N/A';
$popular_qty = $most_ordered_result['total_qty'] ?? 0;

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
            <div class="user-info">Period: <b><?php echo $start_date; ?></b> to <b><?php echo $end_date; ?></b></div>
        </div>

        <div class="card" style="margin-bottom: 25px;">
            <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 150px;">
                    <label>From Date</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" style="width:100%">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label>To Date</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" style="width:100%">
                </div>
                <button type="submit" class="btn btn-primary" style="height:40px;">Filter</button>
                <a href="history.php" class="btn btn-danger" style="height:40px; text-decoration:none; display:flex; align-items:center;">Reset</a>
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
                <small>MOST ORDERED FOOD</small>
                <h2 style="font-size: 1.2rem;"><?php echo htmlspecialchars($popular_item); ?></h2>
                <small style="color: #64748b;"><?php echo $popular_qty; ?> sold</small>
            </div>
        </div>

        <div class="card">
            <h3>Transaction Logs</h3>
            <div class="table-container">
                <table style="width: 100%;">
                    <thead>
                        <tr style="background: #f8fafc; text-align: left;">
                            <th style="padding: 15px;">Date/Time</th>
                            <th style="padding: 15px;">Order ID</th>
                            <th style="padding: 15px;">Table</th>
                            <th style="padding: 15px;">Paid / Change</th>
                            <th style="padding: 15px;">Total</th>
                            <th style="padding: 15px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $history->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 15px;">
                                <strong><?php echo date('M d, Y', strtotime($row['created_at'])); ?></strong><br>
                                <small><?php echo date('h:i A', strtotime($row['created_at'])); ?></small>
                            </td>
                            <td style="padding: 15px;">#<?php echo $row['id']; ?></td>
                            <td style="padding: 15px;">Table <?php echo htmlspecialchars($row['table_number']); ?></td>
                            <td style="padding: 15px;">
                                <div style="font-size: 0.8rem;">Paid: $<?php echo number_format($row['amount_paid'], 2); ?></div>
                                <div style="font-size: 0.8rem; color: #64748b;">Change: $<?php echo number_format($row['amount_paid'] - $row['total_price'], 2); ?></div>
                            </td>
                            <td style="padding: 15px;"><b>$<?php echo number_format($row['total_price'], 2); ?></b></td>
                            <td style="padding: 15px;">
                                <a href="receipt.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-primary" style="padding:5px 10px; font-size: 0.8rem;">Receipt</a>
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