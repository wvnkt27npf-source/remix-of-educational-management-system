<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('*');

// Load students and teachers for linking
$students = csv_read_all(DATA_PATH . '/students.csv');
$teachers = csv_read_all(DATA_PATH . '/teachers.csv');

// Get already linked IDs to filter dropdowns
$users = csv_read_all(DATA_PATH . '/users.csv');
$linkedStudentIds = [];
$linkedTeacherIds = [];
foreach ($users as $u) {
    if (!empty($u['linked_id'])) {
        if ($u['role'] === 'student') $linkedStudentIds[] = $u['linked_id'];
        if ($u['role'] === 'teacher') $linkedTeacherIds[] = $u['linked_id'];
    }
}

// Handle AJAX requests for credential generation
if (isset($_GET['ajax']) && $_GET['ajax'] === 'generate') {
    header('Content-Type: application/json');
    $role = $_GET['role'] ?? 'student';
    echo json_encode([
        'username' => generate_random_username($role),
        'password' => generate_random_password(12)
    ]);
    exit;
}

// Handle AJAX requests for sending WhatsApp credentials
if (isset($_GET['ajax']) && $_GET['ajax'] === 'send_whatsapp') {
    header('Content-Type: application/json');
    
    $phone = trim($_POST['phone'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? 'User');
    $isReset = isset($_POST['is_reset']) && $_POST['is_reset'] === '1';
    
    if (empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'No phone number provided.']);
        exit;
    }
    
    $schoolName = get_site_setting('school_name', 'School');
    $siteDomain = get_site_setting('site_domain', '');
    
    // Build login URL based on role - students go to student_login, others to login
    $loginUrl = '';
    if (!empty($siteDomain)) {
        $loginPath = ($role === 'student') ? '/student_login' : '/login';
        $loginUrl = rtrim($siteDomain, '/') . $loginPath;
    }
    
    if ($isReset) {
        $message = "🎓 *{$schoolName}*\n\n";
        $message .= "Hi " . ($name ?: ucfirst($role)) . "! 👋\n\n";
        $message .= "🔑 *Your New Credentials:*\n";
        $message .= "👤 `{$username}`\n";
        $message .= "🔐 `{$password}`\n";
        if (!empty($loginUrl)) {
            $message .= "\n🔗 {$loginUrl}";
        }
    } else {
        $message = "🎓 *Welcome to {$schoolName}!*\n\n";
        $message .= "Hi " . ($name ?: ucfirst($role)) . "! 👋\n\n";
        $message .= "🔑 *Your Login Credentials:*\n";
        $message .= "👤 `{$username}`\n";
        $message .= "🔐 `{$password}`\n";
        if (!empty($loginUrl)) {
            $message .= "\n🔗 {$loginUrl}";
        }
    }
    
    $result = send_whatsapp_message($phone, $message);
    echo json_encode($result);
    exit;
}

// Handle AJAX requests for resending credentials (generate new password + send)
if (isset($_GET['ajax']) && $_GET['ajax'] === 'resend_credentials') {
    header('Content-Type: application/json');
    
    $userId = trim($_POST['user_id'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? 'User');
    
    if (empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'No phone number provided.']);
        exit;
    }
    
    if (empty($userId)) {
        echo json_encode(['success' => false, 'error' => 'User ID missing.']);
        exit;
    }
    
    // Generate new password
    $newPassword = generate_random_password(12);
    
    // Update user's password in CSV
    $updated = csv_update_by_id(DATA_PATH . '/users.csv', $userId, [
        'password' => password_hash($newPassword, PASSWORD_DEFAULT)
    ]);
    
    if (!$updated) {
        echo json_encode(['success' => false, 'error' => 'Failed to update password.']);
        exit;
    }
    
    // Build message with login link based on role
    $schoolName = get_site_setting('school_name', 'School');
    $siteDomain = get_site_setting('site_domain', '');
    
    $loginUrl = '';
    if (!empty($siteDomain)) {
        $loginPath = ($role === 'student') ? '/student_login' : '/login';
        $loginUrl = rtrim($siteDomain, '/') . $loginPath;
    }
    
    $message = "🎓 *{$schoolName}*\n\n";
    $message .= "Hi " . ($name ?: ucfirst($role)) . "! 👋\n\n";
    $message .= "🔑 *Your New Credentials:*\n";
    $message .= "👤 `{$username}`\n";
    $message .= "🔐 `{$newPassword}`\n";
    if (!empty($loginUrl)) {
        $message .= "\n🔗 {$loginUrl}";
    }
    
    $result = send_whatsapp_message($phone, $message);
    echo json_encode($result);
    exit;
}

// Admin/custom user management
if (request_method() === 'POST') {
    csrf_verify_or_die();
    $action = (string)post('action', '');

    if ($action === 'create') {
        $username = trim((string)post('username', ''));
        $password = (string)post('password', '');
        $role = trim((string)post('role', 'teacher'));
        $linkedId = trim((string)post('linked_id', ''));

        if ($username === '' || $password === '' || $role === '') {
            flash_set('danger', 'Please fill all required fields.');
            redirect('users');
        }
        if (!in_array($role, ['admin', 'teacher', 'student', 'custom'], true)) {
            flash_set('danger', 'Invalid role.');
            redirect('users');
        }

        // Prevent duplicates
        $users = csv_read_all(DATA_PATH . '/users.csv');
        foreach ($users as $u) {
            if (strcasecmp((string)$u['username'], $username) === 0) {
                flash_set('danger', 'Username already exists.');
                redirect('users');
            }
        }

        // Store the plain password temporarily for display
        $plainPassword = $password;

        $newUser = csv_insert(DATA_PATH . '/users.csv', [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'linked_id' => $linkedId,
        ]);
        
        // Get phone number from linked record for WhatsApp
        $linkedPhone = '';
        $linkedName = '';
        if (!empty($linkedId)) {
            if ($role === 'student') {
                $linkedRecord = csv_find_by_id(DATA_PATH . '/students.csv', $linkedId);
                if ($linkedRecord) {
                    $linkedPhone = $linkedRecord['phone'] ?? '';
                    $linkedName = $linkedRecord['name'] ?? '';
                }
            } elseif ($role === 'teacher') {
                $linkedRecord = csv_find_by_id(DATA_PATH . '/teachers.csv', $linkedId);
                if ($linkedRecord) {
                    $linkedPhone = $linkedRecord['phone'] ?? '';
                    $linkedName = $linkedRecord['name'] ?? '';
                }
            }
        }
        
        // Store credentials in session for modal display
        $_SESSION['created_credentials'] = [
            'username' => $username,
            'password' => $plainPassword,
            'role' => $role,
            'phone' => $linkedPhone,
            'name' => $linkedName,
        ];
        
        flash_set('success', 'User created successfully!');
        redirect('users');
    }

    if ($action === 'update') {
        $id = (string)post('id', '');
        $username = trim((string)post('username', ''));
        $password = (string)post('password', '');
        $role = trim((string)post('role', 'teacher'));
        $linkedId = trim((string)post('linked_id', ''));

        if ($id === '' || $username === '' || $role === '') {
            flash_set('danger', 'Please fill all required fields.');
            redirect('users');
        }
        if (!in_array($role, ['admin', 'teacher', 'student', 'custom'], true)) {
            flash_set('danger', 'Invalid role.');
            redirect('users');
        }

        // Check for duplicate username (exclude current user)
        $users = csv_read_all(DATA_PATH . '/users.csv');
        foreach ($users as $u) {
            if ((string)$u['id'] !== $id && strcasecmp((string)$u['username'], $username) === 0) {
                flash_set('danger', 'Username already exists.');
                redirect('users');
            }
        }

        $updateData = ['username' => $username, 'role' => $role, 'linked_id' => $linkedId];
        if ($password !== '') {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        csv_update_by_id(DATA_PATH . '/users.csv', $id, $updateData);
        flash_set('success', 'User updated.');
        redirect('users');
    }

    if ($action === 'delete') {
        $id = (string)post('id', '');
        $me = current_user();
        if ($me && (string)$me['id'] === $id) {
            flash_set('danger', 'You cannot delete your own account.');
            redirect('users');
        }
        if ($id !== '') csv_delete_by_id(DATA_PATH . '/users.csv', $id);
        flash_set('info', 'User deleted.');
        redirect('users');
    }
    
    if ($action === 'reset_password') {
        $id = (string)post('id', '');
        if ($id !== '') {
            $newPassword = generate_random_password(12);
            csv_update_by_id(DATA_PATH . '/users.csv', $id, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]);
            $user = csv_find_by_id(DATA_PATH . '/users.csv', $id);
            
            // Get phone number from linked record for WhatsApp
            $linkedPhone = '';
            $linkedName = '';
            $linkedId = $user['linked_id'] ?? '';
            $role = $user['role'] ?? '';
            
            if (!empty($linkedId)) {
                if ($role === 'student') {
                    $linkedRecord = csv_find_by_id(DATA_PATH . '/students.csv', $linkedId);
                    if ($linkedRecord) {
                        $linkedPhone = $linkedRecord['phone'] ?? '';
                        $linkedName = $linkedRecord['name'] ?? '';
                    }
                } elseif ($role === 'teacher') {
                    $linkedRecord = csv_find_by_id(DATA_PATH . '/teachers.csv', $linkedId);
                    if ($linkedRecord) {
                        $linkedPhone = $linkedRecord['phone'] ?? '';
                        $linkedName = $linkedRecord['name'] ?? '';
                    }
                }
            }
            
            $_SESSION['reset_credentials'] = [
                'username' => $user['username'] ?? '',
                'password' => $newPassword,
                'role' => $role,
                'phone' => $linkedPhone,
                'name' => $linkedName,
            ];
        }
        flash_set('success', 'Password reset successfully!');
        redirect('users');
    }
}

$users = csv_read_all(DATA_PATH . '/users.csv');
usort($users, fn($a, $b) => (int)$a['id'] <=> (int)$b['id']);

// Group users by role
$adminUsers = array_filter($users, fn($u) => $u['role'] === 'admin');
$teacherUsers = array_filter($users, fn($u) => $u['role'] === 'teacher');
$studentUsers = array_filter($users, fn($u) => $u['role'] === 'student');
$customUsers = array_filter($users, fn($u) => $u['role'] === 'custom');

// Check if editing
$editUser = null;
$editId = get('edit', '');
if ($editId !== '') {
    $editUser = csv_find_by_id(DATA_PATH . '/users.csv', $editId);
}

// Get created/reset credentials from session
$createdCredentials = $_SESSION['created_credentials'] ?? null;
$resetCredentials = $_SESSION['reset_credentials'] ?? null;
unset($_SESSION['created_credentials'], $_SESSION['reset_credentials']);

$title = 'Users';
$active = 'users';
$content = function () use ($users, $editUser, $students, $teachers, $linkedStudentIds, $linkedTeacherIds, $createdCredentials, $resetCredentials, $adminUsers, $teacherUsers, $studentUsers, $customUsers) {
    // Prepare students/teachers JSON for JavaScript
    $editLinkedIdStr = $editUser ? (string)($editUser['linked_id'] ?? '') : '';
    $availableStudents = array_filter($students, function($s) use ($linkedStudentIds, $editLinkedIdStr) {
        // Include if not linked, or if editing and this is the current linked record
        return !in_array($s['id'], $linkedStudentIds) || ($editLinkedIdStr !== '' && (string)$s['id'] === $editLinkedIdStr);
    });
    $availableTeachers = array_filter($teachers, function($t) use ($linkedTeacherIds, $editLinkedIdStr) {
        return !in_array($t['id'], $linkedTeacherIds) || ($editLinkedIdStr !== '' && (string)$t['id'] === $editLinkedIdStr);
    });

    // Normalize keys for JS to avoid "undefined" when older CSV headers differ.
    $availableStudentsForJs = array_values(array_map(function ($s) {
        return [
            'id' => trim((string)($s['id'] ?? '')),
            'name' => trim((string)($s['name'] ?? ($s['full_name'] ?? ($s['student_name'] ?? '')))),
            'class' => trim((string)($s['class'] ?? ($s['grade'] ?? ($s['class_name'] ?? '')))),
            'section' => trim((string)($s['section'] ?? '')),
        ];
    }, $availableStudents));

    $availableTeachersForJs = array_values(array_map(function ($t) {
        return [
            'id' => trim((string)($t['id'] ?? '')),
            'name' => trim((string)($t['name'] ?? ($t['full_name'] ?? ($t['teacher_name'] ?? '')))),
            'subject' => trim((string)($t['subject'] ?? '')),
            'designation' => trim((string)($t['designation'] ?? '')),
        ];
    }, $availableTeachers));
    // Determine if link section should be visible
    $showLinkSection = !$editUser || in_array($editUser['role'] ?? '', ['student', 'teacher']);
?>
<style>
.credential-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    color: white;
}
.credential-field {
    background: rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 10px;
}
.credential-field label {
    font-size: 0.75rem;
    opacity: 0.8;
    margin-bottom: 4px;
}
.credential-field .value {
    font-family: 'Courier New', monospace;
    font-size: 1.1rem;
    font-weight: bold;
    word-break: break-all;
}
.copy-btn {
    background: rgba(255,255,255,0.3);
    border: none;
    padding: 4px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}
.copy-btn:hover {
    background: rgba(255,255,255,0.5);
}
/* Dark theme fixes for credentials section */
.credentials-card {
    background: rgba(59, 130, 246, 0.1) !important;
    border: 1px solid rgba(59, 130, 246, 0.3) !important;
}
.credentials-card .form-label {
    color: #e2e8f0 !important;
}
.credentials-card .form-control {
    background-color: #1e293b !important;
    border-color: #475569 !important;
    color: #f1f5f9 !important;
}
.credentials-card .form-control::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}
.credentials-card .btn-outline-secondary {
    border-color: #475569 !important;
    color: #94a3b8 !important;
}
.credentials-card .btn-outline-secondary:hover {
    background-color: #334155 !important;
    color: #f1f5f9 !important;
}
.credentials-card .fw-semibold {
    color: #f1f5f9 !important;
}

/* Create User Modal - Dark Theme Fix */
#createUserModal .modal-content {
    background-color: #1e293b !important;
    border: 1px solid #334155 !important;
}
#createUserModal .modal-header {
    border-bottom: 1px solid #334155 !important;
}
#createUserModal .modal-title {
    color: #f1f5f9 !important;
}
#createUserModal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
#createUserModal .form-label {
    color: #e2e8f0 !important;
}
#createUserModal .form-label .text-muted {
    color: #94a3b8 !important;
}
#createUserModal .form-select,
#createUserModal .form-control {
    background-color: #0f172a !important;
    border-color: #475569 !important;
    color: #f1f5f9 !important;
}
#createUserModal .form-select option {
    background-color: #1e293b;
    color: #f1f5f9;
}
#createUserModal .form-control::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}
#createUserModal .text-muted {
    color: #94a3b8 !important;
}
#createUserModal .text-warning {
    color: #fbbf24 !important;
}
#createUserModal .text-info {
    color: #38bdf8 !important;
}
#createUserModal .modal-footer {
    border-top: 1px solid #334155 !important;
}
#createUserModal .btn-outline-secondary {
    border-color: #475569 !important;
    color: #94a3b8 !important;
}
#createUserModal .btn-outline-secondary:hover {
    background-color: #334155 !important;
    color: #f1f5f9 !important;
}
/* Resend Modal - Dark Theme */
#resendModal .modal-content {
    background-color: #1e293b !important;
    border: 1px solid #334155 !important;
}
#resendModal .modal-header {
    border-bottom: none !important;
}
#resendModal .modal-title {
    color: #f1f5f9 !important;
}
#resendModal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
#resendModal .modal-body p {
    color: #e2e8f0 !important;
}
#resendModal .modal-body strong {
    color: #fbbf24 !important;
}
#resendModal .card.bg-light {
    background: linear-gradient(135deg, rgba(92,124,250,0.15), rgba(59,130,246,0.1)) !important;
    border: 1px solid rgba(92,124,250,0.3) !important;
}
#resendModal .card .small.text-muted {
    color: #94a3b8 !important;
}
#resendModal .card .fw-semibold {
    color: #f1f5f9 !important;
    font-size: 1.1rem;
}
#resendModal #resend-display-username {
    color: #60a5fa !important;
}
#resendModal .btn-success {
    background: linear-gradient(135deg, #25d366, #128c7e) !important;
    border: none !important;
    font-weight: 600;
    padding: 12px 20px;
}
#resendModal .btn-success:hover {
    background: linear-gradient(135deg, #20bd5a, #0f7a6c) !important;
}

.role-badge-admin { background: #dc3545 !important; }
.role-badge-teacher { background: #0d6efd !important; }
.role-badge-student { background: #198754 !important; }
.role-badge-custom { background: #6f42c1 !important; }
.generate-btn {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    border: none;
    color: white;
    font-weight: 600;
}
.generate-btn:hover {
    background: linear-gradient(135deg, #0d7d71, #2bc466);
    color: white;
}
/* Filter Button Styles */
.filter-btn {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.05);
    color: #adb5bd;
    cursor: pointer;
    transition: all 0.2s;
}
.filter-btn:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}
.filter-btn.active {
    color: #fff;
}
.filter-btn.active[data-role="all"] { background: #6c757d; border-color: #6c757d; }
.filter-btn.active[data-role="admin"] { background: #dc3545; border-color: #dc3545; }
.filter-btn.active[data-role="teacher"] { background: #0d6efd; border-color: #0d6efd; }
.filter-btn.active[data-role="student"] { background: #198754; border-color: #198754; }
.filter-btn.active[data-role="custom"] { background: #6f42c1; border-color: #6f42c1; }

/* Table Styles */
.users-table th {
    cursor: pointer;
    user-select: none;
    white-space: nowrap;
}
.users-table th:hover {
    background: rgba(255,255,255,0.05);
}
.users-table th .sort-icon {
    opacity: 0.3;
    margin-left: 4px;
}
.users-table th.sorted .sort-icon {
    opacity: 1;
}
.role-badge-admin { background: #dc3545 !important; }
.role-badge-teacher { background: #0d6efd !important; }
.role-badge-student { background: #198754 !important; }
.role-badge-custom { background: #6f42c1 !important; }
.search-input {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    border-radius: 6px;
}
.search-input:focus {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.3);
    color: #fff;
    box-shadow: none;
}
.search-input::placeholder {
    color: #6c757d;
}
.per-page-select {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 0.85rem;
}
.generate-btn {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    border: none;
    color: white;
    font-weight: 600;
}
.generate-btn:hover {
    background: linear-gradient(135deg, #0d7d71, #2bc466);
    color: white;
}
</style>

<?php if ($createdCredentials): ?>
<?php 
    $hasPhone = !empty($createdCredentials['phone']);
    $whatsappPhone = preg_replace('/[^0-9]/', '', $createdCredentials['phone'] ?? '');
?>
<div class="modal fade show" id="credentialsModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="credential-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-key-fill me-2"></i>User Created Successfully!</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('credentialsModal').style.display='none'"></button>
                </div>
                <p class="small opacity-75 mb-3">Save these credentials - the password cannot be recovered!</p>
                
                <?php if (!empty($createdCredentials['name'])): ?>
                <div class="credential-field">
                    <label class="d-block">Name</label>
                    <span class="value" id="modal-name"><?= e($createdCredentials['name']) ?></span>
                </div>
                <?php endif; ?>
                
                <div class="credential-field">
                    <label class="d-block">Username</label>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="value" id="modal-username"><?= e($createdCredentials['username']) ?></span>
                        <button class="copy-btn" onclick="copyToClipboard('modal-username')"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                
                <div class="credential-field">
                    <label class="d-block">Password</label>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="value" id="modal-password"><?= e($createdCredentials['password']) ?></span>
                        <button class="copy-btn" onclick="copyToClipboard('modal-password')"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                
                <div class="credential-field">
                    <label class="d-block">Role</label>
                    <span class="value text-capitalize" id="modal-role"><?= e($createdCredentials['role']) ?></span>
                </div>
                
                <!-- Hidden data for WhatsApp -->
                <input type="hidden" id="modal-phone" value="<?= e($whatsappPhone) ?>">
                
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-light flex-grow-1" onclick="copyAllCredentials()">
                        <i class="bi bi-clipboard-check me-2"></i>Copy All
                    </button>
                    <?php if ($hasPhone): ?>
                    <button type="button" class="btn btn-success flex-grow-1" id="sendWhatsappBtn" onclick="sendWhatsAppCredentials(false)">
                        <i class="bi bi-whatsapp me-2"></i>Send on WhatsApp
                    </button>
                    <?php else: ?>
                    <button class="btn btn-secondary flex-grow-1" disabled title="No phone number linked">
                        <i class="bi bi-whatsapp me-2"></i>No Phone
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($resetCredentials): ?>
<?php 
    $hasResetPhone = !empty($resetCredentials['phone']);
    $resetWhatsappPhone = preg_replace('/[^0-9]/', '', $resetCredentials['phone'] ?? '');
?>
<div class="modal fade show" id="resetModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="credential-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i>Password Reset!</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('resetModal').style.display='none'"></button>
                </div>
                <p class="small opacity-75 mb-3">New password generated. Share with the user securely!</p>
                
                <?php if (!empty($resetCredentials['name'])): ?>
                <div class="credential-field">
                    <label class="d-block">Name</label>
                    <span class="value" id="reset-name"><?= e($resetCredentials['name']) ?></span>
                </div>
                <?php endif; ?>
                
                <div class="credential-field">
                    <label class="d-block">Username</label>
                    <span class="value" id="reset-username"><?= e($resetCredentials['username']) ?></span>
                </div>
                
                <div class="credential-field">
                    <label class="d-block">New Password</label>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="value" id="reset-password"><?= e($resetCredentials['password']) ?></span>
                        <button class="copy-btn" onclick="copyToClipboard('reset-password')"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                
                <!-- Hidden data for WhatsApp -->
                <input type="hidden" id="reset-phone" value="<?= e($resetWhatsappPhone) ?>">
                <input type="hidden" id="reset-role" value="<?= e($resetCredentials['role'] ?? 'user') ?>">
                
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-light flex-grow-1" onclick="copyResetCredentials()">
                        <i class="bi bi-clipboard-check me-2"></i>Copy All
                    </button>
                    <?php if ($hasResetPhone): ?>
                    <button type="button" class="btn btn-success flex-grow-1" id="sendResetWhatsappBtn" onclick="sendWhatsAppCredentials(true)">
                        <i class="bi bi-whatsapp me-2"></i>Send on WhatsApp
                    </button>
                    <?php else: ?>
                    <button class="btn btn-secondary flex-grow-1" disabled title="No phone number linked">
                        <i class="bi bi-whatsapp me-2"></i>No Phone
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($editUser): ?>
<!-- Edit User Form (full width card) -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 mb-0"><i class="bi bi-pencil-square me-2"></i>Edit User</h3>
            <a href="users" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </a>
        </div>
        <form method="post" id="userForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= e($editUser['id']) ?>">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Role</label>
                    <div>
                        <span class="badge role-badge-<?= e($editUser['role']) ?> fs-6 px-3 py-2">
                            <?php 
                            $roleIcons = ['student' => '🎓', 'teacher' => '👨‍🏫', 'admin' => '👑', 'custom' => '⚙️'];
                            echo ($roleIcons[$editUser['role']] ?? '') . ' ' . ucfirst(e($editUser['role']));
                            ?>
                        </span>
                    </div>
                    <input type="hidden" name="role" value="<?= e($editUser['role']) ?>">
                </div>
                
                <?php if (in_array($editUser['role'], ['student', 'teacher'])): ?>
                <div class="col-md-6" id="linkSection">
                    <label class="form-label fw-semibold">Link to Record</label>
                    <select class="form-select" name="linked_id" id="linkedSelect">
                        <option value="">-- Select to link --</option>
                    </select>
                </div>
                <?php else: ?>
                <input type="hidden" name="linked_id" value="">
                <?php endif; ?>
                
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input class="form-control" name="username" id="usernameField" required maxlength="64" 
                           value="<?= e($editUser['username']) ?>">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password <span class="text-muted">(leave blank to keep)</span></label>
                    <div class="input-group">
                        <input class="form-control" type="password" name="password" id="passwordField" maxlength="128"
                               placeholder="Enter new password">
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg me-1"></i>Update User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Header with Title, Create Button and Filter Buttons -->
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="h6 mb-0"><i class="bi bi-people me-2"></i>User Accounts</h3>
                        <?php if (!$editUser): ?>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="bi bi-plus-lg me-1"></i>Create User
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-wrap gap-2" id="roleFilters">
                        <button class="filter-btn active" data-role="all" onclick="filterByRole('all')">
                            <i class="bi bi-grid-fill me-1"></i>All <span class="badge bg-secondary ms-1"><?= count($users) ?></span>
                        </button>
                        <button class="filter-btn" data-role="admin" onclick="filterByRole('admin')">
                            <i class="bi bi-shield-fill me-1"></i>Admin <span class="badge bg-danger ms-1"><?= count($adminUsers) ?></span>
                        </button>
                        <button class="filter-btn" data-role="teacher" onclick="filterByRole('teacher')">
                            <i class="bi bi-mortarboard-fill me-1"></i>Teachers <span class="badge bg-primary ms-1"><?= count($teacherUsers) ?></span>
                        </button>
                        <button class="filter-btn" data-role="student" onclick="filterByRole('student')">
                            <i class="bi bi-person-fill me-1"></i>Students <span class="badge bg-success ms-1"><?= count($studentUsers) ?></span>
                        </button>
                        <?php if (count($customUsers) > 0): ?>
                        <button class="filter-btn" data-role="custom" onclick="filterByRole('custom')">
                            <i class="bi bi-gear-fill me-1"></i>Custom <span class="badge bg-purple ms-1"><?= count($customUsers) ?></span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Search and Per Page -->
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted">Show</label>
                        <select class="per-page-select" id="perPageSelect" onchange="changePerPage()">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="small text-muted">entries</label>
                    </div>
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm search-input" id="userSearchInput" 
                               placeholder="Search users..." onkeyup="searchUsers()" style="width: 200px;">
                        <i class="bi bi-search position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); opacity: 0.5;"></i>
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-sm align-middle users-table" id="usersTable">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)">Username <i class="bi bi-arrow-down-up sort-icon"></i></th>
                                <th onclick="sortTable(1)">Role <i class="bi bi-arrow-down-up sort-icon"></i></th>
                                <th onclick="sortTable(2)">Linked Record <i class="bi bi-arrow-down-up sort-icon"></i></th>
                                <th onclick="sortTable(3)">Details <i class="bi bi-arrow-down-up sort-icon"></i></th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php 
                            // Prepare all users with their linked records
                            foreach ($users as $u): 
                                $linkedRecord = null;
                                $linkedName = '-';
                                $linkedDetails = '-';
                                
                                if (!empty($u['linked_id'])) {
                                    if ($u['role'] === 'student') {
                                        $linkedRecord = csv_find_by_id(DATA_PATH . '/students.csv', $u['linked_id']);
                                        if ($linkedRecord) {
                                            $linkedName = $linkedRecord['name'] ?? '-';
                                            $class = $linkedRecord['class'] ?? '';
                                            $section = $linkedRecord['section'] ?? '';
                                            $linkedDetails = $class . ($section ? '-' . $section : '');
                                            if (empty($linkedDetails)) $linkedDetails = '-';
                                        }
                                    } elseif ($u['role'] === 'teacher') {
                                        $linkedRecord = csv_find_by_id(DATA_PATH . '/teachers.csv', $u['linked_id']);
                                        if ($linkedRecord) {
                                            $linkedName = $linkedRecord['name'] ?? '-';
                                            $linkedDetails = ($linkedRecord['designation'] ?? '') . ' | ' . ($linkedRecord['subject'] ?? '');
                                        }
                                    }
                                }
                            ?>
                            <tr data-role="<?= e($u['role']) ?>">
                                <td>
                                    <span class="fw-semibold"><?= e($u['username']) ?></span>
                                    <span class="text-muted small d-block">ID: <?= e($u['id']) ?></span>
                                </td>
                                <td>
                                    <span class="badge role-badge-<?= e($u['role']) ?>">
                                        <?= ucfirst(e($u['role'])) ?>
                                    </span>
                                </td>
                                <td><?= e($linkedName) ?></td>
                                <td class="small text-muted"><?= e($linkedDetails) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <?php 
                                        // Get phone for resend
                                        $resendPhone = '';
                                        $resendName = $linkedName !== '-' ? $linkedName : '';
                                        if ($linkedRecord) {
                                            $resendPhone = $linkedRecord['phone'] ?? '';
                                        }
                                        ?>
                                        <?php if (!empty($resendPhone)): ?>
                                        <button type="button" class="btn btn-outline-success" title="Resend Credentials via WhatsApp"
                                                onclick="openResendModal('<?= e($u['id']) ?>', '<?= e($u['username']) ?>', '<?= e($resendPhone) ?>', '<?= e($resendName) ?>', '<?= e($u['role']) ?>')">
                                            <i class="bi bi-whatsapp"></i>
                                        </button>
                                        <?php endif; ?>
                                        <a href="users?edit=<?= e($u['id']) ?>" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($u['role'] !== 'admin' || (current_user()['id'] ?? '') !== $u['id']): ?>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= e($u['id']) ?>">
                                            <button class="btn btn-outline-danger" type="submit" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Info -->
                <div class="d-flex justify-content-between align-items-center mt-3" id="paginationInfo">
                    <span class="small text-muted">Showing <span id="showingFrom">1</span> to <span id="showingTo"><?= min(10, count($users)) ?></span> of <span id="totalEntries"><?= count($users) ?></span> entries</span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="paginationNav">
                            <!-- Pagination buttons will be generated by JS -->
                        </ul>
                    </nav>
                </div>
                
                <?php if (count($users) === 0): ?>
                <p class="text-muted text-center py-4">No users found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<?php if (!$editUser): ?>
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Create User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="createUserForm">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select class="form-select" name="role" id="roleSelect" required>
                            <option value="student">🎓 Student</option>
                            <option value="teacher">👨‍🏫 Teacher</option>
                            <option value="admin">👑 Admin</option>
                            <option value="custom">⚙️ Custom (Full Access)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="linkSection">
                        <label class="form-label fw-semibold">Link to Record <span class="text-muted">(optional)</span></label>
                        <select class="form-select" name="linked_id" id="linkedSelect">
                            <option value="">-- Select to link --</option>
                        </select>
                        <small class="text-muted d-block mt-1">Link this login to an existing student/teacher profile</small>
                        <small class="text-warning d-none" id="noRecordsWarning">
                            <i class="bi bi-exclamation-triangle me-1"></i>No records available. 
                            <span id="createRecordLink"></span>
                        </small>
                    </div>
                    
                    <div class="card credentials-card mb-3">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-semibold">Credentials</span>
                                <button type="button" class="btn btn-sm generate-btn" id="generateBtn">
                                    <i class="bi bi-magic me-1"></i>Generate Random
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small mb-1">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input class="form-control" name="username" id="usernameField" required maxlength="64" 
                                           placeholder="Enter username">
                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('usernameField')" title="Copy">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label small mb-1">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input class="form-control" type="password" name="password" id="passwordField"
                                           required maxlength="128" placeholder="Enter password">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" title="Show/Hide">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('passwordField')" title="Copy">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Resend Credentials Modal -->
<div class="modal fade" id="resendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="bi bi-whatsapp me-2 text-success"></i>Resend Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-3">This will generate a <strong>new password</strong> and send it via WhatsApp.</p>
                
                <div class="card bg-light mb-3">
                    <div class="card-body py-2">
                        <div class="small text-muted">User</div>
                        <div class="fw-semibold" id="resend-display-name">-</div>
                        <div class="small text-muted" id="resend-display-username">-</div>
                    </div>
                </div>
                
                <input type="hidden" id="resend-user-id">
                <input type="hidden" id="resend-username">
                <input type="hidden" id="resend-phone">
                <input type="hidden" id="resend-name">
                <input type="hidden" id="resend-role">
                
                <button type="button" class="btn btn-success w-100" id="confirmResendBtn" onclick="confirmResendCredentials()">
                    <i class="bi bi-whatsapp me-2"></i>Generate & Send New Password
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Data for linking
const studentsData = <?= json_encode($availableStudentsForJs) ?>;
const teachersData = <?= json_encode($availableTeachersForJs) ?>;
const editLinkedId = <?= json_encode($editUser['linked_id'] ?? '') ?>;
const editMode = <?= $editUser ? 'true' : 'false' ?>;
const editRole = <?= json_encode($editUser['role'] ?? '') ?>;

const roleSelect = document.getElementById('roleSelect');
const linkSection = document.getElementById('linkSection');
const linkedSelect = document.getElementById('linkedSelect');
const generateBtn = document.getElementById('generateBtn');
const usernameField = document.getElementById('usernameField');
const passwordField = document.getElementById('passwordField');
const togglePassword = document.getElementById('togglePassword');
const eyeIcon = document.getElementById('eyeIcon');

// Initialize dropdown for EDIT mode (student/teacher)
function initForEditMode() {
    if (!editMode || !linkedSelect) return;
    
    const noRecordsWarning = document.getElementById('noRecordsWarning');
    const createRecordLink = document.getElementById('createRecordLink');
    linkedSelect.innerHTML = '<option value="">-- Select to link --</option>';
    
    let hasRecords = false;
    
    if (editRole === 'student') {
        studentsData.forEach(s => {
            if (!s || !s.id) return;
            const opt = document.createElement('option');
            opt.value = s.id;
            const nm = s.name || 'Unnamed';
            const cls = s.class || '';
            const sec = s.section || '';
            opt.textContent = `${nm} (${cls}${sec ? '-' + sec : ''})`;
            if (String(s.id) === String(editLinkedId)) opt.selected = true;
            linkedSelect.appendChild(opt);
        });
        hasRecords = studentsData.length > 0;
        if (!hasRecords && createRecordLink) {
            createRecordLink.innerHTML = '<a href="students" class="text-info">Create student first →</a>';
        }
    } else if (editRole === 'teacher') {
        teachersData.forEach(t => {
            if (!t || !t.id) return;
            const opt = document.createElement('option');
            opt.value = t.id;
            const nm = t.name || 'Unnamed';
            opt.textContent = `${nm} (${t.subject || 'No subject'})`;
            if (String(t.id) === String(editLinkedId)) opt.selected = true;
            linkedSelect.appendChild(opt);
        });
        hasRecords = teachersData.length > 0;
        if (!hasRecords && createRecordLink) {
            createRecordLink.innerHTML = '<a href="teachers" class="text-info">Create teacher first →</a>';
        }
    }
    
    // Show/hide warning
    if (noRecordsWarning) {
        if (!hasRecords) {
            noRecordsWarning.classList.remove('d-none');
        } else {
            noRecordsWarning.classList.add('d-none');
        }
    }
}

// Update linked dropdown based on role (for CREATE mode)
function updateLinkDropdown() {
    if (!roleSelect || !linkSection || !linkedSelect) return;
    
    const role = roleSelect.value;
    const noRecordsWarning = document.getElementById('noRecordsWarning');
    const createRecordLink = document.getElementById('createRecordLink');
    linkedSelect.innerHTML = '<option value="">-- Select to link --</option>';
    
    let hasRecords = false;
    
    if (role === 'student') {
        linkSection.style.display = 'block';
        studentsData.forEach(s => {
            if (!s || !s.id) return;
            const opt = document.createElement('option');
            opt.value = s.id;
            const nm = s.name || 'Unnamed';
            const cls = s.class || '';
            const sec = s.section || '';
            opt.textContent = `${nm} (${cls}${sec ? '-' + sec : ''})`;
            linkedSelect.appendChild(opt);
        });
        hasRecords = studentsData.length > 0;
        if (!hasRecords && createRecordLink) {
            createRecordLink.innerHTML = '<a href="students" class="text-info">Create student first →</a>';
        }
    } else if (role === 'teacher') {
        linkSection.style.display = 'block';
        teachersData.forEach(t => {
            if (!t || !t.id) return;
            const opt = document.createElement('option');
            opt.value = t.id;
            const nm = t.name || 'Unnamed';
            opt.textContent = `${nm} (${t.subject || 'No subject'})`;
            linkedSelect.appendChild(opt);
        });
        hasRecords = teachersData.length > 0;
        if (!hasRecords && createRecordLink) {
            createRecordLink.innerHTML = '<a href="teachers" class="text-info">Create teacher first →</a>';
        }
    } else {
        linkSection.style.display = 'none';
    }
    
    // Show/hide warning
    if (noRecordsWarning) {
        if ((role === 'student' || role === 'teacher') && !hasRecords) {
            noRecordsWarning.classList.remove('d-none');
        } else {
            noRecordsWarning.classList.add('d-none');
        }
    }
}

// Initialize based on mode
if (editMode) {
    // Edit mode: populate dropdown for student/teacher
    initForEditMode();
} else if (roleSelect) {
    // Create mode: handle role changes
    roleSelect.addEventListener('change', updateLinkDropdown);
    updateLinkDropdown();
}

// Generate random credentials
if (generateBtn) {
    generateBtn.addEventListener('click', async function() {
        const role = roleSelect ? roleSelect.value : 'student';
        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Generating...';
        
        try {
            const response = await fetch(`users?ajax=generate&role=${role}`);
            const data = await response.json();
            usernameField.value = data.username;
            passwordField.value = data.password;
            passwordField.type = 'text'; // Show the generated password
            eyeIcon.className = 'bi bi-eye-slash';
        } catch (err) {
            console.error('Error generating credentials:', err);
        }
        
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-magic me-1"></i>Generate Random';
    });
}

// Toggle password visibility
if (togglePassword) {
    togglePassword.addEventListener('click', function() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.className = 'bi bi-eye-slash';
        } else {
            passwordField.type = 'password';
            eyeIcon.className = 'bi bi-eye';
        }
    });
}
function copyToClipboard(elementId) {
    const el = document.getElementById(elementId);
    const text = el.tagName === 'INPUT' ? el.value : el.textContent;
    navigator.clipboard.writeText(text).then(() => {
        // Show brief feedback
        const btn = el.closest('.input-group')?.querySelector('.btn-outline-secondary') || 
                    el.parentElement.querySelector('.copy-btn');
        if (btn) {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check"></i>';
            setTimeout(() => btn.innerHTML = originalHtml, 1000);
        }
    });
}

function copyAllCredentials() {
    const username = document.getElementById('modal-username')?.textContent || '';
    const password = document.getElementById('modal-password')?.textContent || '';
    const text = `Username: ${username}\nPassword: ${password}`;
    navigator.clipboard.writeText(text).then(() => {
        alert('Credentials copied to clipboard!');
    });
}

function copyResetCredentials() {
    const username = document.getElementById('reset-username')?.textContent || '';
    const password = document.getElementById('reset-password')?.textContent || '';
    const text = `Username: ${username}\nNew Password: ${password}`;
    navigator.clipboard.writeText(text).then(() => {
        alert('Credentials copied to clipboard!');
    });
}

// Send WhatsApp via UltraMSG
async function sendWhatsAppCredentials(isReset = false) {
    const prefix = isReset ? 'reset' : 'modal';
    const btnId = isReset ? 'sendResetWhatsappBtn' : 'sendWhatsappBtn';
    const modalId = isReset ? 'resetModal' : 'credentialsModal';
    const btn = document.getElementById(btnId);
    
    const phone = document.getElementById(prefix + '-phone')?.value || '';
    const username = document.getElementById(prefix + '-username')?.textContent || '';
    const password = document.getElementById(prefix + '-password')?.textContent || '';
    const name = document.getElementById(prefix + '-name')?.textContent || '';
    const role = isReset 
        ? (document.getElementById('reset-role')?.value || 'user')
        : (document.getElementById('modal-role')?.textContent || 'user');
    
    if (!phone) {
        alert('No phone number available.');
        return;
    }
    
    // Show loading state
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
    
    try {
        const formData = new FormData();
        formData.append('phone', phone);
        formData.append('username', username);
        formData.append('password', password);
        formData.append('name', name);
        formData.append('role', role);
        formData.append('is_reset', isReset ? '1' : '0');
        
        const response = await fetch('users?ajax=send_whatsapp', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Reset button state first
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Sent!';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-success');
            
            // Show success toast
            showSuccessToast('Credentials sent successfully on WhatsApp!');
            
            // Auto-close modal after 1.5 seconds
            setTimeout(() => {
                const modalEl = document.getElementById(modalId);
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    // Modal was shown via PHP, hide manually
                    modalEl.style.display = 'none';
                    modalEl.classList.remove('show');
                }
                // Reset button for next use
                btn.innerHTML = '<i class="bi bi-whatsapp me-2"></i>Send via WhatsApp';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-success');
                btn.disabled = false;
            }, 1500);
        } else {
            // Keep modal open on failure
            alert('Failed to send: ' + (result.error || 'Unknown error'));
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    } catch (err) {
        console.error('WhatsApp send error:', err);
        // Keep modal open on failure
        alert('Failed to send message. Check console for details.');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

// Show success toast notification
function showSuccessToast(message) {
    // Create toast container if not exists
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle-fill me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Show toast
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
    toast.show();
    
    // Remove from DOM after hidden
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

// ========== RESEND CREDENTIALS ==========

function openResendModal(userId, username, phone, name, role) {
    document.getElementById('resend-user-id').value = userId;
    document.getElementById('resend-username').value = username;
    document.getElementById('resend-phone').value = phone;
    document.getElementById('resend-name').value = name;
    document.getElementById('resend-role').value = role;
    
    document.getElementById('resend-display-name').textContent = name || ucfirst(role);
    document.getElementById('resend-display-username').textContent = '@' + username;
    
    // Reset button state
    const btn = document.getElementById('confirmResendBtn');
    btn.innerHTML = '<i class="bi bi-whatsapp me-2"></i>Generate & Send New Password';
    btn.disabled = false;
    
    const modal = new bootstrap.Modal(document.getElementById('resendModal'));
    modal.show();
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

async function confirmResendCredentials() {
    const btn = document.getElementById('confirmResendBtn');
    const originalHtml = btn.innerHTML;
    
    const userId = document.getElementById('resend-user-id').value;
    const username = document.getElementById('resend-username').value;
    const phone = document.getElementById('resend-phone').value;
    const name = document.getElementById('resend-name').value;
    const role = document.getElementById('resend-role').value;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating & Sending...';
    
    try {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('username', username);
        formData.append('phone', phone);
        formData.append('name', name);
        formData.append('role', role);
        
        const response = await fetch('users?ajax=resend_credentials', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('resendModal'));
            if (modal) modal.hide();
            showSuccessToast('New password sent successfully on WhatsApp!');
        } else {
            alert('Failed: ' + (result.error || 'Unknown error'));
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    } catch (err) {
        console.error('Resend error:', err);
        alert('Failed to resend credentials. Check console.');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

// ========== TABLE FILTERING, SORTING, SEARCH & PAGINATION ==========

let currentFilter = 'all';
let currentPage = 1;
let perPage = 10;
let sortColumn = -1;
let sortDirection = 'asc';

// Get all table rows
function getAllRows() {
    return Array.from(document.querySelectorAll('#usersTableBody tr'));
}

// Filter by role
function filterByRole(role) {
    currentFilter = role;
    currentPage = 1;
    
    // Update filter button states
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.role === role) btn.classList.add('active');
    });
    
    applyFilters();
}

// Search users
function searchUsers() {
    currentPage = 1;
    applyFilters();
}

// Change per page
function changePerPage() {
    perPage = parseInt(document.getElementById('perPageSelect').value);
    currentPage = 1;
    applyFilters();
}

// Sort table
function sortTable(columnIndex) {
    const headers = document.querySelectorAll('.users-table th');
    
    // Toggle direction if same column
    if (sortColumn === columnIndex) {
        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn = columnIndex;
        sortDirection = 'asc';
    }
    
    // Update header styles
    headers.forEach((h, i) => {
        h.classList.remove('sorted');
        const icon = h.querySelector('.sort-icon');
        if (icon) icon.className = 'bi bi-arrow-down-up sort-icon';
    });
    
    if (columnIndex < headers.length - 1) { // Don't sort actions column
        headers[columnIndex].classList.add('sorted');
        const icon = headers[columnIndex].querySelector('.sort-icon');
        if (icon) icon.className = `bi bi-arrow-${sortDirection === 'asc' ? 'up' : 'down'} sort-icon`;
    }
    
    applyFilters();
}

// Apply all filters, search, sort, and pagination
function applyFilters() {
    const searchTerm = document.getElementById('userSearchInput').value.toLowerCase();
    const allRows = getAllRows();
    
    // Step 1: Filter by role
    let filteredRows = allRows.filter(row => {
        if (currentFilter === 'all') return true;
        return row.dataset.role === currentFilter;
    });
    
    // Step 2: Filter by search
    filteredRows = filteredRows.filter(row => {
        const text = row.textContent.toLowerCase();
        return text.includes(searchTerm);
    });
    
    // Step 3: Sort
    if (sortColumn >= 0 && sortColumn < 4) {
        filteredRows.sort((a, b) => {
            const aText = a.cells[sortColumn]?.textContent.trim().toLowerCase() || '';
            const bText = b.cells[sortColumn]?.textContent.trim().toLowerCase() || '';
            const comparison = aText.localeCompare(bText);
            return sortDirection === 'asc' ? comparison : -comparison;
        });
    }
    
    // Hide all rows first
    allRows.forEach(row => row.style.display = 'none');
    
    // Step 4: Pagination
    const totalFiltered = filteredRows.length;
    const totalPages = Math.ceil(totalFiltered / perPage);
    const startIndex = (currentPage - 1) * perPage;
    const endIndex = Math.min(startIndex + perPage, totalFiltered);
    
    // Show only current page rows
    for (let i = startIndex; i < endIndex; i++) {
        filteredRows[i].style.display = '';
    }
    
    // Update pagination info
    document.getElementById('showingFrom').textContent = totalFiltered > 0 ? startIndex + 1 : 0;
    document.getElementById('showingTo').textContent = endIndex;
    document.getElementById('totalEntries').textContent = totalFiltered;
    
    // Generate pagination buttons
    generatePagination(totalPages);
}

// Generate pagination buttons
function generatePagination(totalPages) {
    const nav = document.getElementById('paginationNav');
    nav.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${currentPage - 1}); return false;">«</a>`;
    nav.appendChild(prevLi);
    
    // Page numbers
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (endPage - startPage < maxVisible - 1) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>`;
        nav.appendChild(li);
    }
    
    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${currentPage + 1}); return false;">»</a>`;
    nav.appendChild(nextLi);
}

// Go to specific page
function goToPage(page) {
    const allRows = getAllRows();
    const searchTerm = document.getElementById('userSearchInput').value.toLowerCase();
    
    let filteredRows = allRows.filter(row => {
        if (currentFilter === 'all') return true;
        return row.dataset.role === currentFilter;
    }).filter(row => row.textContent.toLowerCase().includes(searchTerm));
    
    const totalPages = Math.ceil(filteredRows.length / perPage);
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    applyFilters();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    applyFilters();
});
</script>
<?php
};

include __DIR__ . '/views/partials/layout.php';
