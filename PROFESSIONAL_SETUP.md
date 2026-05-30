# ShopWave — Professional Features Setup

## 1. Database upgrades

Run in phpMyAdmin on `shopwave_db` (in order if upgrading an existing site):

1. `database/upgrade_v2.sql` — payments + OTP table  
2. `database/upgrade_v3.sql` — Stripe webhook cart snapshot  

New installs: use `database.sql` only.

## 2. Configuration

Copy `config/config.example.php` → `config/local.php` and set:

| Key | Purpose |
|-----|---------|
| `mail_*` | Gmail SMTP + App Password |
| `stripe_secret_key` | Card payments |
| `stripe_webhook_secret` | Webhook signature verification |
| `low_stock_threshold` | Admin low-stock alerts (default: 10) |

Test email: `http://localhost/ecommerece/send_mail.php`

## 3. Stripe webhooks (recommended)

Webhooks confirm orders even if the customer closes the browser after paying.

### Local development (Stripe CLI)

1. Install [Stripe CLI](https://stripe.com/docs/stripe-cli)
2. Run:
   ```bash
   stripe login
   stripe listen --forward-to localhost/ecommerece/payment/webhook.php
   ```
3. Copy the `whsec_...` secret into `config/local.php` as `stripe_webhook_secret`

### Production

1. Stripe Dashboard → Developers → Webhooks → Add endpoint  
2. URL: `https://yourdomain.com/ecommerece/payment/webhook.php`  
3. Events: `checkout.session.completed`, `checkout.session.expired`  
4. Paste signing secret into `config/local.php`

## 4. User profile

Logged-in customers: nav **Profile** → edit name/email or change password (current password required).

## 5. Invoice PDFs

For **paid** orders:

- Customer: My Orders or order success → **Download Invoice**  
- Admin: Order detail → **Invoice**  
- URL: `/invoice.php?id=ORDER_ID`

Uses Dompdf (installed via Composer).

## 6. Low-stock alerts

Admin **Dashboard** shows products with stock ≤ `low_stock_threshold`.  
**Products** list uses color badges: green / yellow (low) / red (out of stock).

## 7. Feature map

| Feature | Files |
|---------|--------|
| OTP password reset | `user/forgot-password.php`, `verify-otp.php`, `reset-password.php` |
| Stripe Checkout | `checkout.php`, `payment/stripe-success.php` |
| Stripe webhooks | `payment/webhook.php`, `includes/orders.php` → `complete_stripe_order()` |
| User profile | `user/profile.php` |
| Invoice PDF | `invoice.php`, `includes/invoice.php` |
| Stock alerts | `includes/inventory.php`, `admin/index.php` |

## 8. Test card (Stripe)

| Result | Number |
|--------|--------|
| Success | 4242 4242 4242 4242 |

Any future expiry, any CVC.

## 9. Security

- Never commit `config/local.php`  
- Use HTTPS in production  
- Rotate Gmail App Password if it was ever exposed in git  
