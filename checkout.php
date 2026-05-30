<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_once 'includes/config.php';
require_once 'includes/orders.php';
require_once 'includes/payments.php';
requireLogin();

$cart  = getCart();
$total = getCartTotal();

if (empty($cart)) {
    redirect(BASE_URL . '/cart.php', 'Your cart is empty.', 'info');
}

$errors = [];
$stripeReady = config_is_ready('stripe');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $fullName = trim($_POST['full_name'] ?? '');
    $address  = trim($_POST['address']   ?? '');
    $phone    = trim($_POST['phone']     ?? '');
    $payment  = $_POST['payment_method'] ?? 'cod';

    if (strlen($fullName) < 2) $errors[] = 'Full name is required.';
    if (strlen($address) < 5)  $errors[] = 'Address is required.';
    if (!preg_match('/^[\+\d\s\-\(\)]{7,20}$/', $phone)) $errors[] = 'Valid phone number is required.';
    if (!in_array($payment, ['cod', 'stripe'], true)) $errors[] = 'Invalid payment method.';
    if ($payment === 'stripe' && !$stripeReady) {
        $errors[] = 'Card payments are not configured. Choose Cash on Delivery.';
    }

    if (empty($errors)) {
        try {
            if ($payment === 'cod') {
                $pdo->beginTransaction();
                $orderId = create_order($pdo, (int) $_SESSION['user_id'], $fullName, $address, $phone, $total, 'cod', 'paid');
                fulfill_order_items($pdo, $orderId, $cart);
                $pdo->commit();
                $_SESSION['cart'] = [];
                send_order_confirmation_email($pdo, $orderId);
                redirect(BASE_URL . '/order_success.php?id=' . $orderId, 'Order placed successfully!', 'success');
            }

            $pdo->beginTransaction();
            $orderId = create_order($pdo, (int) $_SESSION['user_id'], $fullName, $address, $phone, $total, 'stripe', 'pending');
            save_order_pending_cart($pdo, $orderId, $cart);
            $_SESSION['pending_checkout_cart'] = ['order_id' => $orderId, 'cart' => $cart];

            $session = create_stripe_checkout_session($orderId, $cart, $total, $_SESSION['email'] ?? '');
            if (!$session || empty($session->url)) {
                throw new RuntimeException('Could not start card payment.');
            }

            $pdo->prepare('UPDATE orders SET stripe_session_id = ? WHERE id = ?')->execute([$session->id, $orderId]);
            $pdo->commit();
            header('Location: ' . $session->url);
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            unset($_SESSION['pending_checkout_cart']);
            $errors[] = 'Checkout failed. Please try again.';
        }
    }
}

$pageTitle = 'Checkout — ShopWave';
$stmt = $pdo->prepare('SELECT name, email FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<?php include 'includes/header.php'; ?>

<?php ui_page_hero('Secure Checkout', 'Complete your order in a few steps', 'Checkout'); ?>

<div class="container page-content-tight">
    <?php if (!empty($errors)): ?>
        <div class="flash flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>

    <?php if (!$stripeReady): ?>
        <div class="flash flash-info" style="margin-bottom:24px;">Card payments need Stripe keys in <code>config/local.php</code>. COD works now.</div>
    <?php endif; ?>

    <?php ui_checkout_steps(2); ?>

    <div class="layout-sidebar">
        <div>
            <form method="POST" action="<?= BASE_URL ?>/checkout.php" id="checkout-form">
                <?php csrf_field(); ?>

                <?php ui_panel_start('Delivery Details', 'fa-solid fa-location-dot'); ?>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control"
                            value="<?= htmlspecialchars($_POST['full_name'] ?? $user['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control"
                            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                    </div>
                <?php ui_panel_end(); ?>

                <?php ui_panel_start('Payment Method', 'fa-solid fa-credit-card'); ?>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cod" checked>
                        <span class="payment-option-body">
                            <i class="fa-solid fa-truck"></i>
                            <span><strong>Cash on Delivery</strong><small>Pay when it arrives</small></span>
                        </span>
                    </label>
                    <label class="payment-option <?= $stripeReady ? '' : 'payment-option-disabled' ?>">
                        <input type="radio" name="payment_method" value="stripe" <?= $stripeReady ? '' : 'disabled' ?>>
                        <span class="payment-option-body">
                            <i class="fa-brands fa-stripe"></i>
                            <span><strong>Pay with Card</strong><small>Stripe secure checkout</small></span>
                        </span>
                    </label>
                <?php ui_panel_end(); ?>

                <button type="submit" class="btn btn-primary btn-block" style="padding:18px;" id="checkout-btn">
                    Place Order — $<?= number_format($total, 2) ?>
                </button>
            </form>
        </div>

        <aside class="cart-summary">
            <h3>Your Order</h3>
            <?php foreach ($cart as $item): ?>
                <div class="summary-row">
                    <span><?= htmlspecialchars($item['name']) ?> ×<?= $item['quantity'] ?></span>
                    <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row" style="border-top:1px solid rgba(255,255,255,0.1);padding-top:12px;">
                <span>Shipping</span><span style="color:var(--gold-light);">Free</span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span>$<?= number_format($total, 2) ?></span>
            </div>
            <a href="<?= BASE_URL ?>/cart.php" class="btn btn-outline btn-block btn-sm" style="margin-top:20px;border-color:rgba(255,255,255,0.2);color:var(--pearl);">← Edit Cart</a>
        </aside>
    </div>
</div>

<script>
document.querySelectorAll('input[name="payment_method"]').forEach(function (r) {
    r.addEventListener('change', function () {
        var b = document.getElementById('checkout-btn');
        b.textContent = this.value === 'stripe'
            ? 'Continue to Secure Payment — $<?= number_format($total, 2) ?>'
            : 'Place Order — $<?= number_format($total, 2) ?>';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
