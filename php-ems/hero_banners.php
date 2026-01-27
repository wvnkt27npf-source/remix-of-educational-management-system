<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/bootstrap.php';
require_login();
require_permission('*');

$title = 'Hero Banners';
$active = 'hero_banners';

// Ensure uploads/banners directory exists
$bannerUploadDir = __DIR__ . '/uploads/banners/';
if (!is_dir($bannerUploadDir)) {
    @mkdir($bannerUploadDir, 0755, true);
}

// Theme definitions
$themes = [
    'admission' => [
        'name' => 'Admission Focus',
        'icon' => 'bi-mortarboard',
        'color' => '#1a4d8f',
        'description' => 'Professional, trustworthy design for admission season',
    ],
    'festival' => [
        'name' => 'Festival Celebration',
        'icon' => 'bi-stars',
        'color' => '#ff6b35',
        'description' => 'Vibrant, festive design with confetti animations',
    ],
    'vacation' => [
        'name' => 'Summer Vacation',
        'icon' => 'bi-sun',
        'color' => '#06d6a0',
        'description' => 'Playful, energetic design for camps & activities',
    ],
    'achievement' => [
        'name' => 'Achievement Showcase',
        'icon' => 'bi-trophy',
        'color' => '#7b2cbf',
        'description' => 'Elegant, prestigious design for results & awards',
    ],
    'welcome' => [
        'name' => 'New Session Welcome',
        'icon' => 'bi-book',
        'color' => '#2ec4b6',
        'description' => 'Clean, welcoming design for new beginnings',
    ],
];

// Handle form submissions
if (request_method() === 'POST') {
    $action = post('action', '');
    
    if ($action === 'add' || $action === 'edit') {
        $data = [
            'title' => trim((string)post('title', '')),
            'subtitle' => trim((string)post('subtitle', '')),
            'theme' => trim((string)post('theme', 'admission')),
            'badge_text' => trim((string)post('badge_text', '')),
            'cta_primary_text' => trim((string)post('cta_primary_text', '')),
            'cta_primary_link' => trim((string)post('cta_primary_link', '')),
            'cta_secondary_text' => trim((string)post('cta_secondary_text', '')),
            'cta_secondary_link' => trim((string)post('cta_secondary_link', '')),
            'is_active' => post('is_active') ? '1' : '0',
            'display_order' => (int)post('display_order', 0),
            'start_date' => trim((string)post('start_date', '')),
            'end_date' => trim((string)post('end_date', '')),
        ];
        
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/uploads/banners/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $data['image'] = 'uploads/banners/' . $filename;
            }
        } elseif ($action === 'edit') {
            // Keep existing image
            $existing = csv_find_by_id(DATA_PATH . '/hero_banners.csv', post('id'));
            $data['image'] = $existing['image'] ?? '';
        } else {
            $data['image'] = '';
        }
        
        if ($action === 'add') {
            $data['created_at'] = date('Y-m-d H:i:s');
            csv_insert(DATA_PATH . '/hero_banners.csv', $data);
            flash_set('success', 'Banner added successfully!');
        } else {
            csv_update_by_id(DATA_PATH . '/hero_banners.csv', post('id'), $data);
            flash_set('success', 'Banner updated successfully!');
        }
        redirect('hero_banners');
    }
    
    if ($action === 'delete') {
        csv_delete_by_id(DATA_PATH . '/hero_banners.csv', post('id'));
        flash_set('success', 'Banner deleted successfully!');
        redirect('hero_banners');
    }
    
    if ($action === 'toggle') {
        $banner = csv_find_by_id(DATA_PATH . '/hero_banners.csv', post('id'));
        if ($banner) {
            csv_update_by_id(DATA_PATH . '/hero_banners.csv', post('id'), [
                'is_active' => $banner['is_active'] === '1' ? '0' : '1',
            ]);
        }
        redirect('hero_banners');
    }
    
    if ($action === 'reorder') {
        $orders = json_decode(post('orders', '[]'), true);
        if (is_array($orders)) {
            foreach ($orders as $id => $order) {
                csv_update_by_id(DATA_PATH . '/hero_banners.csv', $id, ['display_order' => (int)$order]);
            }
        }
        echo json_encode(['success' => true]);
        exit;
    }
}

// Load banners
$banners = csv_read_all(DATA_PATH . '/hero_banners.csv');
usort($banners, function($a, $b) {
    return ((int)($a['display_order'] ?? 0)) - ((int)($b['display_order'] ?? 0));
});

$content = function() use ($banners, $themes) {
?>
<style>
.banner-card {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    transition: all 0.3s;
    cursor: grab;
}
.banner-card:hover {
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.15);
}
.banner-card.dragging {
    opacity: 0.5;
    cursor: grabbing;
}
.banner-preview {
    width: 120px;
    height: 70px;
    border-radius: 10px;
    object-fit: cover;
    background: linear-gradient(135deg, var(--theme-color, #1a4d8f), rgba(0,0,0,0.3));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
}
.banner-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}
.theme-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    background: rgba(255,255,255,.08);
}
.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-active { background: rgba(40, 167, 69, 0.2); color: #28a745; }
.status-inactive { background: rgba(108, 117, 125, 0.2); color: #6c757d; }
.status-scheduled { background: rgba(255, 193, 7, 0.2); color: #ffc107; }

.theme-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.theme-option {
    border: 2px solid rgba(255,255,255,.1);
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}
.theme-option:hover {
    border-color: rgba(255,255,255,.3);
    background: rgba(255,255,255,.03);
}
.theme-option.selected {
    border-color: var(--theme-color);
    background: rgba(var(--theme-color-rgb), 0.1);
}
.theme-option input { display: none; }
.theme-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin: 0 auto 10px;
}
.drag-handle {
    cursor: grab;
    color: rgba(255,255,255,.3);
    font-size: 1.3rem;
}
.banner-card:active .drag-handle {
    cursor: grabbing;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Hero Banners</h4>
        <p class="text-muted mb-0">Manage your landing page slider banners</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-2"></i>Add Banner
    </button>
</div>

<?php if (empty($banners)): ?>
<div class="text-center py-5">
    <i class="bi bi-images display-1 text-muted mb-3"></i>
    <h5>No Banners Yet</h5>
    <p class="text-muted">Create your first hero banner to showcase on your landing page.</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-2"></i>Add Your First Banner
    </button>
</div>
<?php else: ?>

<div class="alert alert-info d-flex align-items-center mb-4">
    <i class="bi bi-info-circle me-2"></i>
    <span>Drag banners to reorder. Only active banners with valid dates will be shown on the landing page.</span>
</div>

<div id="bannersList">
<?php foreach ($banners as $banner): 
    $theme = $themes[$banner['theme'] ?? 'admission'] ?? $themes['admission'];
    $isActive = ($banner['is_active'] ?? '0') === '1';
    $today = date('Y-m-d');
    $isScheduled = (!empty($banner['start_date']) && $banner['start_date'] > $today) || 
                   (!empty($banner['end_date']) && $banner['end_date'] < $today);
    $statusClass = $isActive ? ($isScheduled ? 'scheduled' : 'active') : 'inactive';
    $statusText = $isActive ? ($isScheduled ? 'Scheduled' : 'Active') : 'Inactive';
?>
<div class="banner-card d-flex align-items-center gap-4" data-id="<?= e($banner['id']) ?>">
    <div class="drag-handle">
        <i class="bi bi-grip-vertical"></i>
    </div>
    
    <div class="banner-preview" style="--theme-color: <?= e($theme['color']) ?>">
        <?php if (!empty($banner['image'])): ?>
            <img src="<?= e($banner['image']) ?>" alt="Banner">
        <?php else: ?>
            <i class="<?= e($theme['icon']) ?>"></i>
        <?php endif; ?>
    </div>
    
    <div class="flex-grow-1">
        <h6 class="mb-1"><?= e($banner['title'] ?: 'Untitled Banner') ?></h6>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="theme-badge" style="color: <?= e($theme['color']) ?>">
                <i class="<?= e($theme['icon']) ?>"></i>
                <?= e($theme['name']) ?>
            </span>
            <span class="status-badge status-<?= $statusClass ?>"><?= $statusText ?></span>
            <?php if (!empty($banner['start_date']) || !empty($banner['end_date'])): ?>
                <small class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    <?= !empty($banner['start_date']) ? date('M j', strtotime($banner['start_date'])) : 'Any' ?>
                    -
                    <?= !empty($banner['end_date']) ? date('M j', strtotime($banner['end_date'])) : 'Any' ?>
                </small>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <form method="post" class="d-inline">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= e($banner['id']) ?>">
            <button type="submit" class="btn btn-sm <?= $isActive ? 'btn-outline-success' : 'btn-outline-secondary' ?>" title="Toggle Active">
                <i class="bi bi-<?= $isActive ? 'eye' : 'eye-slash' ?>"></i>
            </button>
        </form>
        <button class="btn btn-sm btn-outline-primary" onclick="editBanner(<?= htmlspecialchars(json_encode($banner), ENT_QUOTES) ?>)">
            <i class="bi bi-pencil"></i>
        </button>
        <form method="post" class="d-inline" onsubmit="return confirm('Delete this banner?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= e($banner['id']) ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>

<!-- Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <form method="post" enctype="multipart/form-data" id="bannerForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="bannerId">
                
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="modalTitle">Add New Banner</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Theme Selector -->
                    <label class="form-label fw-semibold mb-3">Select Theme</label>
                    <div class="theme-selector">
                        <?php foreach ($themes as $key => $t): ?>
                        <label class="theme-option" data-theme="<?= e($key) ?>" style="--theme-color: <?= e($t['color']) ?>">
                            <input type="radio" name="theme" value="<?= e($key) ?>" <?= $key === 'admission' ? 'checked' : '' ?>>
                            <div class="theme-icon" style="background: <?= e($t['color']) ?>">
                                <i class="<?= e($t['icon']) ?>"></i>
                            </div>
                            <div class="fw-semibold"><?= e($t['name']) ?></div>
                            <small class="text-muted"><?= e($t['description']) ?></small>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <hr class="border-secondary my-4">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Badge Text</label>
                            <input type="text" name="badge_text" id="badgeText" class="form-control bg-dark text-white border-secondary" placeholder="e.g., Admissions Open 2025-26">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="bannerTitle" class="form-control bg-dark text-white border-secondary" required placeholder="Main headline">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Subtitle</label>
                            <textarea name="subtitle" id="bannerSubtitle" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Supporting text"></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Background Image</label>
                            <input type="file" name="image" class="form-control bg-dark text-white border-secondary" accept="image/*">
                            <small class="text-muted">Optional. Recommended: 1920x800px</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" id="displayOrder" class="form-control bg-dark text-white border-secondary" value="0" min="0">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Primary Button Text</label>
                            <input type="text" name="cta_primary_text" id="ctaPrimaryText" class="form-control bg-dark text-white border-secondary" placeholder="e.g., Apply Now">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Primary Button Link</label>
                            <input type="text" name="cta_primary_link" id="ctaPrimaryLink" class="form-control bg-dark text-white border-secondary" placeholder="e.g., #admission">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Secondary Button Text</label>
                            <input type="text" name="cta_secondary_text" id="ctaSecondaryText" class="form-control bg-dark text-white border-secondary" placeholder="e.g., Learn More">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Secondary Button Link</label>
                            <input type="text" name="cta_secondary_link" id="ctaSecondaryLink" class="form-control bg-dark text-white border-secondary" placeholder="e.g., #about">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Start Date (Optional)</label>
                            <input type="date" name="start_date" id="startDate" class="form-control bg-dark text-white border-secondary">
                            <small class="text-muted">Show from this date</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">End Date (Optional)</label>
                            <input type="date" name="end_date" id="endDate" class="form-control bg-dark text-white border-secondary">
                            <small class="text-muted">Hide after this date</small>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="isActive" class="form-check-input" checked>
                                <label class="form-check-label" for="isActive">Active (Show on landing page)</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i><span id="submitText">Add Banner</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Theme selector
document.querySelectorAll('.theme-option').forEach(opt => {
    opt.addEventListener('click', () => {
        document.querySelectorAll('.theme-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
        opt.querySelector('input').checked = true;
    });
});

function resetForm() {
    document.getElementById('formAction').value = 'add';
    document.getElementById('bannerId').value = '';
    document.getElementById('bannerForm').reset();
    document.getElementById('modalTitle').textContent = 'Add New Banner';
    document.getElementById('submitText').textContent = 'Add Banner';
    document.querySelectorAll('.theme-option').forEach(o => o.classList.remove('selected'));
    document.querySelector('.theme-option[data-theme="admission"]').classList.add('selected');
}

function editBanner(banner) {
    document.getElementById('formAction').value = 'edit';
    document.getElementById('bannerId').value = banner.id;
    document.getElementById('badgeText').value = banner.badge_text || '';
    document.getElementById('bannerTitle').value = banner.title || '';
    document.getElementById('bannerSubtitle').value = banner.subtitle || '';
    document.getElementById('displayOrder').value = banner.display_order || 0;
    document.getElementById('ctaPrimaryText').value = banner.cta_primary_text || '';
    document.getElementById('ctaPrimaryLink').value = banner.cta_primary_link || '';
    document.getElementById('ctaSecondaryText').value = banner.cta_secondary_text || '';
    document.getElementById('ctaSecondaryLink').value = banner.cta_secondary_link || '';
    document.getElementById('startDate').value = banner.start_date || '';
    document.getElementById('endDate').value = banner.end_date || '';
    document.getElementById('isActive').checked = banner.is_active === '1';
    document.getElementById('modalTitle').textContent = 'Edit Banner';
    document.getElementById('submitText').textContent = 'Save Changes';
    
    // Select theme
    document.querySelectorAll('.theme-option').forEach(o => o.classList.remove('selected'));
    const themeOpt = document.querySelector(`.theme-option[data-theme="${banner.theme || 'admission'}"]`);
    if (themeOpt) {
        themeOpt.classList.add('selected');
        themeOpt.querySelector('input').checked = true;
    }
    
    new bootstrap.Modal(document.getElementById('bannerModal')).show();
}

// Drag and drop reordering
const bannersList = document.getElementById('bannersList');
if (bannersList) {
    let draggedItem = null;
    
    bannersList.querySelectorAll('.banner-card').forEach(card => {
        card.setAttribute('draggable', true);
        
        card.addEventListener('dragstart', (e) => {
            draggedItem = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        
        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            saveOrder();
        });
        
        card.addEventListener('dragover', (e) => {
            e.preventDefault();
            const afterElement = getDragAfterElement(bannersList, e.clientY);
            if (afterElement == null) {
                bannersList.appendChild(draggedItem);
            } else {
                bannersList.insertBefore(draggedItem, afterElement);
            }
        });
    });
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.banner-card:not(.dragging)')];
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function saveOrder() {
    const cards = document.querySelectorAll('.banner-card');
    const orders = {};
    cards.forEach((card, index) => {
        orders[card.dataset.id] = index + 1;
    });
    
    fetch('hero_banners.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=reorder&orders=' + encodeURIComponent(JSON.stringify(orders))
    });
}
</script>

<?php
};

include __DIR__ . '/views/partials/layout.php';
