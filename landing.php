<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/db.php';

// If already logged in, redirect appropriately
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: ' . BASE_URL . '/admin/index.php');
    } else {
        header('Location: ' . BASE_URL . '/index.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome — ShopWave</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/shopwave-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✦</text></svg>">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            background: #0a0a0a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(200, 169, 110, 0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(200, 169, 110, 0.08) 0%, transparent 40%),
                radial-gradient(ellipse at 60% 80%, rgba(200, 169, 110, 0.06) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }

        .landing-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 960px;
            padding: 40px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 60px;
        }

        /* Logo / Brand */
        .brand {
            text-align: center;
        }

        .brand-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(42px, 8vw, 72px);
            font-weight: 600;
            color: #fafaf8;
            letter-spacing: 6px;
            text-transform: uppercase;
            line-height: 1;
        }

        .brand-logo span { color: #c8a96e; }

        .brand-tagline {
            margin-top: 12px;
            font-size: 13px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #6b6b6b;
        }

        /* Divider */
        .divider {
            width: 40px;
            height: 1px;
            background: #c8a96e;
            margin: 8px auto 0;
            opacity: 0.5;
        }

        /* Cards */
        .cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            width: 100%;
        }

        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            padding: 48px 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(200, 169, 110, 0.05) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover {
            border-color: rgba(200, 169, 110, 0.4);
            background: rgba(200, 169, 110, 0.05);
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .card:hover::before { opacity: 1; }

        .card-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            transition: all 0.3s ease;
        }

        .card--user .card-icon {
            background: rgba(200, 169, 110, 0.12);
            color: #c8a96e;
            border: 1px solid rgba(200, 169, 110, 0.25);
        }

        .card--admin .card-icon {
            background: rgba(255, 255, 255, 0.06);
            color: #d8d4cb;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .card:hover .card-icon {
            transform: scale(1.1);
        }

        .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 400;
            color: #fafaf8;
            letter-spacing: 1px;
        }

        .card-desc {
            font-size: 13px;
            color: #6b6b6b;
            line-height: 1.6;
            letter-spacing: 0.3px;
        }

        .card-action {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #c8a96e;
            font-weight: 500;
            transition: gap 0.3s ease;
        }

        .card:hover .card-action { gap: 12px; }

        /* Footer note */
        .landing-footer {
            text-align: center;
            font-size: 12px;
            color: #3a3a3a;
            letter-spacing: 0.5px;
        }

        .landing-footer a {
            color: #6b6b6b;
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: color 0.2s;
        }

        .landing-footer a:hover { color: #c8a96e; }

        /* Responsive */
        @media (max-width: 600px) {
            .cards {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .card {
                padding: 36px 28px;
            }

            .landing-wrapper {
                gap: 40px;
            }
        }
    </style>
</head>
<body>

<div class="landing-wrapper">

    <div class="brand">
        <div class="brand-logo">Shop<span>Wave</span></div>
        <div class="divider"></div>
        <div class="brand-tagline">Premium Curated Commerce</div>
    </div>

    <div class="cards">

        <!-- User Card -->
        <a href="<?= BASE_URL ?>/user/login.php" class="card card--user">
            <div class="card-icon">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <div class="card-title">Customer</div>
                <div class="card-desc">Browse our curated collection,<br>manage orders &amp; your account.</div>
            </div>
            <div class="card-action">
                Login as Customer <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Admin Card -->
        <a href="<?= BASE_URL ?>/admin/index.php" class="card card--admin">
            <div class="card-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <div class="card-title">Admin</div>
                <div class="card-desc">Manage products, orders,<br>users &amp; store settings.</div>
            </div>
            <div class="card-action">
                Login as Admin <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

    </div>

    <div class="landing-footer">
        New customer? <a href="<?= BASE_URL ?>/user/register.php">Create an account</a>
    </div>

</div>

</body>
</html>
