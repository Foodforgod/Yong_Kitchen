🍽️ Restaurant Management System (RMS)

A clean, responsive, and functional PHP-based management system for restaurants. This system handles everything from menu management and 

order processing to real-time kitchen tracking and cashier checkouts.

🚀 Features

*Admin Dashboard: Manage menu items (CRUD), track live revenue, and monitor order history.

*Secure Authentication: Admin login and registration system using PHP Sessions and password_hash.

*Kitchen Display: Real-time view for chefs to see pending orders and mark them as "Ready."

*Cashier Station: Handle payments for orders coming out of the kitchen.

*Order Workflow:Pending (Kitchen) → Ready (Cashier) → Completed (Paid/Admin History).


🛠️ Tech Stack

*Backend: PHP 8.x

*Database: MySQL / PHPMYADMIN

*Frontend: HTML5, CSS3 (Flexbox/Grid), FontAwesome Icons

*Security: Prepared Statements (SQLi protection) and BCrypt Password Hashing.


📂 File Structure

├── db.php      # Database connection settings

├── login.php   # Admin login page

├── admin_register.php   # Create new admin accounts

├── admin.php             # Main dashboard & menu management

├── kitchen.php          # Kitchen order queue

├── cashier.php           # Payment processing station

├── logout.php           # Secure session termination

└── README.md            # Project documentation


⚙️ Installation

1.Clone the project into your local server folder (e.g., htdocs for XAMPP).

2. Database Setup:
   
   1. Create a database named `restaurant_db`.
      
   2. Run the following SQL in the **SQL tab** of phpMyAdmin to create the tables:
      

   ```sql
   CREATE TABLE items (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       description TEXT,
       price DECIMAL(10,2) NOT NULL,
       category ENUM('Food', 'Drinks') DEFAULT 'Food',
       stock INT DEFAULT 0,
       image_path VARCHAR(255)
   );

   CREATE TABLE orders (
       id INT AUTO_INCREMENT PRIMARY KEY,
       table_number INT NOT NULL,
       total_price DECIMAL(10,2) NOT NULL,
       status ENUM('pending', 'ready', 'completed') DEFAULT 'pending',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE order_items (
       id INT AUTO_INCREMENT PRIMARY KEY,
       order_id INT,
       item_id INT,
       FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
   );

   CREATE TABLE admins (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL
   );
   ```

3.Configure Connection:

    1.Open db.php and update your database credentials (host, username, password).
    
4.Register First Admin:

    1.Navigate to admin_register.php in your browser.
    
    2.Create your first account, then delete this file for security.
    
5.Start Managing:

    1.Login via login.php to access the dashboard.
    
    
🔒 Security Notes

*This system uses Prepared Statements to prevent SQL Injection.

*Passwords are never stored in plain text; they are encrypted using PASSWORD_DEFAULT.

*The admin.php page is protected by a session guard; unauthorized users are redirected to the login page.


