<?php
// /includes/ui.php — Shared UI components

function ui_breadcrumb(array $items): void
{
    echo '<nav class="breadcrumb" aria-label="Breadcrumb">';
    $last = count($items) - 1;
    foreach ($items as $i => $item) {
        if ($i > 0) {
            echo '<span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>';
        }
        if ($i === $last || empty($item['url'])) {
            echo '<span class="breadcrumb-current">' . htmlspecialchars($item['label']) . '</span>';
        } else {
            echo '<a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['label']) . '</a>';
        }
    }
    echo '</nav>';
}

function ui_page_hero(string $title, string $subtitle = '', string $label = ''): void
{
    ?>
    <section class="page-hero">
        <div class="page-hero-bg" aria-hidden="true"></div>
        <div class="container page-hero-inner">
            <?php if ($label): ?>
                <span class="page-hero-label"><?= htmlspecialchars($label) ?></span>
            <?php endif; ?>
            <h1 class="page-hero-title"><?= htmlspecialchars($title) ?></h1>
            <?php if ($subtitle): ?>
                <p class="page-hero-sub"><?= htmlspecialchars($subtitle) ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

function ui_panel_start(string $title = '', string $icon = ''): void
{
    echo '<div class="ui-panel">';
    if ($title) {
        echo '<div class="ui-panel-head">';
        if ($icon) {
            echo '<i class="' . htmlspecialchars($icon) . '"></i>';
        }
        echo '<h3>' . htmlspecialchars($title) . '</h3></div>';
    }
    echo '<div class="ui-panel-body">';
}

function ui_panel_end(): void
{
    echo '</div></div>';
}

function ui_checkout_steps(int $current = 1): void
{
    $steps = [
        1 => ['Cart', BASE_URL . '/cart.php'],
        2 => ['Checkout', BASE_URL . '/checkout.php'],
        3 => ['Confirmation', '#'],
    ];
    echo '<div class="checkout-steps">';
    foreach ($steps as $num => $step) {
        $class = 'checkout-step';
        if ($num < $current) {
            $class .= ' is-done';
        } elseif ($num === $current) {
            $class .= ' is-active';
        }
        echo '<div class="' . $class . '">';
        echo '<span class="checkout-step-num">' . ($num < $current ? '<i class="fa-solid fa-check"></i>' : $num) . '</span>';
        echo '<span class="checkout-step-label">' . htmlspecialchars($step[0]) . '</span>';
        echo '</div>';
        if ($num < count($steps)) {
            echo '<div class="checkout-step-line"></div>';
        }
    }
    echo '</div>';
}
