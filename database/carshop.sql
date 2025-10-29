-- Create database
CREATE DATABASE IF NOT EXISTS carshop_db;
USE carshop_db;

-- Roles Table
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL
);

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inventory Logs (Audit Trail)
CREATE TABLE inventory_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    action VARCHAR(50),
    quantity_change INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert default roles
INSERT INTO roles (role_name) VALUES ('Admin'), ('User');

-- Insert admin account (username: admin, password: admin123)
INSERT INTO users (username, password, role_id) 
VALUES ('admin', MD5('admin123'), 1);

-- Insert sample user accounts
INSERT INTO users (username, password, role_id) 
VALUES ('user1', MD5('user123'), 2),
       ('user2', MD5('user123'), 2);

-- Insert sample products
INSERT INTO products (name, category, price, quantity) VALUES
('Brake Pads', 'Car Parts', 25.50, 10),
('Engine Oil', 'Car Parts', 40.00, 5),
('Car Battery', 'Car Parts', 80.00, 3),
('Car Wax', 'Accessories', 15.00, 20),
('Seat Cover', 'Accessories', 50.00, 8),
('Toyota Corolla 2020', 'Vehicles', 18000.00, 2),
('Honda Civic 2019', 'Vehicles', 17000.00, 1);

-- Insert sample inventory logs
INSERT INTO inventory_logs (product_id, action, quantity_change) VALUES
(1, 'Added new product', 10),
(3, 'Updated quantity', -2),
(5, 'Deleted product', -1);
