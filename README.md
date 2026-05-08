🍽️ Restaurant Management System (RMS)
<br>
A clean, responsive, and functional PHP-based management system for restaurants. This system handles everything from menu management and 

order processing to real-time kitchen tracking and cashier checkouts.

<br>
🚀 Features
<br>

*Admin Dashboard: Manage menu items (CRUD), track live revenue, and monitor order history.

*Secure Authentication: Admin login and registration system using PHP Sessions and password_hash.

*Kitchen Display: Real-time view for chefs to see pending orders and mark them as "Ready."

*Cashier Station: Handle payments for orders coming out of the kitchen.

*Order Workflow:Pending (Kitchen) → Ready (Cashier) → Completed (Paid/Admin History).


<br>
🛠️ Tech Stack
<br>
*Backend: PHP 8.x

*Database: MySQL / PHPMYADMIN

*Frontend: HTML5, CSS3 (Flexbox/Grid), FontAwesome Icons

*Security: Prepared Statements (SQLi protection) and BCrypt Password Hashing.


<br>
📂 File Structure
<br>
├── db.php      # Database connection settings

├── login.php   # Admin login page

├── admin_register.php   # Create new admin accounts

├── admin.php             # Main dashboard & menu management

├── kitchen.php          # Kitchen order queue

├── cashier.php           # Payment processing station

├── logout.php           # Secure session termination

└── README.md            # Project documentation


<br>
⚙️ Installation & Setup
<br>
Follow these steps to get your Restaurant Management System (RMS) running on your local machine:
<br>
Step 1: Environment Setup
<br>
*Ensure you have a local server environment installed (like XAMPP, WAMP, or MAMP).

*Start the Apache and MySQL modules from your control panel.
<br>
Step 2: Project Deployment
<br>
*Create a folder named rms inside your server's root directory (e.g., C:/xampp/htdocs/rms).

*Copy all the project files into this folder.
<br>
Step 3: Database Configuration
<br>
1.Open your browser and go to http://localhost/phpmyadmin.

2.Click on the New tab on the left sidebar and create a database named restaurant_db.

3.Click on your new database, then click the SQL tab at the top.

4.Copy the SQL code below and paste it into the box, then click Go:
<br>
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2),
    category ENUM('Food', 'Drinks'),
    stock INT,
    image_path VARCHAR(255)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'ready', 'completed') DEFAULT 'pending'
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    item_id INT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);
<br>
Step 4: Database Connection
<br>
*Open the db.php file in your text editor.

*Ensure the credentials match your local setup (usually root with no password for XAMPP).
<br>
Step 5: Admin Registration
<br>
1.Open your browser and navigate to http://localhost:8080/admin_register.php

2.Create your username and password.

3.Important: For security, delete or rename admin_register.php after your account is created.
<br>
Step 6: Launch System
<br>
*Go to http://localhost:8080/login.php

*Log in with your new credentials to access the Admin Dashboard.
    
    

🔒 Security Notes

*This system uses Prepared Statements to prevent SQL Injection.

*Passwords are never stored in plain text; they are encrypted using PASSWORD_DEFAULT.

*The admin.php page is protected by a session guard; unauthorized users are redirected to the login page.


