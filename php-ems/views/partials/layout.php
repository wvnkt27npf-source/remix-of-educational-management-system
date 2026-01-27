<?php
/**
 * Layout wrapper.
 * Variables expected:
 * - $title
 * - $active (string)
 * - $content (callable)
 */
require_login();
$u = current_user();
?>
<?php include __DIR__ . '/head.php'; ?>

<div class="app-shell">
  <aside class="app-sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-mark"><i class="bi bi-mortarboard"></i></div>
      <div class="brand-text">
        <div class="brand-title">EMS</div>
        <div class="brand-sub"><?= e(APP_NAME) ?></div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <a class="nav-item <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= e(base_url('dashboard')) ?>">
        <i class="bi bi-grid"></i><span>Dashboard</span>
      </a>

      <?php if (has_permission('students.read')): ?>
        <a class="nav-item <?= ($active ?? '') === 'students' ? 'active' : '' ?>" href="<?= e(base_url('students')) ?>">
          <i class="bi bi-people"></i><span>Students</span>
        </a>
      <?php endif; ?>

      <?php if (has_permission('teachers.read')): ?>
        <a class="nav-item <?= ($active ?? '') === 'teachers' ? 'active' : '' ?>" href="<?= e(base_url('teachers')) ?>">
          <i class="bi bi-person-workspace"></i><span>Teachers</span>
        </a>
      <?php endif; ?>

      <?php if (has_permission('*')): ?>
        <a class="nav-item <?= ($active ?? '') === 'classes' ? 'active' : '' ?>" href="<?= e(base_url('classes')) ?>">
          <i class="bi bi-building"></i><span>Classes</span>
        </a>
      <?php endif; ?>

      <?php if (has_permission('exams.read')): ?>
        <?php if (has_permission('exams.write')): ?>
          <a class="nav-item <?= ($active ?? '') === 'exams' ? 'active' : '' ?>" href="<?= e(base_url('exams')) ?>">
            <i class="bi bi-journal-text"></i><span>Exams</span>
          </a>
          <a class="nav-item <?= ($active ?? '') === 'exam-results' ? 'active' : '' ?>" href="<?= e(base_url('exam_results')) ?>">
            <i class="bi bi-award"></i><span>Exam Results</span>
          </a>
        <?php else: ?>
          <a class="nav-item <?= ($active ?? '') === 'my-exams' ? 'active' : '' ?>" href="<?= e(base_url('my_exams')) ?>">
            <i class="bi bi-journal-text"></i><span>My Exams</span>
          </a>
          <a class="nav-item <?= ($active ?? '') === 'my-results' ? 'active' : '' ?>" href="<?= e(base_url('my_results')) ?>">
            <i class="bi bi-trophy"></i><span>My Results</span>
          </a>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (has_permission('events.read')): ?>
        <a class="nav-item <?= ($active ?? '') === 'events' ? 'active' : '' ?>" href="<?= e(base_url('events')) ?>">
          <i class="bi bi-megaphone"></i><span>Events</span>
        </a>
      <?php endif; ?>

      <?php if (has_permission('*')): ?>
        <div class="nav-divider">Website</div>
        <a class="nav-item <?= ($active ?? '') === 'site_settings' ? 'active' : '' ?>" href="<?= e(base_url('site_settings')) ?>">
          <i class="bi bi-gear"></i><span>Site Settings</span>
        </a>
        <a class="nav-item <?= ($active ?? '') === 'hero_banners' ? 'active' : '' ?>" href="<?= e(base_url('hero_banners')) ?>">
          <i class="bi bi-images"></i><span>Hero Banners</span>
        </a>
        <a class="nav-item <?= ($active ?? '') === 'news' ? 'active' : '' ?>" href="<?= e(base_url('news')) ?>">
          <i class="bi bi-newspaper"></i><span>News</span>
        </a>
        <a class="nav-item <?= ($active ?? '') === 'testimonials' ? 'active' : '' ?>" href="<?= e(base_url('testimonials')) ?>">
          <i class="bi bi-chat-quote"></i><span>Testimonials</span>
        </a>
        <a class="nav-item <?= ($active ?? '') === 'admissions' ? 'active' : '' ?>" href="<?= e(base_url('admissions')) ?>">
          <i class="bi bi-person-plus"></i><span>Admissions</span>
        </a>
        <div class="nav-divider">System</div>
        <a class="nav-item <?= ($active ?? '') === 'notification_settings' ? 'active' : '' ?>" href="<?= e(base_url('notification_settings')) ?>">
          <i class="bi bi-bell"></i><span>Notifications</span>
        </a>
        <a class="nav-item <?= ($active ?? '') === 'users' ? 'active' : '' ?>" href="<?= e(base_url('users')) ?>">
          <i class="bi bi-shield-lock"></i><span>Users</span>
        </a>
      <?php endif; ?>

      <div class="nav-divider">Account</div>
      <a class="nav-item <?= ($active ?? '') === 'profile' ? 'active' : '' ?>" href="<?= e(base_url('profile')) ?>">
        <i class="bi bi-person"></i><span>Profile</span>
      </a>
      <a class="nav-item <?= ($active ?? '') === 'password' ? 'active' : '' ?>" href="<?= e(base_url('change_password')) ?>">
        <i class="bi bi-key"></i><span>Change Password</span>
      </a>
      <a class="nav-item" href="<?= e(base_url('logout')) ?>">
        <i class="bi bi-box-arrow-right"></i><span>Logout</span>
      </a>
    </nav>
  </aside>

  <main class="app-main">
    <header class="app-topbar">
      <button class="btn btn-sm btn-outline-light sidebar-toggle" type="button" data-sidebar-toggle>
        <i class="bi bi-list"></i>
      </button>
      <div class="topbar-title"><?= e($title ?? APP_NAME) ?></div>
      <div class="topbar-spacer"></div>

      <div class="dropdown">
        <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown" type="button">
          <i class="bi bi-person-circle me-1"></i>
          <?= e(($u['username'] ?? 'User') . ' (' . ($u['role'] ?? '') . ')') ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="<?= e(base_url('profile')) ?>">Profile</a></li>
          <li><a class="dropdown-item" href="<?= e(base_url('change_password')) ?>">Change Password</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="<?= e(base_url('logout')) ?>">Logout</a></li>
        </ul>
      </div>
    </header>

    <div class="app-content container-fluid">
      <?php $flash = flash_get(); ?>
      <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
          <?= e($flash['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php $content(); ?>
    </div>
  </main>
</div>

<?php include __DIR__ . '/scripts.php'; ?>
