<?php
require_once __DIR__ . '/bootstrap.php';

if (is_logged_in()) redirect('dashboard');

$errors = [];
if (request_method() === 'POST') {
    csrf_verify_or_die();

    $username = trim((string)post('username', ''));
    $password = (string)post('password', '');

    if ($username === '') $errors['username'] = 'Student ID is required.';
    if ($password === '') $errors['password'] = 'Password is required.';

    if (!$errors) {
        $user = authenticate_user($username, $password);
        if ($user && strtolower($user['role'] ?? '') === 'student') {
            login_user($user);
            flash_set('success', 'Welcome back, ' . $user['username'] . '!');
            redirect('dashboard');
        } elseif ($user) {
            $errors['general'] = 'This portal is for students only. Use Admin/Teacher login.';
        } else {
            $errors['general'] = 'Invalid Student ID or password.';
        }
    }
}

// Get school settings
$settingsFile = DATA_PATH . '/site_settings.csv';
$settings = [];
if (file_exists($settingsFile)) {
    $allSettings = csv_read_all($settingsFile);
    foreach ($allSettings as $s) {
        $settings[$s['key']] = $s['value'];
    }
}
$schoolName = $settings['school_name'] ?? SCHOOL_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Login - <?= e($schoolName) ?></title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <style>
    :root {
      --primary: #6366f1;
      --secondary: #ec4899;
      --accent: #06b6d4;
      --success: #10b981;
      --warning: #f59e0b;
      --orange: #f97316;
      --bg-light: #fef7ff;
      --text-dark: #1f2937;
      --text-muted: #6b7280;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Nunito', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }

    /* Animated Shapes Background */
    .shapes-container {
      position: fixed;
      inset: 0;
      overflow: hidden;
      z-index: 0;
    }

    .shape {
      position: absolute;
      border-radius: 50%;
      animation: floatShape 15s ease-in-out infinite;
    }

    .shape-1 {
      width: 300px; height: 300px;
      background: linear-gradient(135deg, #fbbf24, #f97316);
      top: -100px; left: -100px;
      animation-delay: 0s;
      opacity: 0.6;
    }

    .shape-2 {
      width: 200px; height: 200px;
      background: linear-gradient(135deg, #06b6d4, #3b82f6);
      top: 20%; right: -50px;
      animation-delay: -5s;
      border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
      opacity: 0.7;
    }

    .shape-3 {
      width: 150px; height: 150px;
      background: linear-gradient(135deg, #ec4899, #f43f5e);
      bottom: 10%; left: 10%;
      animation-delay: -2s;
      border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
      opacity: 0.6;
    }

    .shape-4 {
      width: 100px; height: 100px;
      background: linear-gradient(135deg, #10b981, #84cc16);
      bottom: 30%; right: 15%;
      animation-delay: -7s;
      opacity: 0.7;
    }

    .shape-5 {
      width: 80px; height: 80px;
      background: linear-gradient(135deg, #8b5cf6, #a855f7);
      top: 50%; left: 20%;
      animation-delay: -3s;
      border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
      opacity: 0.5;
    }

    @keyframes floatShape {
      0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
      25% { transform: translate(30px, -30px) rotate(90deg) scale(1.1); }
      50% { transform: translate(-20px, 40px) rotate(180deg) scale(0.9); }
      75% { transform: translate(40px, 20px) rotate(270deg) scale(1.05); }
    }

    /* Stars/Sparkles */
    .sparkle {
      position: absolute;
      width: 10px; height: 10px;
      background: white;
      border-radius: 50%;
      animation: twinkle 2s ease-in-out infinite;
    }

    .sparkle:nth-child(1) { top: 15%; left: 30%; animation-delay: 0s; }
    .sparkle:nth-child(2) { top: 25%; right: 25%; animation-delay: 0.5s; }
    .sparkle:nth-child(3) { bottom: 35%; left: 40%; animation-delay: 1s; }
    .sparkle:nth-child(4) { bottom: 20%; right: 35%; animation-delay: 1.5s; }
    .sparkle:nth-child(5) { top: 40%; left: 15%; animation-delay: 0.3s; }
    .sparkle:nth-child(6) { top: 60%; right: 10%; animation-delay: 0.8s; }

    @keyframes twinkle {
      0%, 100% { opacity: 0.3; transform: scale(1); }
      50% { opacity: 1; transform: scale(1.5); }
    }

    /* Main Container */
    .login-container {
      position: relative;
      z-index: 10;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-card {
      background: white;
      border-radius: 32px;
      box-shadow: 
        0 25px 50px -12px rgba(0,0,0,0.25),
        0 0 0 1px rgba(255,255,255,0.1);
      max-width: 450px;
      width: 100%;
      padding: 50px 40px;
      animation: cardPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
      overflow: hidden;
    }

    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, #f97316, #ec4899, #8b5cf6, #06b6d4, #10b981);
    }

    @keyframes cardPop {
      from { opacity: 0; transform: scale(0.9) translateY(30px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* Mascot */
    .mascot {
      width: 100px;
      height: 100px;
      margin: -80px auto 20px;
      background: linear-gradient(135deg, #fbbf24, #f97316);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 30px rgba(249, 115, 22, 0.4);
      animation: bounce 2s ease-in-out infinite;
      border: 5px solid white;
    }

    .mascot i {
      font-size: 48px;
      color: white;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    /* Header */
    .login-header {
      text-align: center;
      margin-bottom: 35px;
    }

    .login-header h1 {
      font-family: 'Fredoka', sans-serif;
      font-size: 2rem;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .login-header p {
      color: var(--text-muted);
      font-size: 1rem;
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
      font-size: 0.9rem;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 20px;
      transition: color 0.3s;
    }

    .form-input {
      width: 100%;
      padding: 16px 16px 16px 50px;
      background: #f8fafc;
      border: 2px solid #e2e8f0;
      border-radius: 16px;
      font-size: 1rem;
      color: var(--text-dark);
      font-family: 'Nunito', sans-serif;
      transition: all 0.3s ease;
    }

    .form-input::placeholder {
      color: #94a3b8;
    }

    .form-input:focus {
      outline: none;
      border-color: var(--primary);
      background: white;
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .input-wrapper:focus-within i {
      color: var(--primary);
    }

    .form-input.is-invalid {
      border-color: #ef4444;
      background: #fef2f2;
    }

    .invalid-feedback {
      color: #ef4444;
      font-size: 0.85rem;
      margin-top: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: 500;
    }

    /* Login Button */
    .btn-login {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      border-radius: 16px;
      font-size: 1.1rem;
      font-weight: 700;
      color: white;
      font-family: 'Fredoka', sans-serif;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 10px;
    }

    .btn-login:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    /* Alert */
    .alert-error {
      background: linear-gradient(135deg, #fef2f2, #fee2e2);
      border: 2px solid #fecaca;
      color: #dc2626;
      padding: 14px 18px;
      border-radius: 14px;
      margin-bottom: 20px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Footer Links */
    .login-footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 2px dashed #e2e8f0;
    }

    .login-footer a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }

    .login-footer a:hover {
      color: var(--secondary);
    }

    .teacher-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 15px;
      padding: 10px 20px;
      background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
      border-radius: 30px;
      color: #0369a1;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s;
    }

    .teacher-link:hover {
      background: linear-gradient(135deg, #e0f2fe, #bae6fd);
      transform: translateY(-2px);
    }

    /* Fun elements */
    .emoji-row {
      display: flex;
      justify-content: center;
      gap: 15px;
      font-size: 1.5rem;
      margin-bottom: 20px;
    }

    .emoji {
      animation: wiggle 2s ease-in-out infinite;
    }

    .emoji:nth-child(1) { animation-delay: 0s; }
    .emoji:nth-child(2) { animation-delay: 0.2s; }
    .emoji:nth-child(3) { animation-delay: 0.4s; }

    @keyframes wiggle {
      0%, 100% { transform: rotate(-5deg); }
      50% { transform: rotate(5deg); }
    }

    /* Loading */
    .loading-spinner {
      display: none;
      width: 22px;
      height: 22px;
      border: 3px solid rgba(255,255,255,0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .btn-login.loading .btn-text { display: none; }
    .btn-login.loading .loading-spinner { display: block; }

    @media (max-width: 480px) {
      .login-card {
        padding: 40px 25px;
        border-radius: 24px;
      }
      .login-header h1 { font-size: 1.6rem; }
      .mascot { width: 80px; height: 80px; margin-top: -60px; }
      .mascot i { font-size: 36px; }
    }
  </style>
</head>
<body>
  <!-- Animated Background -->
  <div class="shapes-container">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="shape shape-4"></div>
    <div class="shape shape-5"></div>
    <div class="sparkle"></div>
    <div class="sparkle"></div>
    <div class="sparkle"></div>
    <div class="sparkle"></div>
    <div class="sparkle"></div>
    <div class="sparkle"></div>
  </div>

  <div class="login-container">
    <div class="login-card">
      <div class="mascot">
        <i class="bi bi-mortarboard-fill"></i>
      </div>

      <div class="login-header">
        <div class="emoji-row">
          <span class="emoji">📚</span>
          <span class="emoji">✨</span>
          <span class="emoji">🎓</span>
        </div>
        <h1>Student Portal</h1>
        <p>Login to access your dashboard</p>
      </div>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= e($errors['general']) ?>
        </div>
      <?php endif; ?>

      <form method="post" id="loginForm">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label">Student ID</label>
          <div class="input-wrapper">
            <i class="bi bi-person-badge"></i>
            <input type="text" name="username" class="form-input <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                   placeholder="Enter your Student ID" value="<?= e((string)post('username', '')) ?>" autocomplete="username">
          </div>
          <?php if (isset($errors['username'])): ?>
            <div class="invalid-feedback"><i class="bi bi-exclamation-circle"></i> <?= e($errors['username']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrapper">
            <i class="bi bi-lock-fill"></i>
            <input type="password" name="password" class="form-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                   placeholder="Enter your password" autocomplete="current-password">
          </div>
          <?php if (isset($errors['password'])): ?>
            <div class="invalid-feedback"><i class="bi bi-exclamation-circle"></i> <?= e($errors['password']) ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn-login">
          <span class="btn-text">Let's Go! 🚀</span>
          <div class="loading-spinner"></div>
        </button>
      </form>

      <div class="login-footer">
        <a href="<?= e(base_url('')) ?>">← Back to Home</a>
        <br>
        <a href="<?= e(base_url('login')) ?>" class="teacher-link">
          <i class="bi bi-person-workspace"></i>
          Admin / Teacher Login
        </a>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', function() {
      this.querySelector('.btn-login').classList.add('loading');
    });
  </script>
</body>
</html>
