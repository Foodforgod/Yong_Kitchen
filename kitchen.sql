DROP DATABASE IF EXISTS restaurant_db;


CREATE DATABASE restaurant_db;
USE restaurant_db;


CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100),
    stock INT DEFAULT 0,
    image_path VARCHAR(255)
);


CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10),
    total_price DECIMAL(10, 2),
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    item_id INT,
    quantity INT,
    remarks TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE SET NULL
);


INSERT INTO items (name, description, price, category, stock) VALUES 
('Classic Burger', 'Beef patty with lettuce and tomato', 8.00, 'Food', 100),
('Iced Latte', 'Cold brewed coffee with milk', 4.50, 'Drinks', 50),
('French Fries', 'Crispy golden fries', 3.00, 'Food', 80);
