<?php
require_once 'includes/db.php';
$rows = $pdo->query("SELECT image, COUNT(*) AS cnt FROM products GROUP BY image ORDER BY cnt DESC")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['image'] . ' => ' . $r['cnt'] . "\n";
}
?>