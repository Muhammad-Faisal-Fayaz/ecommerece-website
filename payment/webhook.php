<?php
/**
 * Stripe webhook endpoint — confirms payments even if the user closes the browser.
 * URL: /ecommerece/payment/webhook.php
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/payments.php';
require_once __DIR__ . '/../includes/orders.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$payload = file_get_contents('php://input') ?: '';
$signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if (!config_is_ready('stripe_webhook')) {
    http_response_code(503);
    echo json_encode(['error' => 'Webhook not configured']);
    exit;
}

$event = verify_stripe_webhook($payload, $signature);
if (!$event) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;
        if ($session->payment_status === 'paid') {
            $orderId = (int) ($session->metadata->order_id ?? 0);
            if ($orderId > 0) {
                complete_stripe_order($pdo, $orderId, $session->id);
            }
        }
        break;

    case 'checkout.session.expired':
        $session = $event->data->object;
        $orderId = (int) ($session->metadata->order_id ?? 0);
        if ($orderId > 0) {
            $pdo->prepare(
                'UPDATE orders SET payment_status = ?, status = ? WHERE id = ? AND payment_status = ?'
            )->execute(['failed', 'cancelled', $orderId, 'pending']);
        }
        break;
}

http_response_code(200);
echo json_encode(['received' => true]);
