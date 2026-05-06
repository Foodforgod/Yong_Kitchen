<?php
include 'db.php';

$message = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $message = "Username already taken!";
        } else {
            
            $insert = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $hashed_password);
            
            if ($insert->execute()) {
                
                header("Location: login.php?registered=1");
                exit();
            } else {
                $message = "Error creating account.";
            }
        }
    } else {
        $message = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration | RMS</title>
    <style>
        :root { --primary: #2563eb; --dark: #1e293b; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 360px; box-sizing: border-box; }
        h2 { margin-top: 0; color: var(--dark); text-align: center; }
        label { font-size: 0.85rem; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn { background: var(--primary); color: white; border: none; width: 100%; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; }
        .msg { color: #ef4444; font-size: 0.85rem; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Register Admin</h2>
        
        <?php if(!empty($message)) echo "<div class='msg'>$message</div>"; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Choose a username" required>
            
            <label>Password</label>
            <input type="password" name="password" placeholder="Choose a password" required>
            
            <button type="submit" name="register" class="btn">Create Account</button>
        </form>
        
        <p style="text-align:center; font-size:0.85rem; margin-top:20px;">
            <a href="login.php" style="color:var(--primary); text-decoration:none;">Already have an account? Login</a>
        </p>
    </div>

</body>
</html>
