<?php
// /checkout.php — Checkout page
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
requireLogin();

$cart  = getCart();
$total = getCartTotal();

if (empty($cart)) {
    redirect(BASE_URL . '/cart.php', 'Your cart is empty.', 'info');
}

$errors = [];
$flash  = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $fullName = trim($_POST['full_name'] ?? '');
    $address  = trim($_POST['address']   ?? '');
    $phone    = trim($_POST['phone']     ?? '');

    if (strlen($fullName) < 2) $errors[] = 'Full name is required.';
    if (strlen($address) < 5)  $errors[] = 'Address is required.';
    if (!preg_match('/^[\+\d\s\-\(\)]{7,20}$/', $phone)) $errors[] = 'Valid phone number is required.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, full_name, address, phone, total_amount) VALUES (?,?,?,?,?)");
            $stmt->execute([$_SESSION['user_id'], $fullName, $address, $phone, $total]);
            $orderId = $pdo->lastInsertId();

            // Insert order items
            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?,?,?,?,?)");
            foreach ($cart as $productId => $item) {
                $itemStmt->execute([$orderId, $productId, $item['name'], $item['quantity'], $item['price']]);
                // Decrease stock
                $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?")->execute([$item['quantity'], $productId, $item['quantity']]);
            }

            $pdo->commit();

            // Clear cart
            $_SESSION['cart'] = [];

            redirect(BASE_URL . '/order_success.php?id=' . $orderId, 'Order placed successfully!', 'success');
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Order failed. Please try again.';
        }
    }
}

$pageTitle = 'Checkout — ShopWave';
// Pre-fill user name
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<?php include 'includes/header.php'; ?>

<div class="container section">
    <h1 class="section-title" style="margin-bottom:40px;">Checkout</h1>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
        </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:48px;align-items:start;">
        <div>
            <form method="POST" action="<?= BASE_URL ?>/checkout.php">
                <?php csrf_field(); ?>

                <div style="background:var(--white);border:1px solid var(--light);border-radius:8px;padding:36px;">
                    <h3 style="font-size:28px;margin-bottom:28px;">Delivery Details</h3>

                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control"
                            value="<?= htmlspecialchars($_POST['full_name'] ?? $user['name'] ?? '') ?>"
                            placeholder="John Doe" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Delivery Address</label>
                        <textarea name="address" class="form-control" rows="3"
                            placeholder="123 Main Street, City, State, ZIP" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control"
                            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                            placeholder="+1 (555) 000-0000" required>
                    </div>
                </div>

                <div style="background:var(--white);border:1px solid var(--light);border-radius:8px;padding:36px;margin-top:24px;">
                    <h3 style="font-size:28px;margin-bottom:20px;">Payment</h3>
                    <div style="background:var(--cream);border-radius:6px;padding:20px;display:flex;align-items:center;gap:12px;">
                        <span style="font-size:24px;">💳</span>
                        <div>
                            <div style="font-weight:600;">Cash on Delivery</div>
                            <div style="font-size:13px;color:var(--mid);">Pay when your order arrives</div>
                        </div>
                        <span style="margin-left:auto;color:var(--success);">✓ Selected</span>
                    </div>
                </div>

                <div style="margin-top:28px;">
                    <button type="submit" class="btn btn-primary btn-block" style="padding:16px;">
                        Place Order — $<?= number_format($total, 2) ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Review -->
        <div class="cart-summary">
            <h3>Your Order</h3>

            <?php foreach ($cart as $id => $item): ?>
                <div class="summary-row" style="align-items:center;">
                    <div>
                        <div style="font-weight:500;font-size:14px;"><?= htmlspecialchars($item['name']) ?></div>
                        <div style="font-size:12px;color:var(--mid);">Qty: <?= $item['quantity'] ?></div>
                    </div>
                    <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </div>
            <?php endforeach; ?>

            <div class="summary-row" style="color:var(--mid);border-top:1px solid var(--light);padding-top:12px;margin-top:8px;">
                <span>Shipping</span>
                <span style="color:var(--success);font-weight:600;">Free</span>
            </div>

            <div class="summary-total">
                <span>Total</span>
                <span>$<?= number_format($total, 2) ?></span>
            </div>

            <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--light);">
                <a href="<?= BASE_URL ?>/cart.php" class="btn btn-outline btn-block btn-sm">← Edit Cart</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
