<?php
/**
 * Centralized helpers: CSV layer (with locking + atomic writes), auth helpers,
 * RBAC/permissions, CSRF protection, flash messages, output escaping.
 */

// PHP 7.4 compatibility
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

// -----------------------------
// Generic helpers
// -----------------------------

function base_url(string $path = ''): string
{
    $base = rtrim((string)BASE_URL, '/');
    $path = '/' . ltrim($path, '/');
    return $base ? ($base . $path) : $path;
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function post(string $key, $default = null)
{
    return $_POST[$key] ?? $default;
}

function get(string $key, $default = null)
{
    return $_GET[$key] ?? $default;
}

// -----------------------------
// Flash messages
// -----------------------------

function flash_set(string $type, string $message): void
{
    $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (!isset($_SESSION['_flash'])) return null;
    $v = $_SESSION['_flash'];
    unset($_SESSION['_flash']);
    return $v;
}

// -----------------------------
// CSRF
// -----------------------------

function csrf_token(): string
{
    if (empty($_SESSION[CSRF_TOKEN_KEY])) {
        $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION[CSRF_TOKEN_KEY];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify_or_die(): void
{
    $sent = (string)post('csrf_token', '');
    $real = (string)($_SESSION[CSRF_TOKEN_KEY] ?? '');
    if (!$sent || !$real || !hash_equals($real, $sent)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}

// -----------------------------
// CSV Storage
// -----------------------------

function csv_init(string $file, array $headers): void
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    if (!file_exists($file)) {
        $fh = fopen($file, 'c+');
        if (!$fh) return;
        flock($fh, LOCK_EX);
        ftruncate($fh, 0);
        fputcsv($fh, $headers);
        fflush($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
    }
}

function csv_read_all(string $file): array
{
    if (!file_exists($file)) return [];
    $rows = [];

    $fh = fopen($file, 'r');
    if (!$fh) return [];

    flock($fh, LOCK_SH);
    $headers = fgetcsv($fh);
    if (!$headers) {
        flock($fh, LOCK_UN);
        fclose($fh);
        return [];
    }

    while (($data = fgetcsv($fh)) !== false) {
        if (count($data) === 1 && trim((string)$data[0]) === '') continue;
        $row = [];
        foreach ($headers as $i => $h) {
            $row[$h] = $data[$i] ?? '';
        }
        $rows[] = $row;
    }

    flock($fh, LOCK_UN);
    fclose($fh);
    return $rows;
}

function csv_find_by_id(string $file, string $id): ?array
{
    foreach (csv_read_all($file) as $row) {
        if ((string)($row['id'] ?? '') === (string)$id) return $row;
    }
    return null;
}

function csv_next_id(array $rows): int
{
    $max = 0;
    foreach ($rows as $r) {
        $id = (int)($r['id'] ?? 0);
        if ($id > $max) $max = $id;
    }
    return $max + 1;
}

/**
 * Atomic write: writes to temp file and renames.
 * Caller provides rows INCLUDING headers.
 */
function csv_atomic_write(string $file, array $headers, array $rows): bool
{
    $dir = dirname($file);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);

    $tmp = $file . '.tmp';
    $fh = fopen($tmp, 'w');
    if (!$fh) return false;

    // Writing temp: still acquire exclusive lock on original to reduce races.
    $lock = fopen($file, 'c+');
    if (!$lock) {
        fclose($fh);
        return false;
    }

    flock($lock, LOCK_EX);
    fputcsv($fh, $headers);
    foreach ($rows as $row) {
        $line = [];
        foreach ($headers as $h) {
            $line[] = $row[$h] ?? '';
        }
        fputcsv($fh, $line);
    }
    fflush($fh);
    fclose($fh);

    // Replace
    $ok = @rename($tmp, $file);
    flock($lock, LOCK_UN);
    fclose($lock);

    if (!$ok) {
        @unlink($tmp);
    }
    return $ok;
}

function csv_insert(string $file, array $row): array
{
    $rows = csv_read_all($file);
    
    // Read existing headers from file
    $headers = [];
    if (file_exists($file)) {
        $fh = fopen($file, 'r');
        if ($fh) {
            flock($fh, LOCK_SH);
            $headers = fgetcsv($fh) ?: [];
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }
    
    // If no headers yet, use keys from the row
    if (empty($headers)) {
        $headers = array_keys($row);
    }
    
    // IMPORTANT: Merge any NEW keys from $row into headers (to support new fields like linked_id)
    foreach (array_keys($row) as $key) {
        if (!in_array($key, $headers, true)) {
            $headers[] = $key;
        }
    }

    if (!in_array('id', $headers, true)) {
        array_unshift($headers, 'id');
    }

    $row['id'] = (string)csv_next_id($rows);
    $rows[] = $row;

    csv_atomic_write($file, $headers, $rows);
    return $row;
}

function csv_update_by_id(string $file, string $id, array $newData): bool
{
    if (!file_exists($file)) return false;

    $rows = csv_read_all($file);
    $fh = fopen($file, 'r');
    if (!$fh) return false;
    flock($fh, LOCK_SH);
    $headers = fgetcsv($fh) ?: [];
    flock($fh, LOCK_UN);
    fclose($fh);

    // IMPORTANT: Add any NEW keys from $newData to headers (supports adding linked_id to old CSVs)
    $headersChanged = false;
    foreach (array_keys($newData) as $key) {
        if ($key !== 'id' && !in_array($key, $headers, true)) {
            $headers[] = $key;
            $headersChanged = true;
        }
    }

    $updated = false;
    foreach ($rows as &$row) {
        if ((string)($row['id'] ?? '') === (string)$id) {
            foreach ($newData as $k => $v) {
                if ($k === 'id') continue;
                $row[$k] = $v;
            }
            $updated = true;
            break;
        }
    }

    if (!$updated) return false;
    return csv_atomic_write($file, $headers, $rows);
}

/**
 * Ensure CSV file has all required headers (migration helper).
 * Adds missing headers without losing existing data.
 */
function csv_ensure_headers(string $file, array $requiredHeaders): bool
{
    if (!file_exists($file)) return false;

    $fh = fopen($file, 'r');
    if (!$fh) return false;
    flock($fh, LOCK_SH);
    $existingHeaders = fgetcsv($fh) ?: [];
    flock($fh, LOCK_UN);
    fclose($fh);

    // Check if any headers are missing
    $missingHeaders = array_diff($requiredHeaders, $existingHeaders);
    if (empty($missingHeaders)) return true; // All headers present

    // Merge headers (preserve existing order, add missing at end)
    $newHeaders = array_merge($existingHeaders, $missingHeaders);

    // Re-read all data and rewrite with new headers
    $rows = csv_read_all($file);
    return csv_atomic_write($file, $newHeaders, $rows);
}

function csv_delete_by_id(string $file, string $id): bool
{
    if (!file_exists($file)) return false;

    $rows = csv_read_all($file);
    $fh = fopen($file, 'r');
    if (!$fh) return false;
    flock($fh, LOCK_SH);
    $headers = fgetcsv($fh) ?: [];
    flock($fh, LOCK_UN);
    fclose($fh);

    $before = count($rows);
    // PHP 7.x compatibility: avoid arrow functions
    $rows = array_values(array_filter($rows, function ($r) use ($id) {
        return (string)($r['id'] ?? '') !== (string)$id;
    }));
    if (count($rows) === $before) return false;
    return csv_atomic_write($file, $headers, $rows);
}

// -----------------------------
// Auth + RBAC
// -----------------------------

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return (bool)current_user();
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash_set('warning', 'Please log in to continue.');
        redirect('login');
    }
}

/**
 * Permission model (future-proof). For now:
 * - admin: all
 * - custom: all
 * - teacher: students.read, exams.read, exams.write, events.read
 * - student: profile.read, exams.read, events.read
 */
function permissions_for_role(string $role): array
{
    $role = strtolower(trim($role));
    $all = ['*'];

    $map = [
        'admin' => $all,
        'custom' => $all,
        'teacher' => [
            'dashboard.read',
            'students.read',
            'teachers.read',
            'exams.read', 'exams.write',
            'events.read',
            'profile.read',
        ],
        'student' => [
            'dashboard.read',
            'profile.read',
            'exams.read',
            'events.read',
        ],
    ];

    return $map[$role] ?? [];
}

function has_permission(string $perm): bool
{
    $u = current_user();
    if (!$u) return false;
    $perms = permissions_for_role((string)($u['role'] ?? ''));
    return in_array('*', $perms, true) || in_array($perm, $perms, true);
}

function require_permission(string $perm): void
{
    require_login();
    if (!has_permission($perm)) {
        http_response_code(403);
        include __DIR__ . '/views/403.php';
        exit;
    }
}

function authenticate_user(string $username, string $password): ?array
{
    $username = trim($username);
    if ($username === '' || $password === '') return null;

    $users = csv_read_all(DATA_PATH . '/users.csv');
    foreach ($users as $u) {
        if (strcasecmp((string)$u['username'], $username) === 0) {
            if (password_verify($password, (string)$u['password'])) {
                return [
                    'id' => (string)$u['id'],
                    'username' => (string)$u['username'],
                    'role' => (string)$u['role'],
                ];
            }
            return null;
        }
    }
    return null;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = $user;
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

// -----------------------------
// Domain helpers
// -----------------------------

function student_record_for_current_user(): ?array
{
    $u = current_user();
    if (!$u) return null;
    if (strtolower((string)$u['role']) !== 'student') return null;

    // Check linked_id first, then fall back to username == student_id
    $studentId = !empty($u['linked_id']) ? (string)$u['linked_id'] : (string)$u['username'];
    $students = csv_read_all(DATA_PATH . '/students.csv');
    foreach ($students as $s) {
        if ((string)$s['id'] === $studentId) return $s;
    }
    return null;
}

function teacher_record_for_current_user(): ?array
{
    $u = current_user();
    if (!$u) return null;
    if (strtolower((string)$u['role']) !== 'teacher') return null;

    // Check linked_id first, then fall back to username == teacher_id
    $teacherId = !empty($u['linked_id']) ? (string)$u['linked_id'] : (string)$u['username'];
    $teachers = csv_read_all(DATA_PATH . '/teachers.csv');
    foreach ($teachers as $t) {
        if ((string)$t['id'] === $teacherId) return $t;
    }
    return null;
}

// -----------------------------
// Credential Generation Functions
// -----------------------------

function generate_random_password(int $length = 12): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789@#$%';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

function generate_random_username(string $role): string
{
    // PHP 7.x compatibility: avoid match expressions
    $roleLower = strtolower($role);
    if ($roleLower === 'student') {
        $prefix = 'STU';
    } elseif ($roleLower === 'teacher') {
        $prefix = 'TCH';
    } else {
        $prefix = 'USR';
    }
    $year = date('Y');
    $random = str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    
    // Check uniqueness
    $users = csv_read_all(DATA_PATH . '/users.csv');
    $username = $prefix . $year . $random;
    $attempts = 0;
    while ($attempts < 100) {
        $exists = false;
        foreach ($users as $u) {
            if (strcasecmp((string)$u['username'], $username) === 0) {
                $exists = true;
                break;
            }
        }
        if (!$exists) break;
        $random = str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        $username = $prefix . $year . $random;
        $attempts++;
    }
    return $username;
}

function get_linked_record_name(string $role, string $linkedId): string
{
    if (empty($linkedId)) return '-';
    
    if ($role === 'student') {
        $record = csv_find_by_id(DATA_PATH . '/students.csv', $linkedId);
        if ($record) {
            return e($record['name']) . ' (' . e($record['class']) . '-' . e($record['section']) . ')';
        }
    } elseif ($role === 'teacher') {
        $record = csv_find_by_id(DATA_PATH . '/teachers.csv', $linkedId);
        if ($record) {
            return e($record['name']) . ' (' . e($record['subject']) . ')';
        }
    }
    return '-';
}


function upcoming_exams(int $limit = 3, ?string $class = null): array
{
    $exams = csv_read_all(DATA_PATH . '/exams.csv');
    $today = date('Y-m-d');
    $filtered = array_filter($exams, function ($e) use ($today, $class) {
        $date = (string)($e['date'] ?? '');
        if ($date < $today) return false;
        if ($class && (string)($e['class'] ?? '') !== $class) return false;
        return true;
    });
    // PHP 7.x compatibility: avoid arrow functions
    usort($filtered, function ($a, $b) {
        return strcmp((string)($a['date'] ?? ''), (string)($b['date'] ?? ''));
    });
    return array_slice($filtered, 0, $limit);
}

function recent_events(int $limit = 5): array
{
    $events = csv_read_all(DATA_PATH . '/events.csv');
    // PHP 7.x compatibility: avoid arrow functions
    usort($events, function ($a, $b) {
        return strcmp((string)($b['date'] ?? ''), (string)($a['date'] ?? ''));
    });
    return array_slice($events, 0, $limit);
}

// -----------------------------
// File Upload Functions
// -----------------------------

define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('UPLOAD_URL', 'uploads');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

function ensure_upload_dir(): bool
{
    if (!is_dir(UPLOAD_PATH)) {
        return mkdir(UPLOAD_PATH, 0755, true);
    }
    return true;
}

function upload_image(array $file, string $prefix = 'img'): array
{
    $result = ['success' => false, 'error' => '', 'path' => '', 'url' => ''];
    
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $result['error'] = 'No file uploaded.';
        return $result;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'Upload error: ' . $file['error'];
        return $result;
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $result['error'] = 'File too large. Maximum size is 5MB.';
        return $result;
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) {
        $result['error'] = 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP.';
        return $result;
    }
    
    // PHP 7.x compatibility: avoid match expressions
    if ($mime === 'image/jpeg') {
        $ext = 'jpg';
    } elseif ($mime === 'image/png') {
        $ext = 'png';
    } elseif ($mime === 'image/gif') {
        $ext = 'gif';
    } elseif ($mime === 'image/webp') {
        $ext = 'webp';
    } else {
        $ext = 'jpg';
    }
    
    ensure_upload_dir();
    
    $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $filepath = UPLOAD_PATH . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $result['success'] = true;
        $result['path'] = $filepath;
        $result['url'] = base_url(UPLOAD_URL . '/' . $filename);
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'Failed to save file.';
    }
    
    return $result;
}

function delete_upload(string $filename): bool
{
    if (empty($filename)) return false;
    $filepath = UPLOAD_PATH . '/' . basename($filename);
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// -----------------------------
// Site Settings Functions
// -----------------------------

function get_site_setting(string $key, string $default = ''): string
{
    $settingsFile = DATA_PATH . '/site_settings.csv';
    if (!file_exists($settingsFile)) {
        return $default;
    }
    $settings = csv_read_all($settingsFile);
    foreach ($settings as $s) {
        if ((string)($s['key'] ?? '') === $key) {
            return (string)($s['value'] ?? $default);
        }
    }
    return $default;
}

// Notification Settings Functions
// -----------------------------

function get_notification_setting(string $key, string $default = ''): string
{
    $settings = csv_read_all(DATA_PATH . '/notification_settings.csv');
    foreach ($settings as $s) {
        if ((string)($s['key'] ?? '') === $key) {
            return (string)($s['value'] ?? $default);
        }
    }
    return $default;
}

function save_notification_setting(string $key, string $value, string $channel = ''): bool
{
    $settings = csv_read_all(DATA_PATH . '/notification_settings.csv');
    $found = false;
    
    foreach ($settings as &$s) {
        if ((string)($s['key'] ?? '') === $key) {
            $s['value'] = $value;
            $s['channel'] = $channel;
            $found = true;
            break;
        }
    }
    
    if ($found) {
        $fh = fopen(DATA_PATH . '/notification_settings.csv', 'r');
        if (!$fh) return false;
        flock($fh, LOCK_SH);
        $headers = fgetcsv($fh) ?: ['id', 'key', 'value', 'channel'];
        flock($fh, LOCK_UN);
        fclose($fh);
        return csv_atomic_write(DATA_PATH . '/notification_settings.csv', $headers, $settings);
    } else {
        csv_insert(DATA_PATH . '/notification_settings.csv', [
            'key' => $key,
            'value' => $value,
            'channel' => $channel
        ]);
        return true;
    }
}

function send_test_email(): array
{
    $result = ['success' => false, 'error' => ''];
    
    if (get_notification_setting('smtp_enabled', '0') !== '1') {
        $result['error'] = 'Email is not enabled.';
        return $result;
    }
    
    $host = get_notification_setting('smtp_host', '');
    $port = (int)get_notification_setting('smtp_port', '587');
    $username = get_notification_setting('smtp_username', '');
    $password = get_notification_setting('smtp_password', '');
    $fromEmail = get_notification_setting('smtp_from_email', '');
    $fromName = get_notification_setting('smtp_from_name', APP_NAME);
    $testEmail = get_notification_setting('smtp_test_email', '');
    
    if (empty($host) || empty($username) || empty($password)) {
        $result['error'] = 'SMTP settings are incomplete.';
        return $result;
    }
    
    if (empty($testEmail)) {
        $result['error'] = 'Please enter a Test Email Address to send test emails.';
        return $result;
    }
    
    // Use PHP's mail function for simplicity (in production, use PHPMailer)
    $to = $testEmail;
    $subject = 'Test Email from ' . APP_NAME;
    $message = 'This is a test email from your ' . APP_NAME . ' notification system.';
    $headers = "From: $fromName <$fromEmail>\r\nContent-Type: text/plain; charset=UTF-8";
    
    if (@mail($to, $subject, $message, $headers)) {
        $result['success'] = true;
    } else {
        $result['error'] = 'Failed to send email. Check SMTP settings.';
    }
    
    return $result;
}

function send_telegram_message(string $message): array
{
    $result = ['success' => false, 'error' => ''];
    
    if (get_notification_setting('telegram_enabled', '0') !== '1') {
        $result['error'] = 'Telegram is not enabled.';
        return $result;
    }
    
    $token = get_notification_setting('telegram_token', '');
    $chatId = get_notification_setting('telegram_chat_id', '');
    
    if (empty($token) || empty($chatId)) {
        $result['error'] = 'Telegram settings are incomplete.';
        return $result;
    }
    
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        $result['error'] = 'cURL error: ' . $error;
    } elseif ($httpCode !== 200) {
        $result['error'] = 'Telegram API error (HTTP ' . $httpCode . ')';
    } else {
        $decoded = json_decode($response, true);
        if ($decoded && isset($decoded['ok']) && $decoded['ok']) {
            $result['success'] = true;
        } else {
            $result['error'] = $decoded['description'] ?? 'Unknown error';
        }
    }
    
    return $result;
}

function send_whatsapp_message(string $phone, string $message): array
{
    $result = ['success' => false, 'error' => ''];
    
    if (get_notification_setting('whatsapp_enabled', '0') !== '1') {
        $result['error'] = 'WhatsApp is not enabled.';
        return $result;
    }
    
    $instance = get_notification_setting('ultramsg_instance', '');
    $token = get_notification_setting('ultramsg_token', '');
    
    if (empty($instance) || empty($token)) {
        $result['error'] = 'UltraMSG settings are incomplete.';
        return $result;
    }
    
    // Clean phone number
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    if (strlen($phone) > 15 || strlen($phone) < 10) {
        $result['error'] = 'Invalid phone number format.';
        return $result;
    }
    
    $url = "https://api.ultramsg.com/{$instance}/messages/chat";
    $data = [
        'token' => $token,
        'to' => $phone,
        'body' => $message
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        $result['error'] = 'cURL error: ' . $error;
    } elseif ($httpCode !== 200) {
        $result['error'] = 'UltraMSG API error (HTTP ' . $httpCode . ')';
    } else {
        $decoded = json_decode($response, true);
        if ($decoded && isset($decoded['sent']) && $decoded['sent'] === 'true') {
            $result['success'] = true;
        } else {
            $result['error'] = $decoded['message'] ?? 'Unknown error';
        }
    }
    
    return $result;
}

function send_notification_to_recipients(string $message, array $recipients, ?string $class = null): array
{
    $results = ['email' => 0, 'telegram' => 0, 'whatsapp' => 0, 'errors' => []];
    
    $students = csv_read_all(DATA_PATH . '/students.csv');
    $teachers = csv_read_all(DATA_PATH . '/teachers.csv');
    
    $contacts = [];
    
    // Collect student contacts
    if (in_array('students', $recipients) || in_array('both', $recipients)) {
        foreach ($students as $s) {
            if ($class && (string)$s['class'] !== (string)$class) continue;
            if (!empty($s['phone'])) {
                $contacts[] = ['phone' => $s['phone'], 'email' => $s['email'] ?? '', 'type' => 'student'];
            }
        }
    }
    
    // Collect teacher contacts
    if (in_array('teachers', $recipients) || in_array('both', $recipients)) {
        foreach ($teachers as $t) {
            if (!empty($t['phone'])) {
                $contacts[] = ['phone' => $t['phone'], 'email' => $t['email'] ?? '', 'type' => 'teacher'];
            }
        }
    }
    
    // Send Telegram (group message)
    if (get_notification_setting('telegram_enabled', '0') === '1') {
        $tgResult = send_telegram_message($message);
        if ($tgResult['success']) {
            $results['telegram'] = 1;
        } else {
            $results['errors'][] = 'Telegram: ' . $tgResult['error'];
        }
    }
    
    // Send WhatsApp to each contact
    if (get_notification_setting('whatsapp_enabled', '0') === '1') {
        foreach ($contacts as $c) {
            if (!empty($c['phone'])) {
                $waResult = send_whatsapp_message($c['phone'], $message);
                if ($waResult['success']) {
                    $results['whatsapp']++;
                }
            }
        }
    }
    
    return $results;
}
