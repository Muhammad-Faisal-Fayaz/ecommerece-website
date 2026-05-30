<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/payments.php';
require_once '../includes/orders.php';
requireLogin();

$sessionId = trim($_GET['session_id'] ?? '');
if ($sessionId === '') {
    redirect(BASE_URL . '/cart.php', 'Invalid payment session.', 'error');
}

$session = retrieve_stripe_checkout_session($sessionId);
if (!$session || $session->payment_status !== 'paid') {
    redirect(BASE_URL . '/cart.php', 'Payment was not completed.', 'error');
}

$orderId = (int) ($session->metadata->order_id ?? 0);
if ($orderId <= 0) {
    redirect(BASE_URL . '/cart.php', 'Order not found.', 'error');
}

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect(BASE_URL . '/cart.php', 'Order not found.', 'error');
}

if ($order['payment_status'] === 'paid') {
    redirect(BASE_URL . '/order_success.php?id=' . $orderId);
}

if (!complete_stripe_order($pdo, $orderId, $sessionId)) {
    redirect(BASE_URL . '/user/orders.php', 'Payment received but order update failed. Contact support.', 'error');
}

$_SESSION['cart'] = [];
unset($_SESSION['pending_checkout_cart']);

redirect(BASE_URL . '/order_success.php?id=' . $orderId, 'Payment successful! Order confirmed.', 'success');
