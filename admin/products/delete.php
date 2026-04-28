<?php
// /admin/products/delete.php — Delete product
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/csrf.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/products/index.php');
    exit;
}

verify_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        // Delete image file
        $imgPath = __DIR__ . '/../../images/products/' . $product['image'];
        if ($product['image'] !== 'default.jpg' && file_exists($imgPath)) {
            unlink($imgPath);
        }
        redirect('/admin/products/index.php', 'Product deleted.', 'success');
    }
}

redirect('/admin/products/index.php', 'Product not found.', 'error');
