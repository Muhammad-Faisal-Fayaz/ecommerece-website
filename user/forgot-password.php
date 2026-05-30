<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';
require_once '../includes/config.php';
require_once '../includes/otp.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email.';
    } elseif (!config_is_ready('mail')) {
        $errors[] = 'Email service is not configured.';
    } elseif (!can_request_otp($email)) {
        $errors[] = 'Too many attempts. Try again later.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $otp = generate_otp_code();
            store_password_otp($pdo, (int) $user['id'], $otp);
            send_password_reset_otp_email($user['email'], $user['name'], $otp);
            record_otp_request($email);
        }
        $_SESSION['reset_email'] = $email;
        redirect(BASE_URL . '/user/verify-otp.php', 'If registered, we sent a 6-digit code.', 'success');
    }
}

$pageTitle = 'Forgot Password — ShopWave';
$authTitle = 'Reset <em>Access</em>';
$authSubtitle = 'We will send a secure one-time code to your email.';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/auth_wrap_start.php'; ?>

        <h2 class="form-title">Forgot Password</h2>
        <p class="form-sub">Enter your account email.</p>
        <?php if (!empty($errors)): ?>
            <div class="flash flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>
        <form method="POST">
            <?php csrf_field(); ?>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Send Code</button>
        </form>
        <p style="text-align:center;margin-top:20px;"><a href="<?= BASE_URL ?>/user/login.php" style="color:var(--gold);">← Back to login</a></p>

<?php include '../includes/auth_wrap_end.php'; ?>
<?php include '../includes/footer.php'; ?>
