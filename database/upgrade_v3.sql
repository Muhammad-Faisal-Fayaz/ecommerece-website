-- ShopWave v3: webhook cart snapshot for Stripe
-- Safe to re-run: skips column if it already exists

USE shopwave_db;

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS pending_cart_json TEXT NULL AFTER stripe_session_id;
