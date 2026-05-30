<?php
// /includes/config.php — Application configuration loader

function app_config(string $key, $default = null)
{
    static $config = null;

    if ($config === null) {
        $local = dirname(__DIR__) . '/config/local.php';
        if (!is_file($local)) {
            $config = [];
        } else {
            $config = require $local;
        }
    }

    return $config[$key] ?? $default;
}

function config_is_ready(string $section): bool
{
    if ($section === 'mail') {
        return (bool) app_config('mail_username') && (bool) app_config('mail_password');
    }
    if ($section === 'stripe') {
        return (bool) app_config('stripe_secret_key') && strpos((string) app_config('stripe_secret_key'), 'sk_') === 0;
    }
    if ($section === 'stripe_webhook') {
        return config_is_ready('stripe') && (bool) app_config('stripe_webhook_secret');
    }
    return false;
}
