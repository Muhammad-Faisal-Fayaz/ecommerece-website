<?php
// /user/logout.php
session_start();
require_once '../includes/csrf.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
}

$_SESSION = [];
session_destroy();
header('Location: ' . BASE_URL . '/user/login.php');
exit;
