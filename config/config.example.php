<?php
/**
 * Copy this file to config/local.php and fill in your credentials.
 * Never commit config/local.php to version control.
 */
return [
    'app_name' => 'ShopWave',

    // Gmail SMTP (use an App Password: https://myaccount.google.com/apppasswords)
    'mail_host'       => 'smtp.gmail.com',
    'mail_port'       => 587,
    'mail_username'   => 'your-email@gmail.com',
    'mail_password'   => 'your-app-password',
    'mail_from_email' => 'your-email@gmail.com',
    'mail_from_name'  => 'ShopWave',

    // Stripe test keys: https://dashboard.stripe.com/test/apikeys
    'stripe_publishable_key' => 'pk_test_xxxxxxxx',
    'stripe_secret_key'      => 'sk_test_xxxxxxxx',
    'stripe_currency'        => 'usd',

    // Webhook signing secret (Stripe CLI: stripe listen --forward-to localhost/ecommerece/payment/webhook.php)
    'stripe_webhook_secret'  => 'whsec_xxxxxxxx',

    // Alert when product stock is at or below this number
    'low_stock_threshold'    => 10,
];
