<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

if ($orderId) {
    $stmt = $pdo->prepare(
        'SELECT id FROM orders WHERE id = ? AND user_id = ? AND payment_status = ?'
    );
    $stmt->execute([$orderId, $_SESSION['user_id'], 'pending']);
    if ($stmt->fetch()) {
        $pdo->prepare(
            'UPDATE orders SET payment_status = ?, status = ? WHERE id = ?'
        )->execute(['failed', 'cancelled', $orderId]);
    }
}

unset($_SESSION['pending_checkout_cart']);

redirect(BASE_URL . '/checkout.php', 'Payment cancelled. Your cart is still available.', 'info');
