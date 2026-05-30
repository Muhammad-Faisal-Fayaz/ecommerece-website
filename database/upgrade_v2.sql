-- ShopWave v2 upgrade: OTP password reset + online payments
-- Safe to re-run: skips columns/tables that already exist (MariaDB / MySQL 8+)

USE shopwave_db;

-- Password reset OTPs
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

-- Payment columns on orders (IF NOT EXISTS = no error if already added)
ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS payment_method ENUM('cod', 'stripe') NOT NULL DEFAULT 'cod' AFTER total_amount;

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending' AFTER payment_method;

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS stripe_session_id VARCHAR(255) NULL AFTER payment_status;

-- Existing COD orders should show as paid (safe to run again)
UPDATE orders SET payment_status = 'paid' WHERE payment_method = 'cod' AND payment_status = 'pending';
