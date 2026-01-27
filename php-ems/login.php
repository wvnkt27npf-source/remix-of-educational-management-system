<?php
require_once __DIR__ . '/bootstrap.php';

if (is_logged_in()) redirect('dashboard');

$errors = [];
if (request_method() === 'POST') {
    csrf_verify_or_die();

    $username = trim((string)post('username', ''));
    $password = (string)post('password', '');

    if ($username === '') $errors['username'] = 'Username is required.';
    if ($password === '') $errors['password'] = 'Password is required.';

    if (!$errors) {
        $user = authenticate_user($username, $password);
        if ($user) {
            login_user($user);
            flash_set('success', 'Welcome back, ' . $user['username'] . '!');
            redirect('dashboard');
        }
        $errors['general'] = 'Invalid username or password.';
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
$schoolTagline = $settings['school_tagline'] ?? SCHOOL_TAGLINE;
$primaryColor = $settings['primary_color'] ?? '#4f6ef7';
$secondaryColor = $settings['secondary_color'] ?? '#a855f7';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - <?= e($schoolName) ?></title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <style>
    :root {
      --primary: <?= e($primaryColor) ?>;
      --secondary: <?= e($secondaryColor) ?>;
      --accent: #00d4ff;
      --bg-dark: #050505;
      --bg-card: rgba(255,255,255,0.02);
      --text-primary: rgba(255,255,255,0.95);
      --text-secondary: rgba(255,255,255,0.7);
      --text-muted: rgba(255,255,255,0.4);
      --glass-border: rgba(255,255,255,0.08);
      --success: #22c55e;
      --danger: #ef4444;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Outfit', sans-serif;
      background: var(--bg-dark);
      color: var(--text-primary);
      min-height: 100vh;
      overflow-x: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Animated Mesh Background */
    .mesh-bg {
      position: fixed;
      inset: 0;
      z-index: 0;
      background: 
        radial-gradient(ellipse 80% 50% at 20% 40%, rgba(79, 110, 247, 0.15), transparent),
        radial-gradient(ellipse 60% 40% at 80% 20%, rgba(168, 85, 247, 0.1), transparent),
        radial-gradient(ellipse 50% 50% at 60% 80%, rgba(0, 212, 255, 0.08), transparent);
      animation: meshMove 20s ease-in-out infinite;
    }

    @keyframes meshMove {
      0%, 100% { filter: hue-rotate(0deg); }
      50% { filter: hue-rotate(15deg); }
    }

    /* Noise Overlay */
    .noise-overlay {
      position: fixed;
      inset: 0;
      z-index: 1;
      opacity: 0.03;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%' height='100%' filter='url(%23noise)'/%3E%3C/svg%3E");
      pointer-events: none;
    }

    /* Floating Orbs */
    .orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(80px);
      z-index: 0;
      animation: float 10s ease-in-out infinite;
    }

    .orb-1 {
      width: 400px;
      height: 400px;
      background: var(--primary);
      opacity: 0.15;
      top: -100px;
      left: -100px;
      animation-delay: 0s;
    }

    .orb-2 {
      width: 300px;
      height: 300px;
      background: var(--secondary);
      opacity: 0.12;
      bottom: -50px;
      right: -50px;
      animation-delay: -5s;
    }

    .orb-3 {
      width: 200px;
      height: 200px;
      background: var(--accent);
      opacity: 0.1;
      top: 50%;
      left: 50%;
      animation-delay: -2.5s;
    }

    @keyframes float {
      0%, 100% { transform: translate(0, 0) scale(1); }
      33% { transform: translate(30px, -30px) scale(1.05); }
      66% { transform: translate(-20px, 20px) scale(0.95); }
    }

    /* Main Container */
    .login-container {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 1100px;
      margin: 20px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      background: var(--bg-card);
      border: 1px solid var(--glass-border);
      border-radius: 32px;
      overflow: hidden;
      backdrop-filter: blur(20px);
      box-shadow: 
        0 50px 100px -20px rgba(0,0,0,0.5),
        0 0 0 1px rgba(255,255,255,0.05) inset;
      animation: containerSlide 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes containerSlide {
      from {
        opacity: 0;
        transform: translateY(40px) scale(0.98);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    /* Left Panel - Branding */
    .brand-panel {
      padding: 60px 50px;
      background: linear-gradient(135deg, rgba(79, 110, 247, 0.1) 0%, rgba(168, 85, 247, 0.05) 100%);
      border-right: 1px solid var(--glass-border);
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .brand-panel::before {
      content: '';
      position: absolute;
      inset: 0;
      background: 
        radial-gradient(circle at 30% 70%, rgba(79, 110, 247, 0.1), transparent 50%),
        radial-gradient(circle at 70% 30%, rgba(168, 85, 247, 0.08), transparent 50%);
    }

    .brand-content {
      position: relative;
      z-index: 1;
    }

    .brand-logo {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 30px;
      box-shadow: 
        0 20px 40px rgba(79, 110, 247, 0.3),
        0 0 0 1px rgba(255,255,255,0.1) inset;
      animation: logoFloat 4s ease-in-out infinite;
    }

    @keyframes logoFloat {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-10px) rotate(2deg); }
    }

    .brand-logo i {
      font-size: 36px;
      color: white;
    }

    .brand-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.2rem;
      font-weight: 700;
      background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.7) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 10px;
      letter-spacing: -0.5px;
    }

    .brand-tagline {
      font-size: 1rem;
      color: var(--text-secondary);
      margin-bottom: 40px;
      line-height: 1.6;
    }

    .features-list {
      list-style: none;
    }

    .features-list li {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px 0;
      border-bottom: 1px solid var(--glass-border);
      animation: featureSlide 0.6s ease-out backwards;
    }

    .features-list li:nth-child(1) { animation-delay: 0.2s; }
    .features-list li:nth-child(2) { animation-delay: 0.3s; }
    .features-list li:nth-child(3) { animation-delay: 0.4s; }
    .features-list li:nth-child(4) { animation-delay: 0.5s; }

    @keyframes featureSlide {
      from {
        opacity: 0;
        transform: translateX(-20px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .features-list li:last-child {
      border-bottom: none;
    }

    .feature-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      flex-shrink: 0;
      transition: transform 0.3s ease;
    }

    .features-list li:hover .feature-icon {
      transform: scale(1.1);
    }

    .feature-icon.blue { 
      background: rgba(79, 110, 247, 0.15); 
      color: #6d8cfa; 
      box-shadow: 0 0 20px rgba(79, 110, 247, 0.2);
    }
    .feature-icon.cyan { 
      background: rgba(0, 212, 255, 0.15); 
      color: #00d4ff; 
      box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
    }
    .feature-icon.purple { 
      background: rgba(168, 85, 247, 0.15); 
      color: #c084fc; 
      box-shadow: 0 0 20px rgba(168, 85, 247, 0.2);
    }
    .feature-icon.green { 
      background: rgba(34, 197, 94, 0.15); 
      color: #4ade80; 
      box-shadow: 0 0 20px rgba(34, 197, 94, 0.2);
    }

    .feature-text h4 {
      font-size: 0.95rem;
      font-weight: 600;
      margin-bottom: 4px;
      color: var(--text-primary);
    }

    .feature-text p {
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    /* Right Panel - Login Form */
    .login-panel {
      padding: 60px 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-header {
      margin-bottom: 35px;
    }

    .login-header h2 {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text-primary);
    }

    .login-header p {
      color: var(--text-muted);
      font-size: 0.95rem;
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 24px;
    }

    .form-label {
      display: block;
      font-size: 0.85rem;
      font-weight: 500;
      margin-bottom: 10px;
      color: var(--text-secondary);
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 20px;
      transition: all 0.3s ease;
    }

    .form-input {
      width: 100%;
      padding: 18px 20px 18px 54px;
      background: rgba(255,255,255,0.03);
      border: 1px solid var(--glass-border);
      border-radius: 16px;
      font-size: 1rem;
      color: var(--text-primary);
      font-family: 'Outfit', sans-serif;
      transition: all 0.3s ease;
    }

    .form-input::placeholder {
      color: var(--text-muted);
    }

    .form-input:focus {
      outline: none;
      border-color: var(--primary);
      background: rgba(79, 110, 247, 0.05);
      box-shadow: 
        0 0 0 4px rgba(79, 110, 247, 0.1),
        0 0 20px rgba(79, 110, 247, 0.1);
    }

    .input-wrapper:focus-within i {
      color: var(--primary);
    }

    .form-input.is-invalid {
      border-color: var(--danger);
      background: rgba(239, 68, 68, 0.05);
    }

    .invalid-feedback {
      color: #fca5a5;
      font-size: 0.8rem;
      margin-top: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* Login Button */
    .btn-login {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      border-radius: 16px;
      font-size: 1rem;
      font-weight: 600;
      color: white;
      font-family: 'Outfit', sans-serif;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .btn-login::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 20px 40px rgba(79, 110, 247, 0.4),
        0 0 0 1px rgba(255,255,255,0.1) inset;
    }

    .btn-login:hover::before {
      opacity: 1;
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .btn-login span {
      position: relative;
      z-index: 1;
    }

    /* Loading Spinner */
    .loading-spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255,255,255,0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .btn-login.loading .loading-spinner {
      display: block;
    }

    .btn-login.loading .btn-text {
      opacity: 0.7;
    }

    /* Alert Error */
    .alert-error {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #fca5a5;
      padding: 16px 20px;
      border-radius: 14px;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.9rem;
      animation: shake 0.5s ease;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }

    .alert-error i {
      font-size: 22px;
    }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      margin: 30px 0;
      gap: 15px;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--glass-border);
    }

    .divider span {
      font-size: 0.75rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    /* Demo Credentials */
    .demo-credentials {
      background: rgba(34, 197, 94, 0.06);
      border: 1px solid rgba(34, 197, 94, 0.15);
      border-radius: 16px;
      padding: 20px;
    }

    .demo-credentials h5 {
      font-size: 0.8rem;
      font-weight: 600;
      color: #4ade80;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .demo-credentials p {
      font-size: 0.9rem;
      color: var(--text-secondary);
      font-family: 'JetBrains Mono', 'Monaco', monospace;
    }

    .demo-credentials code {
      background: rgba(0, 212, 255, 0.1);
      color: var(--accent);
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.85rem;
    }

    /* Footer */
    .login-footer {
      margin-top: 30px;
      text-align: center;
      color: var(--text-muted);
      font-size: 0.8rem;
    }

    .login-footer a {
      color: var(--primary);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .login-footer a:hover {
      color: var(--accent);
      text-decoration: underline;
    }

    /* Back to Home Button */
    .back-home {
      position: absolute;
      top: 20px;
      left: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      padding: 10px 16px;
      background: var(--bg-card);
      border: 1px solid var(--glass-border);
      border-radius: 12px;
      transition: all 0.3s ease;
      z-index: 100;
    }

    .back-home:hover {
      background: rgba(255,255,255,0.05);
      color: var(--text-primary);
      transform: translateX(-3px);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .login-container {
        grid-template-columns: 1fr;
        max-width: 500px;
      }

      .brand-panel {
        padding: 40px 35px;
        border-right: none;
        border-bottom: 1px solid var(--glass-border);
      }

      .brand-logo {
        width: 60px;
        height: 60px;
        margin-bottom: 20px;
        border-radius: 18px;
      }

      .brand-logo i {
        font-size: 28px;
      }

      .brand-title {
        font-size: 1.6rem;
      }

      .brand-tagline {
        font-size: 0.9rem;
        margin-bottom: 25px;
      }

      .features-list li {
        padding: 12px 0;
      }

      .feature-icon {
        width: 42px;
        height: 42px;
        font-size: 18px;
      }

      .login-panel {
        padding: 40px 35px;
      }
    }

    @media (max-width: 576px) {
      body {
        align-items: flex-start;
        padding-top: 60px;
      }

      .login-container {
        margin: 15px;
        border-radius: 24px;
      }

      .brand-panel {
        padding: 30px 25px;
      }

      .login-panel {
        padding: 30px 25px;
      }

      .brand-title {
        font-size: 1.4rem;
      }

      .login-header h2 {
        font-size: 1.5rem;
      }

      .form-input {
        padding: 16px 16px 16px 48px;
      }

      .btn-login {
        padding: 16px;
      }

      .back-home {
        top: 10px;
        left: 10px;
        padding: 8px 12px;
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>
  <!-- Background -->
  <div class="mesh-bg"></div>
  <div class="noise-overlay"></div>
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>

  <!-- Back to Home -->
  <a href="<?= e(base_url('/')) ?>" class="back-home">
    <i class="bi bi-arrow-left"></i>
    <span>Back to Home</span>
  </a>

  <!-- Main Container -->
  <div class="login-container">
    <!-- Left Panel - Branding -->
    <div class="brand-panel">
      <div class="brand-content">
        <div class="brand-logo">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h1 class="brand-title"><?= e($schoolName) ?></h1>
        <p class="brand-tagline"><?= e($schoolTagline) ?></p>
        
        <ul class="features-list">
          <li>
            <div class="feature-icon blue">
              <i class="bi bi-people-fill"></i>
            </div>
            <div class="feature-text">
              <h4>Student Management</h4>
              <p>Complete admission & records system</p>
            </div>
          </li>
          <li>
            <div class="feature-icon cyan">
              <i class="bi bi-journal-check"></i>
            </div>
            <div class="feature-text">
              <h4>Exam Scheduling</h4>
              <p>Organize exams by class & subject</p>
            </div>
          </li>
          <li>
            <div class="feature-icon purple">
              <i class="bi bi-calendar-event"></i>
            </div>
            <div class="feature-text">
              <h4>Event Management</h4>
              <p>School announcements & events</p>
            </div>
          </li>
          <li>
            <div class="feature-icon green">
              <i class="bi bi-shield-check"></i>
            </div>
            <div class="feature-text">
              <h4>Secure Access</h4>
              <p>Role-based permission system</p>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="login-panel">
      <div class="login-header">
        <h2>Welcome Back!</h2>
        <p>Sign in to access your dashboard</p>
      </div>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert-error">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <span><?= e($errors['general']) ?></span>
        </div>
      <?php endif; ?>

      <form method="post" id="loginForm" autocomplete="off">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label">Username</label>
          <div class="input-wrapper">
            <input 
              type="text" 
              name="username" 
              class="form-input <?= !empty($errors['username']) ? 'is-invalid' : '' ?>" 
              placeholder="Enter your username"
              value="<?= e((string)post('username','')) ?>"
              autocomplete="username"
              maxlength="64"
              required
            >
            <i class="bi bi-person"></i>
          </div>
          <?php if (!empty($errors['username'])): ?>
            <span class="invalid-feedback"><i class="bi bi-x-circle"></i> <?= e($errors['username']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrapper">
            <input 
              type="password" 
              name="password" 
              class="form-input <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" 
              placeholder="Enter your password"
              autocomplete="current-password"
              maxlength="128"
              required
            >
            <i class="bi bi-lock"></i>
          </div>
          <?php if (!empty($errors['password'])): ?>
            <span class="invalid-feedback"><i class="bi bi-x-circle"></i> <?= e($errors['password']) ?></span>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn-login" id="loginBtn">
          <span class="loading-spinner"></span>
          <span class="btn-text">Sign In</span>
          <i class="bi bi-arrow-right" style="position:relative;z-index:1;"></i>
        </button>
      </form>

      <div class="divider">
        <span>Demo Access</span>
      </div>

      <div class="demo-credentials">
        <h5><i class="bi bi-info-circle"></i> Default Credentials</h5>
        <p>Username: <code>admin</code> &nbsp;•&nbsp; Password: <code>admin123</code></p>
      </div>

      <div class="login-footer">
        <p>First time? Run <a href="<?= e(base_url('install')) ?>">install</a> once.</p>
        <p style="margin-top: 15px;">
          <a href="<?= e(base_url('student_login')) ?>" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,rgba(168,85,247,0.15),rgba(236,72,153,0.15));padding:12px 24px;border-radius:30px;color:#c084fc;font-weight:600;transition:all 0.3s;">
            <i class="bi bi-mortarboard"></i> Student Login Portal
          </a>
        </p>
        <p style="margin-top: 15px;">© <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</p>
      </div>
    </div>
  </div>

  <script>
    // Add loading state on form submit
    document.getElementById('loginForm').addEventListener('submit', function() {
      const btn = document.getElementById('loginBtn');
      btn.classList.add('loading');
      btn.disabled = true;
    });

    // Input focus effects
    document.querySelectorAll('.form-input').forEach(input => {
      input.addEventListener('focus', function() {
        this.closest('.input-wrapper').classList.add('focused');
      });
      input.addEventListener('blur', function() {
        this.closest('.input-wrapper').classList.remove('focused');
      });
    });
  </script>
</body>
</html>
