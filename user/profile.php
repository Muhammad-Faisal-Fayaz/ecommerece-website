<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';
requireLogin();

$errors = [];
$success = '';
$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'profile';

    if ($action === 'profile') {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (strlen($name) < 2) $errors[] = 'Name too short.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
        else {
            $check = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $check->execute([$email, $userId]);
            if ($check->fetch()) {
                $errors[] = 'Email in use.';
            } else {
                $pdo->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?')->execute([$name, $email, $userId]);
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $user['name'] = $name;
                $user['email'] = $email;
                $success = 'Profile updated.';
            }
        }
    }

    if ($action === 'password') {
        $current  = $_POST['current_password'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        $hash = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $hash->execute([$userId]);
        $stored = $hash->fetchColumn();
        if (!password_verify($current, $stored)) $errors[] = 'Current password wrong.';
        elseif (strlen($password) < 8) $errors[] = 'Min. 8 characters.';
        elseif ($password !== $confirm) $errors[] = 'Passwords do not match.';
        else {
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([password_hash($password, PASSWORD_BCRYPT), $userId]);
            $success = 'Password changed.';
        }
    }
}

$pageTitle = 'My Profile — ShopWave';
?>
<?php include '../includes/header.php'; ?>

<?php ui_page_hero('My Profile', 'Manage your account settings', 'Account'); ?>

<div class="container page-content-tight">
    <?php if ($success): ?><div class="flash flash-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if (!empty($errors)): ?><div class="flash flash-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

    <div class="profile-grid">
        <div>
        <?php ui_panel_start('Profile Details', 'fa-solid fa-user'); ?>
            <form method="POST">
                <?php csrf_field(); ?>
                <input type="hidden" name="action" value="profile">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <p style="font-size:13px;color:var(--ink-soft);margin-bottom:16px;">Member since <?= date('M j, Y', strtotime($user['created_at'])) ?></p>
                <button type="submit" class="btn btn-primary">Save Profile</button>
            </form>
        <?php ui_panel_end(); ?>
        </div>
        <div>
        <?php ui_panel_start('Password', 'fa-solid fa-lock'); ?>
            <form method="POST">
                <?php csrf_field(); ?>
                <input type="hidden" name="action" value="password">
                <div class="form-group">
                    <label class="form-label">Current</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">New</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm</label>
                    <input type="password" name="confirm" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-outline">Update Password</button>
            </form>
        <?php ui_panel_end(); ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
