<?php
session_start();
include 'db.php';


if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

$error = "";
$success = "";


if (isset($_GET['registered'])) {
    $success = "Account created! You can now log in.";
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $row['username'];
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin account not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | RMS</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --primary: #2563eb; --dark: #1e293b; --bg: #f8fafc; --danger: #ef4444; --success: #10b981; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 360px; box-sizing: border-box; }
        h2 { margin-top: 0; color: var(--dark); text-align: center; margin-bottom: 25px; }
        label { font-size: 0.85rem; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn { background: var(--primary); color: white; border: none; width: 100%; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .error-msg { background: #fee2e2; color: var(--danger); padding: 10px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 20px; text-align: center; }
        .success-msg { background: #dcfce7; color: var(--success); padding: 10px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 20px; text-align: center; }
        .footer-link { text-align: center; margin-top: 20px; font-size: 0.85rem; color: #64748b; }
        .footer-link a { color: var(--primary); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>RMS Admin Login</h2>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" required>
            
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
            
            <button type="submit" name="login" class="btn">Login to Dashboard</button>
        </form>

        
        <div class="footer-link">
            Don't have an account? <br>
            <a href="admin_register.php">Create Admin Account</a>
        </div>
    </div>

</body>
</html>


