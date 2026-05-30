<?php
// /includes/inventory.php — Stock alerts

require_once __DIR__ . '/config.php';

function low_stock_threshold(): int
{
    return max(1, (int) app_config('low_stock_threshold', 10));
}

function get_low_stock_products(PDO $pdo): array
{
    $threshold = low_stock_threshold();
    $stmt = $pdo->prepare(
        'SELECT * FROM products WHERE stock <= ? ORDER BY stock ASC, name ASC'
    );
    $stmt->execute([$threshold]);
    return $stmt->fetchAll();
}

function count_out_of_stock(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(*) FROM products WHERE stock = 0')->fetchColumn();
}
