CREATE DATABASE IF NOT EXISTS furniture_store;
USE furniture_store;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert dummy categories
INSERT INTO categories (name, description, image_url) VALUES 
('Living Room', 'Comfortable and stylish furniture for your living space.', 'images/living-room.jpg'),
('Bedroom', 'Create your perfect sanctuary with our bedroom collection.', 'images/bedroom.jpg'),
('Dining Room', 'Gather around beautiful dining tables and chairs.', 'images/dining-room.jpg');

-- Insert dummy products
INSERT INTO products (category_id, name, description, price, image_url, stock) VALUES 
(1, 'Luxe Velvet Sofa', 'A stunning coffee-colored velvet sofa with gold-finished legs.', 899.99, 'images/sofa.jpg', 10),
(1, 'Gold Accent Coffee Table', 'Modern glass top coffee table with a geometric gold base.', 249.50, 'images/coffee-table.jpg', 15),
(2, 'Rich Espresso Bed Frame', 'Queen size bed frame in a rich espresso finish with subtle gold accents.', 599.00, 'images/bed.jpg', 8),
(2, 'Golden Glow Nightstand', 'Elegant nightstand with brass hardware and a deep coffee finish.', 129.99, 'images/nightstand.jpg', 20),
(3, 'Walnut Dining Table', 'Spacious dining table for six, crafted from rich walnut wood.', 649.00, 'images/dining-table.jpg', 5),
(3, 'Velvet Dining Chair', 'Set of two plush velvet dining chairs with slender gold legs.', 199.99, 'images/dining-chair.jpg', 24);
