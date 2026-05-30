<?php
/**
 * Test email script — configure config/local.php first, then visit:
 * http://localhost/ecommerece/send_mail.php
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/mailer.php';

if (!config_is_ready('mail')) {
    die('Copy config/config.example.php to config/local.php and add your SMTP credentials.');
}

$to = app_config('mail_username');
$ok = send_app_email(
    $to,
    'Test',
    app_config('app_name', 'ShopWave') . ' — Test Email',
    email_template('Test successful', '<p>PHPMailer is configured correctly.</p>'),
    'PHPMailer is configured correctly.'
);

echo $ok ? 'Email sent successfully!' : 'Failed to send. Check PHP error log.';
