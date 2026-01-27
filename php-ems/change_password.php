<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$u = current_user();
$role = $u['role'] ?? '';

// Check if password change is allowed for this role
if ($role === 'teacher') {
    $allowed = get_site_setting('allow_teacher_password_change', '1');
    if ($allowed !== '1') {
        flash_set('error', 'Password change is disabled for teachers. Please contact administrator.');
        redirect('profile');
    }
} elseif ($role === 'student') {
    $allowed = get_site_setting('allow_student_password_change', '1');
    if ($allowed !== '1') {
        flash_set('error', 'Password change is disabled for students. Please contact administrator.');
        redirect('profile');
    }
}

$errors = [];

if (request_method() === 'POST') {
    csrf_verify_or_die();
    $current = (string)post('current_password', '');
    $new = (string)post('new_password', '');
    $confirm = (string)post('confirm_password', '');

    if ($current === '') $errors['current_password'] = 'Current password is required.';
    if (strlen($new) < 8) $errors['new_password'] = 'New password must be at least 8 characters.';
    if ($new !== $confirm) $errors['confirm_password'] = 'Passwords do not match.';

    if (!$errors) {
        // Verify current password against users.csv
        $users = csv_read_all(DATA_PATH . '/users.csv');
        $found = null;
        foreach ($users as $usr) {
            if ((string)$usr['id'] === (string)($u['id'] ?? '')) {
                $found = $usr;
                break;
            }
        }
        if (!$found || !password_verify($current, (string)($found['password'] ?? ''))) {
            $errors['current_password'] = 'Current password is incorrect.';
        } else {
            csv_update_by_id(DATA_PATH . '/users.csv', (string)$found['id'], [
                'password' => password_hash($new, PASSWORD_DEFAULT),
            ]);
            flash_set('success', 'Password changed successfully.');
            redirect('profile');
        }
    }
}

$title = 'Change Password';
$active = 'password';
$content = function () use ($errors) {
?>
  <div class="card" style="max-width: 680px;">
    <div class="card-body">
      <h3 class="h6 mb-3">Change Password</h3>
      <form method="post">
        <?= csrf_field() ?>

        <div class="mb-2">
          <label class="form-label">Current Password</label>
          <input class="form-control" type="password" name="current_password" required>
          <?php if (!empty($errors['current_password'])): ?>
            <div class="text-danger small mt-1"><?= e($errors['current_password']) ?></div>
          <?php endif; ?>
        </div>
        <div class="mb-2">
          <label class="form-label">New Password</label>
          <input class="form-control" type="password" name="new_password" required minlength="8">
          <?php if (!empty($errors['new_password'])): ?>
            <div class="text-danger small mt-1"><?= e($errors['new_password']) ?></div>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input class="form-control" type="password" name="confirm_password" required minlength="8">
          <?php if (!empty($errors['confirm_password'])): ?>
            <div class="text-danger small mt-1"><?= e($errors['confirm_password']) ?></div>
          <?php endif; ?>
        </div>

        <button class="btn btn-primary" type="submit">Update Password</button>
      </form>
    </div>
  </div>
<?php
};

include __DIR__ . '/views/partials/layout.php';
