<?php
// /user/logout.php
session_start();
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
}

$_SESSION = [];
session_destroy();
header('Location: /user/login.php');
exit;
