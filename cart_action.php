<?php
// /cart_action.php — Handle cart operations
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

verify_csrf();

$action    = $_POST['action']    ?? '';
$productId = (int)($_POST['product_id'] ?? 0);
$quantity  = max(1, (int)($_POST['quantity'] ?? 1));
$isAjax    = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
             (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) ||
             ($_POST['ajax'] ?? false);

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

switch ($action) {
    case 'add':
        if ($productId > 0) {
            // Verify product exists
            $stmt = $pdo->prepare("SELECT id, name, price, stock, image, category FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if ($product) {
                if (isset($_SESSION['cart'][$productId])) {
                    $newQty = $_SESSION['cart'][$productId]['quantity'] + $quantity;
                    $_SESSION['cart'][$productId]['quantity'] = min($newQty, $product['stock']);
                } else {
                    $_SESSION['cart'][$productId] = [
                        'product_id' => $product['id'],
                        'name'       => $product['name'],
                        'price'      => $product['price'],
                        'image'      => $product['image'],
                        'category'   => $product['category'],
                        'quantity'   => min($quantity, $product['stock']),
                    ];
                }
                redirect('/cart.php', 'Product added to cart!', 'success');
            }
        }
        redirect('/index.php', 'Product not found.', 'error');
        break;

    case 'update':
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$productId]);
            }
            // Return JSON for AJAX calls
            $item = $_SESSION['cart'][$productId] ?? null;
            echo json_encode([
                'success'        => true,
                'cart_count'     => getCartCount(),
                'total'          => getCartTotal(),
                'item_subtotal'  => $item ? ($item['price'] * $item['quantity']) : 0,
            ]);
            exit;
        }
        redirect('/cart.php');
        break;

    case 'remove':
        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        redirect('/cart.php', 'Item removed from cart.', 'info');
        break;

    default:
        redirect('/cart.php');
}
