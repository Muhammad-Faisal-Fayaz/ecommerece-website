-- ShopWave v2 — use ONLY if upgrade_v2.sql fails (older MySQL without ADD COLUMN IF NOT EXISTS)
-- Run the whole file in phpMyAdmin SQL tab

USE shopwave_db;

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

-- Only run each block below if that column is missing (check Structure tab on orders table)

-- ALTER TABLE orders ADD COLUMN payment_method ENUM('cod', 'stripe') NOT NULL DEFAULT 'cod' AFTER total_amount;
-- ALTER TABLE orders ADD COLUMN payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending' AFTER payment_method;
-- ALTER TABLE orders ADD COLUMN stripe_session_id VARCHAR(255) NULL AFTER payment_status;

UPDATE orders SET payment_status = 'paid' WHERE payment_method = 'cod' AND payment_status = 'pending';
