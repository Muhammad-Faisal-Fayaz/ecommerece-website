<?php
// /includes/payments.php — Stripe Checkout integration

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

function stripe_client(): ?\Stripe\StripeClient
{
    if (!config_is_ready('stripe')) {
        return null;
    }
    return new \Stripe\StripeClient(app_config('stripe_secret_key'));
}

function create_stripe_checkout_session(
    int $orderId,
    array $cart,
    float $total,
    string $customerEmail
): ?\Stripe\Checkout\Session {
    $stripe = stripe_client();
    if (!$stripe) {
        return null;
    }

    $currency = strtolower(app_config('stripe_currency', 'usd'));
    $lineItems = [];

    foreach ($cart as $item) {
        $lineItems[] = [
            'price_data' => [
                'currency'     => $currency,
                'unit_amount'  => (int) round($item['price'] * 100),
                'product_data' => [
                    'name' => $item['name'],
                ],
            ],
            'quantity' => (int) $item['quantity'],
        ];
    }

    if (empty($lineItems)) {
        return null;
    }

    $base = defined('BASE_URL') ? BASE_URL : '';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $successUrl = $protocol . '://' . $host . $base . '/payment/stripe-success.php?session_id={CHECKOUT_SESSION_ID}';
    $cancelUrl  = $protocol . '://' . $host . $base . '/payment/stripe-cancel.php?order_id=' . $orderId;

    return $stripe->checkout->sessions->create([
        'mode'                => 'payment',
        'payment_method_types'=> ['card'],
        'line_items'          => $lineItems,
        'customer_email'      => $customerEmail,
        'success_url'         => $successUrl,
        'cancel_url'          => $cancelUrl,
        'metadata'            => [
            'order_id' => (string) $orderId,
        ],
    ]);
}

function verify_stripe_webhook(string $payload, string $signature): ?\Stripe\Event
{
    $secret = app_config('stripe_webhook_secret');
    if (!$secret) {
        return null;
    }

    try {
        return \Stripe\Webhook::constructEvent($payload, $signature, $secret);
    } catch (\Exception $e) {
        error_log('Stripe webhook verify failed: ' . $e->getMessage());
        return null;
    }
}

function retrieve_stripe_checkout_session(string $sessionId): ?\Stripe\Checkout\Session
{
    $stripe = stripe_client();
    if (!$stripe) {
        return null;
    }

    try {
        return $stripe->checkout->sessions->retrieve($sessionId);
    } catch (\Exception $e) {
        error_log('Stripe session retrieve failed: ' . $e->getMessage());
        return null;
    }
}
