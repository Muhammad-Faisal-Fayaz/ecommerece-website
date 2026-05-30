-- ============================================================
-- ShopWave eCommerce Database
-- Import this file in phpMyAdmin or run via MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS shopwave_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shopwave_db;

-- ============================================================
-- USERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- PRODUCTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    category VARCHAR(100),
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- ORDERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cod', 'stripe') NOT NULL DEFAULT 'cod',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    stripe_session_id VARCHAR(255) NULL,
    pending_cart_json TEXT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- PASSWORD RESET OTP TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS password_reset_otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    otp_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_expires (user_id, expires_at)
);

-- ============================================================
-- ORDER ITEMS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- ============================================================
-- SAMPLE ADMIN USER (password: admin123)
-- ============================================================
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@shopwave.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================================
-- SAMPLE PRODUCTS
-- ============================================================
INSERT INTO products (name, description, price, stock, category, image) VALUES
('Premium Wireless Headphones', 'High-fidelity sound with active noise cancellation. 30-hour battery life and ultra-comfortable ear cushions.', 149.99, 25, 'Electronics', 'headphones.jpg'),
('Minimalist Leather Watch', 'Slim profile genuine leather strap watch. Water resistant to 50m. Available in 3 dial colors.', 89.99, 40, 'Accessories', 'watch.jpg'),
('Organic Cotton T-Shirt', 'Sustainably sourced 100% organic cotton. Pre-shrunk, relaxed fit. Available in 12 colors.', 34.99, 100, 'Clothing', 'tshirt.jpg'),
('Ceramic Pour-Over Coffee Set', 'Hand-crafted ceramic dripper with matching carafe. Makes 2–4 cups. Dishwasher safe.', 59.99, 30, 'Kitchen', 'coffee.jpg'),
('Mechanical Keyboard TKL', 'Tenkeyless layout with Cherry MX Red switches. RGB backlit. Aluminum top plate.', 129.99, 15, 'Electronics', 'keyboard.jpg'),
('Yoga Mat Pro', 'Extra-thick 6mm natural rubber mat with alignment lines. Non-slip, eco-friendly.', 49.99, 60, 'Sports', 'yoga.jpg');
