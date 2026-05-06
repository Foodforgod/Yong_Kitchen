<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation | Restaurant Management System</title>
    <style>
        :root { --primary: #2563eb; --dark: #1e293b; --text: #334155; --bg: #f8fafc; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: var(--text); background: var(--bg); margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 40px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { color: var(--dark); border-bottom: 3px solid var(--primary); padding-bottom: 10px; }
        h2 { color: var(--primary); margin-top: 30px; display: flex; align-items: center; gap: 10px; }
        code, pre { background: #f1f5f9; padding: 15px; border-radius: 6px; display: block; overflow-x: auto; color: #e11d48; font-weight: bold; }
        .file-tree { list-style: none; padding-left: 10px; font-family: monospace; }
        .step { background: #eff6ff; border-left: 4px solid var(--primary); padding: 15px; margin: 15px 0; border-radius: 0 8px 8px 0; }
        .badge { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>🍽️ Restaurant Management System (RMS)</h1>
    <p>A clean, responsive, and functional PHP-based management system for restaurants. This system handles everything from menu management to real-time kitchen tracking and cashier checkouts.</p>

    <h2>🚀 Features</h2>
    <ul>
        <li><strong>Admin Dashboard:</strong> CRUD menu management and live revenue tracking.</li>
        <li><strong>Secure Auth:</strong> Session-based login with BCrypt hashing.</li>
        <li><strong>Kitchen Display:</strong> Real-time pending order queue.</li>
        <li><strong>Cashier Station:</strong> Payment processing for "Ready" orders.</li>
    </ul>

    <h2>📂 File Structure</h2>
    <ul class="file-tree">
        <li>├── db.php (DB Connection)</li>
        <li>├── login.php (Admin Login)</li>
        <li>├── admin_register.php (Account Creation)</li>
        <li>├── admin.php (Main Dashboard)</li>
        <li>├── kitchen.php (Chef View)</li>
        <li>├── cashier.php (Payment View)</li>
        <li>├── logout.php (Secure Exit)</li>
    </ul>

    <h2>⚙️ Installation & Setup</h2>
    
    <div class="step">
        <strong>Step 1: Environment</strong><br>
        Install XAMPP/WAMP and start Apache & MySQL.
    </div>

    <div class="step">
        <strong>Step 2: Project Folder</strong><br>
        Move all files into <code>C:/xampp/htdocs/rms/</code>.
    </div>

    <div class="step">
        <strong>Step 3: Database Setup</strong><br>
        1. Go to <code>localhost/phpmyadmin</code>.<br>
        2. Create a database named <strong>restaurant_db</strong>.<br>
        3. Run this SQL code in the SQL tab:
        <pre>
CREATE TABLE items ( id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), description TEXT, price DECIMAL(10,2), category ENUM('Food', 'Drinks'), stock INT, image_path VARCHAR(255) );

CREATE TABLE orders ( id INT AUTO_INCREMENT PRIMARY KEY, table_number INT, total_price DECIMAL(10,2), status ENUM('pending', 'ready', 'completed') DEFAULT 'pending' );

CREATE TABLE order_items ( id INT AUTO_INCREMENT PRIMARY KEY, order_id INT, item_id INT, FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE, FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE );

CREATE TABLE admins ( id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) UNIQUE, password VARCHAR(255) );</pre>
    </div>

    <div class="step">
        <strong>Step 4: Admin Access</strong><br>
        1. Navigate to <code>localhost/rms/admin_register.php</code> to create an account.<br>
        2. <strong>Security:</strong> Delete <code>admin_register.php</code> after use.
    </div>

    <h2>🔒 Security Notes</h2>
    <p>This system uses <strong>Prepared Statements</strong> to prevent SQL Injection and <strong>Password Hashing</strong> for credential safety. <span class="badge">SECURE</span></p>
</div>

</body>
</html>

