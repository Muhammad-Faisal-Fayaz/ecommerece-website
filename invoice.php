<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/invoice.php';

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$orderId) {
    http_response_code(400);
    die('Invalid order.');
}

requireLogin();
stream_invoice_pdf($pdo, $orderId);
