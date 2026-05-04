<?php
include 'db.php';


if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = $id");
}

$orders = $conn->query("SELECT * FROM orders WHERE status = 'pending'");
?>

<h1>Kitchen View</h1>
<?php while($row = $orders->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
        <h3>Order #<?php echo $row['id']; ?> - Table <?php echo $row['table_number']; ?></h3>
        <a href="?complete=<?php echo $row['id']; ?>">Mark as Completed (Delete)</a>
    </div>
<?php endwhile; ?>
