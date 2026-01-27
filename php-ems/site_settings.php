<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('*');

// Initialize settings CSV
$settingsFile = DATA_PATH . '/site_settings.csv';
csv_init($settingsFile, ['id', 'key', 'value', 'type', 'label', 'group']);

// Default settings
$defaultSettings = [
    // School Info
    ['key' => 'school_name', 'value' => 'Delhi Public School', 'type' => 'text', 'label' => 'School Name', 'group' => 'basic'],
    ['key' => 'school_tagline', 'value' => 'Excellence in Education', 'type' => 'text', 'label' => 'Tagline', 'group' => 'basic'],
    ['key' => 'school_short_name', 'value' => 'DPS', 'type' => 'text', 'label' => 'Short Name', 'group' => 'basic'],
    ['key' => 'school_established', 'value' => '1998', 'type' => 'text', 'label' => 'Established Year', 'group' => 'basic'],
    ['key' => 'school_affiliation', 'value' => 'CBSE Affiliated', 'type' => 'text', 'label' => 'Affiliation', 'group' => 'basic'],
    ['key' => 'school_logo', 'value' => '', 'type' => 'image', 'label' => 'School Logo', 'group' => 'basic'],
    
    // Hero Section
    ['key' => 'hero_title', 'value' => 'Shaping Tomorrow\'s Leaders Today', 'type' => 'text', 'label' => 'Hero Title', 'group' => 'hero'],
    ['key' => 'hero_subtitle', 'value' => 'Where Excellence Meets Innovation - Empowering students with knowledge, creativity, and values for a brighter future.', 'type' => 'textarea', 'label' => 'Hero Subtitle', 'group' => 'hero'],
    ['key' => 'hero_image', 'value' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920', 'type' => 'image', 'label' => 'Hero Background Image', 'group' => 'hero'],
    ['key' => 'admission_open', 'value' => '1', 'type' => 'checkbox', 'label' => 'Show Admission Open Badge', 'group' => 'hero'],
    ['key' => 'admission_year', 'value' => '2025-26', 'type' => 'text', 'label' => 'Admission Year', 'group' => 'hero'],
    
    // Statistics
    ['key' => 'stat_years', 'value' => '25+', 'type' => 'text', 'label' => 'Years of Excellence', 'group' => 'stats'],
    ['key' => 'stat_students', 'value' => '5000+', 'type' => 'text', 'label' => 'Students Enrolled', 'group' => 'stats'],
    ['key' => 'stat_teachers', 'value' => '200+', 'type' => 'text', 'label' => 'Expert Teachers', 'group' => 'stats'],
    ['key' => 'stat_results', 'value' => '98%', 'type' => 'text', 'label' => 'Board Results', 'group' => 'stats'],
    
    // Contact Info
    ['key' => 'phone_1', 'value' => '+91 123 456 7890', 'type' => 'text', 'label' => 'Phone 1', 'group' => 'contact'],
    ['key' => 'phone_2', 'value' => '+91 123 456 7891', 'type' => 'text', 'label' => 'Phone 2', 'group' => 'contact'],
    ['key' => 'email', 'value' => 'info@dps.edu.in', 'type' => 'text', 'label' => 'Email', 'group' => 'contact'],
    ['key' => 'admission_email', 'value' => 'admission@dps.edu.in', 'type' => 'text', 'label' => 'Admission Email', 'group' => 'contact'],
    ['key' => 'address', 'value' => '123 Education Lane, Sector 15, New Delhi - 110001', 'type' => 'textarea', 'label' => 'Address', 'group' => 'contact'],
    ['key' => 'office_hours', 'value' => 'Mon - Sat: 8:00 AM - 4:00 PM', 'type' => 'text', 'label' => 'Office Hours', 'group' => 'contact'],
    
    // Social Links
    ['key' => 'social_facebook', 'value' => 'https://facebook.com/', 'type' => 'text', 'label' => 'Facebook URL', 'group' => 'social'],
    ['key' => 'social_instagram', 'value' => 'https://instagram.com/', 'type' => 'text', 'label' => 'Instagram URL', 'group' => 'social'],
    ['key' => 'social_twitter', 'value' => 'https://twitter.com/', 'type' => 'text', 'label' => 'Twitter/X URL', 'group' => 'social'],
    ['key' => 'social_youtube', 'value' => 'https://youtube.com/', 'type' => 'text', 'label' => 'YouTube URL', 'group' => 'social'],
    ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/', 'type' => 'text', 'label' => 'LinkedIn URL', 'group' => 'social'],
    
    // About Section
    ['key' => 'about_title', 'value' => 'Building Character, Shaping Futures', 'type' => 'text', 'label' => 'About Title', 'group' => 'about'],
    ['key' => 'about_text', 'value' => 'Our institution has been a beacon of educational excellence, committed to providing quality education that nurtures intellectual curiosity, creativity, and moral values in our students. We believe in creating a stimulating learning environment where every child can discover their potential.', 'type' => 'textarea', 'label' => 'About Text', 'group' => 'about'],
    ['key' => 'about_image', 'value' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800', 'type' => 'image', 'label' => 'About Image', 'group' => 'about'],
    
    // Principal Message
    ['key' => 'principal_name', 'value' => 'Dr. Rajesh Kumar', 'type' => 'text', 'label' => 'Principal Name', 'group' => 'principal'],
    ['key' => 'principal_title', 'value' => 'Principal & Director', 'type' => 'text', 'label' => 'Principal Title', 'group' => 'principal'],
    ['key' => 'principal_message', 'value' => 'Education is not just about acquiring knowledge; it is about developing the whole person. At our school, we strive to create an environment where students can grow intellectually, emotionally, and socially.', 'type' => 'textarea', 'label' => 'Principal Message', 'group' => 'principal'],
    ['key' => 'principal_image', 'value' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400', 'type' => 'image', 'label' => 'Principal Photo', 'group' => 'principal'],
    
    // Template Selection
    ['key' => 'site_template', 'value' => 'modern-dark', 'type' => 'template_select', 'label' => 'Website Template', 'group' => 'theme'],
    
    // Notifications
    ['key' => 'admin_notification_email', 'value' => 'admin@school.edu.in', 'type' => 'text', 'label' => 'Admin Email for Notifications', 'group' => 'notifications'],
    
    // Security Settings
    ['key' => 'allow_teacher_password_change', 'value' => '1', 'type' => 'checkbox', 'label' => 'Allow Teachers to Change Password', 'group' => 'security'],
    ['key' => 'allow_student_password_change', 'value' => '1', 'type' => 'checkbox', 'label' => 'Allow Students to Change Password', 'group' => 'security'],
    
    // System Settings
    ['key' => 'site_domain', 'value' => '', 'type' => 'text', 'label' => 'Website Domain (e.g., https://school.com/ems)', 'group' => 'system'],
];

// Initialize default settings if empty
$existingSettings = csv_read_all($settingsFile);
if (empty($existingSettings)) {
    foreach ($defaultSettings as $setting) {
        csv_insert($settingsFile, $setting);
    }
    $existingSettings = csv_read_all($settingsFile);
}

// Add missing settings (for upgrades)
$existingKeys = array_column($existingSettings, 'key');
foreach ($defaultSettings as $setting) {
    if (!in_array($setting['key'], $existingKeys, true)) {
        csv_insert($settingsFile, $setting);
    }
}

// Remove deprecated color settings (cleanup)
$deprecatedKeys = ['primary_color', 'secondary_color', 'accent_color'];
foreach ($existingSettings as $s) {
    if (in_array($s['key'], $deprecatedKeys, true)) {
        csv_delete_by_id($settingsFile, $s['id']);
    }
}

$existingSettings = csv_read_all($settingsFile);

// Handle form submission
if (request_method() === 'POST') {
    csrf_verify_or_die();
    
    // Track which keys were uploaded (to skip text field overwrite)
    $uploadedKeys = [];
    
    // Handle image uploads first
    foreach ($_FILES as $key => $file) {
        if (strpos($key, 'upload_') === 0 && !empty($file['tmp_name'])) {
            $settingKey = substr($key, 7);
            $upload = upload_image($file, $settingKey);
            if ($upload['success']) {
                foreach ($existingSettings as $s) {
                    if ($s['key'] === $settingKey) {
                        csv_update_by_id($settingsFile, $s['id'], ['value' => $upload['url']]);
                        $uploadedKeys[] = $settingKey; // Mark as uploaded
                        break;
                    }
                }
            }
        }
    }
    
    // Handle text/other fields (skip image fields that were just uploaded)
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $settingKey = substr($key, 8);
            
            // Skip if this image field was just uploaded (don't overwrite with old URL)
            if (in_array($settingKey, $uploadedKeys)) {
                continue;
            }
            
            foreach ($existingSettings as $s) {
                if ($s['key'] === $settingKey) {
                    csv_update_by_id($settingsFile, $s['id'], ['value' => $value]);
                    break;
                }
            }
        }
    }
    
    // Handle checkboxes
    foreach ($existingSettings as $s) {
        if ($s['type'] === 'checkbox') {
            $postKey = 'setting_' . $s['key'];
            if (!isset($_POST[$postKey])) {
                csv_update_by_id($settingsFile, $s['id'], ['value' => '0']);
            }
        }
    }
    
    flash_set('success', 'Settings saved successfully!');
    redirect('site_settings');
}

// Reload settings
$settings = csv_read_all($settingsFile);
$settingsByKey = [];
foreach ($settings as $s) {
    $settingsByKey[$s['key']] = $s;
}

// Group settings
$groups = [
    'basic' => ['title' => 'School Information', 'icon' => 'bi-building'],
    'hero' => ['title' => 'Hero Section', 'icon' => 'bi-image'],
    'stats' => ['title' => 'Statistics', 'icon' => 'bi-graph-up'],
    'about' => ['title' => 'About Section', 'icon' => 'bi-info-circle'],
    'principal' => ['title' => 'Principal Message', 'icon' => 'bi-person-badge'],
    'contact' => ['title' => 'Contact Information', 'icon' => 'bi-telephone'],
    'social' => ['title' => 'Social Media Links', 'icon' => 'bi-share'],
    'theme' => ['title' => 'Website Template', 'icon' => 'bi-palette'],
    'notifications' => ['title' => 'Notifications', 'icon' => 'bi-bell'],
    'security' => ['title' => 'Security Settings', 'icon' => 'bi-shield-lock'],
    'system' => ['title' => 'System Settings', 'icon' => 'bi-gear'],
];

$settingsByGroup = [];
foreach ($settings as $s) {
    $group = $s['group'] ?? 'basic';
    if (!isset($settingsByGroup[$group])) {
        $settingsByGroup[$group] = [];
    }
    $settingsByGroup[$group][] = $s;
}

$title = 'Site Settings';
$active = 'site_settings';
$content = function () use ($groups, $settingsByGroup) {
?>
<style>
  .settings-card { border-radius: 16px; overflow: hidden; }
  .settings-header { 
    background: rgba(92,124,250,.1); 
    padding: 15px 20px; 
    border-bottom: 1px solid rgba(255,255,255,.08);
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .settings-header i { font-size: 1.2rem; color: var(--brand); }
  .settings-header h5 { margin: 0; font-size: 1rem; font-weight: 600; }
  .settings-body { padding: 20px; }
  .color-preview {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 2px solid rgba(255,255,255,.2);
    cursor: pointer;
  }
  .nav-pills .nav-link {
    color: var(--text);
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .nav-pills .nav-link:hover { background: rgba(255,255,255,.05); }
  .nav-pills .nav-link.active { background: rgba(92,124,250,.2); color: var(--brand); }
  .image-upload-wrapper {
    border: 2px dashed rgba(255,255,255,.15);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s;
    position: relative;
  }
  .image-upload-wrapper:hover {
    border-color: rgba(92,124,250,.4);
    background: rgba(92,124,250,.05);
  }
  .image-upload-wrapper input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
  }
  .image-preview {
    max-width: 150px;
    max-height: 100px;
    border-radius: 8px;
    margin-bottom: 10px;
    object-fit: cover;
  }
  .current-url {
    font-size: 0.75rem;
    color: var(--text-muted);
    word-break: break-all;
    margin-top: 8px;
  }
</style>

<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  
  <div class="row g-4">
    <div class="col-lg-3">
      <div class="card settings-card sticky-top" style="top: 80px;">
        <div class="card-body p-3">
          <nav class="nav nav-pills flex-column">
            <?php foreach ($groups as $groupKey => $group): ?>
              <a class="nav-link <?= $groupKey === 'basic' ? 'active' : '' ?>" href="#<?= $groupKey ?>">
                <i class="bi <?= $group['icon'] ?>"></i>
                <?= e($group['title']) ?>
              </a>
            <?php endforeach; ?>
          </nav>
          <hr class="my-3 border-secondary">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check-lg me-2"></i>Save All Settings
          </button>
          <a href="<?= e(base_url('/')) ?>" target="_blank" class="btn btn-outline-light w-100 mt-2">
            <i class="bi bi-eye me-2"></i>Preview Website
          </a>
        </div>
      </div>
    </div>
    
    <div class="col-lg-9">
      <?php foreach ($groups as $groupKey => $group): ?>
        <div class="card settings-card mb-4" id="<?= $groupKey ?>">
          <div class="settings-header">
            <i class="bi <?= $group['icon'] ?>"></i>
            <h5><?= e($group['title']) ?></h5>
          </div>
          <div class="settings-body">
            <div class="row g-3">
              <?php if (isset($settingsByGroup[$groupKey])): ?>
                <?php foreach ($settingsByGroup[$groupKey] as $setting): ?>
                  <div class="<?= in_array($setting['type'], ['textarea', 'image']) ? 'col-12' : 'col-md-6' ?>">
                    <label class="form-label"><?= e($setting['label']) ?></label>
                    
                    <?php if ($setting['type'] === 'textarea'): ?>
                      <textarea class="form-control" name="setting_<?= e($setting['key']) ?>" rows="3"><?= e($setting['value']) ?></textarea>
                    
                    <?php elseif ($setting['type'] === 'checkbox'): ?>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="setting_<?= e($setting['key']) ?>" value="1" <?= $setting['value'] === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label">Enabled</label>
                      </div>
                    
                    <?php elseif ($setting['type'] === 'template_select'): ?>
                      <?php
                      $templates = [
                          'modern-dark' => [
                              'name' => 'Admission Focus', 
                              'desc' => 'Clean light design with admission CTA', 
                              'preview' => 'linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%)',
                              'icon' => '🎓',
                              'accent' => '#1a4d8f'
                          ],
                          'classic-elegant' => [
                              'name' => 'Festival Celebration', 
                              'desc' => 'Warm festive design with cultural theme', 
                              'preview' => 'linear-gradient(135deg, #7c1034 0%, #b91c47 50%, #e8447a 100%)',
                              'icon' => '🎉',
                              'accent' => '#ffd700'
                          ],
                          'vibrant-colorful' => [
                              'name' => 'Summer Vacation', 
                              'desc' => 'Fun playful design for primary schools', 
                              'preview' => 'linear-gradient(135deg, #00b4d8 0%, #0096c7 50%, #48cae4 100%)',
                              'icon' => '☀️',
                              'accent' => '#ffeb3b'
                          ],
                          'minimal-clean' => [
                              'name' => 'Achievement Showcase', 
                              'desc' => 'Minimal elegant for prestigious schools', 
                              'preview' => 'linear-gradient(135deg, #f9fafb 0%, #f3f4f6 50%, #e5e7eb 100%)',
                              'icon' => '🏆',
                              'accent' => '#111827'
                          ],
                          'bold-geometric' => [
                              'name' => 'New Session Welcome', 
                              'desc' => 'Modern tech-forward geometric', 
                              'preview' => 'linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%)',
                              'icon' => '🚀',
                              'accent' => '#8b5cf6'
                          ],
                      ];
                      ?>
                      <div class="row g-3">
                        <?php foreach ($templates as $key => $tpl): ?>
                        <div class="col-6 col-md-4 col-xl">
                          <label class="template-card <?= $setting['value'] === $key ? 'active' : '' ?>" style="cursor:pointer;display:block;border:2px solid <?= $setting['value'] === $key ? 'var(--brand)' : 'rgba(255,255,255,.1)' ?>;border-radius:16px;padding:12px;text-align:center;transition:all 0.3s;background:rgba(255,255,255,.02);">
                            <input type="radio" name="setting_<?= e($setting['key']) ?>" value="<?= e($key) ?>" <?= $setting['value'] === $key ? 'checked' : '' ?> style="display:none;">
                            <div style="width:100%;height:70px;background:<?= $tpl['preview'] ?>;border-radius:10px;margin-bottom:12px;border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                              <span style="font-size:28px;filter:drop-shadow(0 2px 4px rgba(0,0,0,.2));"><?= $tpl['icon'] ?></span>
                              <div style="position:absolute;bottom:0;left:0;right:0;height:4px;background:<?= $tpl['accent'] ?>;"></div>
                            </div>
                            <div style="font-weight:600;font-size:0.85rem;margin-bottom:4px;"><?= e($tpl['name']) ?></div>
                            <div style="font-size:0.7rem;color:var(--text-muted);line-height:1.3;"><?= e($tpl['desc']) ?></div>
                            <?php if ($setting['value'] === $key): ?>
                            <span class="badge bg-success mt-2" style="font-size:0.65rem;">Active</span>
                            <?php endif; ?>
                          </label>
                        </div>
                        <?php endforeach; ?>
                      </div>
                      <script>
                      document.querySelectorAll('.template-card input[type="radio"]').forEach(function(radio) {
                        radio.addEventListener('change', function() {
                          document.querySelectorAll('.template-card').forEach(function(card) {
                            card.style.borderColor = 'rgba(255,255,255,.1)';
                            card.classList.remove('active');
                            var badge = card.querySelector('.badge');
                            if (badge) badge.remove();
                          });
                          if (this.checked) {
                            this.closest('.template-card').style.borderColor = 'var(--brand)';
                            this.closest('.template-card').classList.add('active');
                          }
                        });
                      });
                      </script>
                    
                    <?php elseif ($setting['type'] === 'color'): ?>
                      <?php // Color type deprecated - skip rendering ?>
                    
                    <?php elseif ($setting['type'] === 'image'): ?>
                      <div class="row align-items-start g-3">
                        <div class="col-md-6">
                          <div class="image-upload-wrapper">
                            <?php if (!empty($setting['value'])): ?>
                              <img src="<?= e($setting['value']) ?>" alt="Preview" class="image-preview">
                            <?php else: ?>
                              <i class="bi bi-cloud-upload fs-2 text-muted mb-2 d-block"></i>
                            <?php endif; ?>
                            <div class="text-muted small">Click or drag to upload</div>
                            <div class="text-muted" style="font-size:0.7rem;">JPG, PNG, GIF, WEBP (max 5MB)</div>
                            <input type="file" name="upload_<?= e($setting['key']) ?>" accept="image/jpeg,image/png,image/gif,image/webp">
                          </div>
                          <?php if (!empty($setting['value'])): ?>
                            <div class="current-url">Current: <?= e($setting['value']) ?></div>
                          <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label small text-muted">Or enter URL:</label>
                          <input type="text" class="form-control" name="setting_<?= e($setting['key']) ?>" value="<?= e($setting['value']) ?>" placeholder="https://...">
                        </div>
                      </div>
                    
                    <?php else: ?>
                      <input type="text" class="form-control" name="setting_<?= e($setting['key']) ?>" value="<?= e($setting['value']) ?>">
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      
      <div class="text-center pb-4">
        <button type="submit" class="btn btn-primary btn-lg px-5">
          <i class="bi bi-check-lg me-2"></i>Save All Settings
        </button>
      </div>
    </div>
  </div>
</form>

<script>
// Smooth scroll to sections
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    }
  });
});

// Update color text on change
document.querySelectorAll('input[type="color"]').forEach(input => {
  input.addEventListener('input', function() {
    this.nextElementSibling.value = this.value;
  });
});

// Preview image on file select
document.querySelectorAll('input[type="file"]').forEach(input => {
  input.addEventListener('change', function() {
    const wrapper = this.closest('.image-upload-wrapper');
    const file = this.files[0];
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(e) {
        let img = wrapper.querySelector('.image-preview');
        if (!img) {
          img = document.createElement('img');
          img.className = 'image-preview';
          wrapper.insertBefore(img, wrapper.firstChild);
        }
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
});
</script>
<?php
};

include __DIR__ . '/views/partials/layout.php';
