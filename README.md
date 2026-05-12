🍽️ Restaurant Management System (RMS)           <br><br>Group Programming Design Member:Yong Gene,Zhi Bin,Zhan Ming                  

A professional-grade, PHP-based web application designed to streamline restaurant operations. This system manages the full lifecycle of an order—from the customer's initial selection to kitchen preparation, final checkout, and sales reporting.

---

## 📂 System Architecture & Workflow

The system is divided into three distinct modules that interact in real-time:

1.  **Customer Module (`order_index.php`):** Browse menu, filter by category, and add items to a session-based cart.
2.  **Kitchen Module (`kitchen.php`):** Real-time display of pending orders with item-by-item status toggling.
3.  **Admin/Cashier Module (`admin.php`, `cashier.php`):** Inventory management, payment processing, and revenue analytics.

---

## 🚀 Key Features

### 🛒 Ordering & Cart
* **Dynamic Menu:** Automatically hides out-of-stock items.
* **Table Assignment:** Supports 15 unique tables for precise service.
* **Custom Remarks:** Customers can add special instructions (e.g., "no onions") per item.

### 👨‍🍳 Kitchen Workflow
* **AJAX Item Toggling:** Chefs can mark individual dishes as "Done" without refreshing the page.
* **Ready Logic:** Orders can only be sent to the cashier once every item in that order is marked finished.
* **Auto-Refresh:** The kitchen display updates every 30 seconds to ensure no order is missed.

### 💳 Cashier & Finance
* **Change Calculator:** Built-in JS logic to prevent mathematical errors during checkout.
* **Printable Receipts:** Clean, "receipt-style" layout for physical printing.
* **Sales History:** Filterable reports showing total revenue and most popular items within specific date ranges.

---

## 🛠️ Technical Stack

* **Backend:** PHP 8.1+
* **Database:** MySQL (MariaDB)
* **Frontend:** Vanilla JS (Fetch API), CSS3 (Flex/Grid), FontAwesome 6.0
* **Authentication:** Session-based with BCrypt password hashing.

---

## 📋 Database Schema

The system relies on a relational database (`restaurant_db`). Key tables include:

* **`items`**: Stores name, price, category, stock, and image paths.
* **`orders`**: Tracks table numbers, total price, and status (`pending`, `ready`, `completed`).
* **`order_items`**: Junction table linking orders to items with specific quantities and status.
* **`admins`**: Stores encrypted credentials for staff access.

---

## ⚙️ Setup Instructions

1.  **Server Requirements:** Install XAMPP, WAMP, or any environment supporting PHP 8.x and MySQL.
2.  **Database Import:**
    * Create a database named `restaurant_db`.
    * Import the provided `restaurant_db.sql` file.
3.  **Connection:**
    * Update `db.php` with your local database credentials (host, user, password).
4.  **Admin Setup:**
    * Navigate to `admin_register.php` to create the first admin account.
    * **⚠️ SECURITY:** Delete `admin_register.php` from the server immediately after registration.
5.  **Accessing the System:**
    * **Customer Menu:** `order_index.php`
    * **Staff Login:** `login.php`

---

## 🔒 Security Measures
* **Prepared Statements:** Utilized in critical areas to prevent SQL Injection.
* **Session Validation:** Admin pages (`admin.php`, `history.php`, etc.) check for active login sessions.
* **Inventory Protection:** Logic prevents ordering more items than what is currently in stock.
