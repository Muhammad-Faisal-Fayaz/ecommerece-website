# ShopWave — eCommerce Application
## Setup Guide for XAMPP

---

## 📁 File Structure

```
/shopwave/
├── index.php               ← Homepage (product listing)
├── product.php             ← Product detail page
├── cart.php                ← Shopping cart
├── cart_action.php         ← Cart operations (add/remove/update)
├── checkout.php            ← Checkout form
├── order_success.php       ← Order confirmation
├── database.sql            ← Import this into MySQL
│
├── css/
│   └── style.css           ← Main stylesheet
│
├── js/
│   └── main.js             ← Main JavaScript
│
├── images/
│   └── products/           ← Uploaded product images (writable!)
│
├── includes/
│   ├── db.php              ← PDO database connection
│   ├── auth.php            ← Auth helpers, cart functions
│   ├── csrf.php            ← CSRF token protection
│   ├── header.php          ← Global header/nav
│   └── footer.php          ← Global footer
│
├── user/
│   ├── login.php           ← User login
│   ├── logout.php          ← Logout handler
│   ├── register.php        ← User registration
│   └── orders.php          ← User order history
│
└── admin/
    ├── index.php           ← Admin dashboard
    ├── _sidebar.php        ← Admin navigation
    ├── products/
    │   ├── index.php       ← List products
    │   ├── create.php      ← Add product
    │   ├── edit.php        ← Edit product
    │   └── delete.php      ← Delete product
    ├── orders/
    │   ├── index.php       ← All orders
    │   └── view.php        ← Order detail + status update
    └── users/
        └── index.php       ← User list
```

---

## ⚡ Quick Setup (XAMPP)

### Step 1 — Copy files
Place the `shopwave` folder in your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\shopwave\
```

### Step 2 — Start XAMPP
- Open XAMPP Control Panel
- Start **Apache** and **MySQL**

### Step 3 — Import the database
1. Open **http://localhost/phpmyadmin**
2. Click **"New"** to create a database named `shopwave_db`
3. Click **"Import"** → Choose `database.sql` → Click **Go**

   _Or run via MySQL CLI:_
   ```sql
   mysql -u root -p < shopwave/database.sql
   ```

### Step 4 — Configure database
Open `includes/db.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');    // Your MySQL username
define('DB_PASS', '');        // Your MySQL password (blank by default in XAMPP)
define('DB_NAME', 'shopwave_db');
```

### Step 5 — Set folder permissions
Ensure the images folder is writable:
```
shopwave/images/products/   ← Must be writable for image uploads
```
On Windows with XAMPP this is automatic. On Linux/Mac:
```bash
chmod 755 images/products/
```

### Step 6 — Browse the app
| URL | Page |
|-----|------|
| http://localhost/shopwave/ | Homepage |
| http://localhost/shopwave/user/login.php | Login |
| http://localhost/shopwave/user/register.php | Register |
| http://localhost/shopwave/admin/ | Admin Panel |

---

## 🔐 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@shopwave.com | admin123 |

> ⚠️ **Change this password immediately in production!**
> In phpMyAdmin, update the `password` field with a new bcrypt hash.

---

## 🗄️ Database Tables

| Table | Purpose |
|-------|---------|
| `users` | Registered customers + admin |
| `products` | Product catalog |
| `orders` | Customer orders (header) |
| `order_items` | Individual items per order |

---

## 🛡️ Security Features

- **Passwords**: Bcrypt hashed via `password_hash()`
- **SQL Injection**: PDO prepared statements everywhere
- **CSRF**: Token-based protection on all forms
- **XSS**: `htmlspecialchars()` on all output
- **Input Validation**: Server-side validation on all inputs
- **File Upload**: Extension whitelist, size limits, randomized filenames
- **Auth Guards**: `requireLogin()` and `requireAdmin()` middleware

---

## 🔧 Troubleshooting

**Blank page / errors?**
- Enable PHP errors: add `ini_set('display_errors', 1);` temporarily to `includes/db.php`
- Check Apache error logs in XAMPP

**Image uploads not working?**
- Check that `images/products/` directory exists and is writable
- Verify `upload_max_filesize` in `php.ini` (set to at least 5M)

**Database connection failed?**
- Ensure MySQL is running in XAMPP
- Verify credentials in `includes/db.php`
- Confirm the `shopwave_db` database was imported

---

## 🚀 Production Deployment

1. Change DB credentials
2. Set a strong secret for sessions
3. Move `includes/` outside web root or protect with `.htaccess`
4. Enable HTTPS
5. Set `display_errors = Off` in `php.ini`
6. Remove the demo admin hint from `login.php`
