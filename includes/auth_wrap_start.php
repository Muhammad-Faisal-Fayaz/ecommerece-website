<?php
/** @var string $authTitle @var string $authSubtitle */
$authTitle = $authTitle ?? 'ShopWave';
$authSubtitle = $authSubtitle ?? 'Premium shopping, reimagined.';
?>
<div class="auth-split">
    <div class="auth-brand">
        <a href="<?= BASE_URL ?>/index.php" class="nav-logo" style="margin-bottom:48px;display:inline-block;">Shop<span>Wave</span></a>
        <h2><?= $authTitle /* allows <em> */ ?></h2>
        <p><?= htmlspecialchars($authSubtitle) ?></p>
        <ul class="auth-brand-features">
            <li><i class="fa-solid fa-gem"></i> Curated premium products</li>
            <li><i class="fa-solid fa-lock"></i> Bank-grade secure checkout</li>
            <li><i class="fa-solid fa-truck-fast"></i> Fast &amp; free delivery options</li>
        </ul>
    </div>
    <div class="auth-form-side">
        <div class="form-wrap">
