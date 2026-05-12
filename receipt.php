<?php
include 'db.php';
if (!isset($_GET['id'])) die("Order ID missing.");
$order_id = intval($_GET['id']);
$order_query = $conn->query("SELECT * FROM orders WHERE id = $order_id");
$order = $order_query->fetch_assoc();
if (!$order) die("Order not found.");

$items = $conn->query("SELECT oi.quantity, i.price, i.name 
                       FROM order_items oi 
                       JOIN items i ON oi.item_id = i.id 
                       WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt_#<?php echo $order_id; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #f4f4f4; padding: 20px; }
        .receipt-container { background: #fff; width: 300px; margin: 0 auto; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; font-size: 14px; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        @media print { body { background: none; padding: 0; } .receipt-container { box-shadow: none; width: 100%; margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; margin-bottom: 20px;">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="receipt-container">
        <div class="center">
            <h2 style="margin:0;">RMS RESTO</h2>
            <p style="font-size:12px;">123 Street Name, Your City</p>
        </div>
        <div class="divider"></div>
        <p style="font-size:14px;">
            <b>ID:</b> #<?php echo $order['id']; ?><br>
            <b>Table:</b> <?php echo htmlspecialchars($order['table_number']); ?><br>
            <b>Date:</b> <?php echo date('d-m-Y H:i', strtotime($order['created_at'])); ?>
        </p>
        <div class="divider"></div>
        <table>
            <?php while($item = $items->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['quantity']; ?>)</td>
                <td align="right">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <div class="divider"></div>
        
        <table>
            <tr>
                <td class="bold">GRAND TOTAL</td>
                <td class="text-right bold">$<?php echo number_format($order['total_price'], 2); ?></td>
            </tr>
            <tr>
                <td>Amount Paid</td>
                <td class="text-right">$<?php echo number_format($order['amount_paid'], 2); ?></td>
            </tr>
            <tr>
                <td class="bold">CHANGE</td>
                <td class="text-right bold">$<?php echo number_format($order['amount_paid'] - $order['total_price'], 2); ?></td>
            </tr>
        </table>

        <div class="divider" style="margin-top:20px;"></div>
        <div class="center">
            <p>Thank you for dining with us!</p>
        </div>
    </div>
</body>
</html>