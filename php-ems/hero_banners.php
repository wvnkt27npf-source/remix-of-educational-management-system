<?php
/**
 * Hero Banners Management - Simple Image Slider
 * Features: Upload images, enable/disable slider, drag to reorder, edit, delete
 */
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

// Handle form submissions
if (request_method() === 'POST') {
    $action = post('action', '');
    
    // Toggle global slider enable/disable
    if ($action === 'toggle_slider') {
        $settingsFile = DATA_PATH . '/site_settings.csv';
        $currentValue = '0';
        
        if (file_exists($settingsFile)) {
            $allSettings = csv_read_all($settingsFile);
            foreach ($allSettings as $s) {
                if ($s['key'] === 'hero_slider_enabled') {
                    $currentValue = $s['value'];
                    break;
                }
            }
        }
        
        $newValue = $currentValue === '1' ? '0' : '1';
        
        // Update or insert setting
        $found = false;
        if (file_exists($settingsFile)) {
            $allSettings = csv_read_all($settingsFile);
            foreach ($allSettings as &$s) {
                if ($s['key'] === 'hero_slider_enabled') {
                    $s['value'] = $newValue;
                    $found = true;
                    break;
                }
            }
            if ($found) {
                // Rewrite the file
                $fp = fopen($settingsFile, 'w');
                fputcsv($fp, ['id', 'key', 'value', 'updated_at']);
                foreach ($allSettings as $s) {
                    fputcsv($fp, [$s['id'], $s['key'], $s['value'], $s['updated_at'] ?? date('Y-m-d H:i:s')]);
                }
                fclose($fp);
            }
        }
        
        if (!$found) {
            csv_insert($settingsFile, [
                'key' => 'hero_slider_enabled',
                'value' => $newValue,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        flash_set('success', 'Banner slider ' . ($newValue === '1' ? 'enabled' : 'disabled') . '!');
        redirect('hero_banners');
    }
    
    // Add new banner
    if ($action === 'add') {
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($ext, $allowedExts)) {
                flash_set('error', 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.');
                redirect('hero_banners');
            }
            
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $bannerUploadDir . $filename)) {
                // Get max display order
                $banners = csv_read_all(DATA_PATH . '/hero_banners.csv');
                $maxOrder = 0;
                foreach ($banners as $b) {
                    $maxOrder = max($maxOrder, (int)($b['display_order'] ?? 0));
                }
                
                csv_insert(DATA_PATH . '/hero_banners.csv', [
                    'image' => 'uploads/banners/' . $filename,
                    'alt_text' => trim((string)post('alt_text', '')),
                    'link_url' => trim((string)post('link_url', '')),
                    'is_active' => '1',
                    'display_order' => $maxOrder + 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                flash_set('success', 'Banner added successfully!');
            } else {
                flash_set('error', 'Failed to upload image.');
            }
        } else {
            flash_set('error', 'Please select an image to upload.');
        }
        redirect('hero_banners');
    }
    
    // Edit banner
    if ($action === 'edit') {
        $id = post('id');
        $existing = csv_find_by_id(DATA_PATH . '/hero_banners.csv', $id);
        
        if ($existing) {
            $data = [
                'alt_text' => trim((string)post('alt_text', '')),
                'link_url' => trim((string)post('link_url', '')),
            ];
            
            // Handle new image upload
            if (!empty($_FILES['image']['name'])) {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($ext, $allowedExts)) {
                    // Delete old image
                    if (!empty($existing['image']) && file_exists(__DIR__ . '/' . $existing['image'])) {
                        @unlink(__DIR__ . '/' . $existing['image']);
                    }
                    
                    $filename = 'banner_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $bannerUploadDir . $filename)) {
                        $data['image'] = 'uploads/banners/' . $filename;
                    }
                }
            }
            
            csv_update_by_id(DATA_PATH . '/hero_banners.csv', $id, $data);
            flash_set('success', 'Banner updated successfully!');
        }
        redirect('hero_banners');
    }
    
    // Delete banner
    if ($action === 'delete') {
        $id = post('id');
        $banner = csv_find_by_id(DATA_PATH . '/hero_banners.csv', $id);
        
        if ($banner) {
            // Delete image file
            if (!empty($banner['image']) && file_exists(__DIR__ . '/' . $banner['image'])) {
                @unlink(__DIR__ . '/' . $banner['image']);
            }
            csv_delete_by_id(DATA_PATH . '/hero_banners.csv', $id);
            flash_set('success', 'Banner deleted successfully!');
        }
        redirect('hero_banners');
    }
    
    // Toggle individual banner
    if ($action === 'toggle') {
        $banner = csv_find_by_id(DATA_PATH . '/hero_banners.csv', post('id'));
        if ($banner) {
            csv_update_by_id(DATA_PATH . '/hero_banners.csv', post('id'), [
                'is_active' => $banner['is_active'] === '1' ? '0' : '1',
            ]);
        }
        redirect('hero_banners');
    }
    
    // Reorder banners
    if ($action === 'reorder') {
        $orders = json_decode(post('orders', '[]'), true);
        if (is_array($orders)) {
            foreach ($orders as $id => $order) {
                csv_update_by_id(DATA_PATH . '/hero_banners.csv', $id, ['display_order' => (int)$order]);
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

// Load banners
$banners = csv_read_all(DATA_PATH . '/hero_banners.csv');
usort($banners, function($a, $b) {
    return ((int)($a['display_order'] ?? 0)) - ((int)($b['display_order'] ?? 0));
});

// Get slider enabled status
$sliderEnabled = false;
$settingsFile = DATA_PATH . '/site_settings.csv';
if (file_exists($settingsFile)) {
    $allSettings = csv_read_all($settingsFile);
    foreach ($allSettings as $s) {
        if ($s['key'] === 'hero_slider_enabled') {
            $sliderEnabled = $s['value'] === '1';
            break;
        }
    }
}

$content = function() use ($banners, $sliderEnabled) {
?>
<style>
.slider-toggle-card {
    background: linear-gradient(135deg, rgba(255,255,255,.05) 0%, rgba(255,255,255,.02) 100%);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
}
.slider-toggle-card.enabled {
    border-color: rgba(40, 167, 69, 0.4);
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.02) 100%);
}
.slider-toggle-card.disabled {
    border-color: rgba(220, 53, 69, 0.3);
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.08) 0%, rgba(220, 53, 69, 0.02) 100%);
}
.toggle-switch {
    position: relative;
    width: 60px;
    height: 32px;
    cursor: pointer;
}
.toggle-switch input { display: none; }
.toggle-slider {
    position: absolute;
    inset: 0;
    background: rgba(108, 117, 125, 0.4);
    border-radius: 32px;
    transition: all 0.3s;
}
.toggle-slider::before {
    content: '';
    position: absolute;
    width: 26px;
    height: 26px;
    left: 3px;
    top: 3px;
    background: white;
    border-radius: 50%;
    transition: all 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,.2);
}
.toggle-switch input:checked + .toggle-slider {
    background: #28a745;
}
.toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(28px);
}

.banner-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
.banner-card {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s;
    cursor: grab;
}
.banner-card:hover {
    border-color: rgba(255,255,255,.2);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
}
.banner-card.dragging {
    opacity: 0.5;
    cursor: grabbing;
}
.banner-card.inactive {
    opacity: 0.5;
}
.banner-image {
    width: 100%;
    aspect-ratio: 16/9;
    object-fit: cover;
    background: rgba(255,255,255,.05);
}
.banner-actions {
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    border-top: 1px solid rgba(255,255,255,.06);
}
.banner-order {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.1);
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
}
.drag-handle {
    cursor: grab;
    padding: 8px;
    color: rgba(255,255,255,.3);
}
.upload-zone {
    border: 2px dashed rgba(255,255,255,.2);
    border-radius: 16px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: rgba(255,255,255,.02);
}
.upload-zone:hover {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.05);
}
.upload-zone.dragover {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.1);
}
.upload-icon {
    font-size: 3rem;
    color: rgba(255,255,255,.3);
    margin-bottom: 16px;
}
.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}
.status-dot.active { background: #28a745; }
.status-dot.inactive { background: #6c757d; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Hero Banner Slider</h4>
        <p class="text-muted mb-0">Upload banner images for your landing page slider</p>
    </div>
</div>

<!-- Global Enable/Disable Toggle -->
<div class="slider-toggle-card <?= $sliderEnabled ? 'enabled' : 'disabled' ?>">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">
                <i class="bi bi-<?= $sliderEnabled ? 'check-circle text-success' : 'x-circle text-danger' ?> me-2"></i>
                Banner Slider is <?= $sliderEnabled ? 'Enabled' : 'Disabled' ?>
            </h5>
            <p class="text-muted mb-0">
                <?= $sliderEnabled 
                    ? 'Banner slider is showing on all landing page templates.' 
                    : 'Enable to show banner slider at the top of your landing page.' ?>
            </p>
        </div>
        <form method="post" class="d-inline">
            <input type="hidden" name="action" value="toggle_slider">
            <label class="toggle-switch">
                <input type="checkbox" <?= $sliderEnabled ? 'checked' : '' ?> onchange="this.form.submit()">
                <span class="toggle-slider"></span>
            </label>
        </form>
    </div>
</div>

<!-- Upload Zone -->
<div class="upload-zone mb-4" onclick="document.getElementById('imageInput').click();" id="uploadZone">
    <i class="bi bi-cloud-arrow-up upload-icon"></i>
    <h5>Click to Upload or Drag & Drop</h5>
    <p class="text-muted mb-0">JPG, PNG, GIF, WEBP • Recommended size: 1920 x 600 px</p>
</div>

<form method="post" enctype="multipart/form-data" id="uploadForm" style="display: none;">
    <input type="hidden" name="action" value="add">
    <input type="file" name="image" id="imageInput" accept="image/*" onchange="this.form.submit()">
</form>

<!-- Banners Grid -->
<?php if (empty($banners)): ?>
<div class="text-center py-5">
    <i class="bi bi-images display-1 text-muted mb-3"></i>
    <h5>No Banners Yet</h5>
    <p class="text-muted">Upload your first banner image to get started.</p>
</div>
<?php else: ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="bi bi-images me-2"></i>
        <?= count($banners) ?> Banner<?= count($banners) > 1 ? 's' : '' ?>
    </h6>
    <small class="text-muted">
        <i class="bi bi-grip-vertical me-1"></i>Drag to reorder
    </small>
</div>

<div class="banner-grid" id="bannerGrid">
    <?php foreach ($banners as $index => $banner): 
        $isActive = ($banner['is_active'] ?? '1') === '1';
    ?>
    <div class="banner-card <?= $isActive ? '' : 'inactive' ?>" data-id="<?= e($banner['id']) ?>" draggable="true">
        <?php if (!empty($banner['image']) && file_exists(__DIR__ . '/' . $banner['image'])): ?>
            <img src="<?= e($banner['image']) ?>" alt="<?= e($banner['alt_text'] ?? 'Banner') ?>" class="banner-image">
        <?php else: ?>
            <div class="banner-image d-flex align-items-center justify-content-center">
                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
            </div>
        <?php endif; ?>
        
        <div class="banner-actions">
            <div class="d-flex align-items-center gap-2">
                <div class="drag-handle">
                    <i class="bi bi-grip-vertical"></i>
                </div>
                <span class="banner-order"><?= $index + 1 ?></span>
                <span class="status-dot <?= $isActive ? 'active' : 'inactive' ?>" title="<?= $isActive ? 'Active' : 'Inactive' ?>"></span>
            </div>
            
            <div class="d-flex gap-2">
                <form method="post" class="d-inline">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= e($banner['id']) ?>">
                    <button type="submit" class="btn btn-sm <?= $isActive ? 'btn-outline-success' : 'btn-outline-secondary' ?>" title="<?= $isActive ? 'Disable' : 'Enable' ?>">
                        <i class="bi bi-<?= $isActive ? 'eye' : 'eye-slash' ?>"></i>
                    </button>
                </form>
                
                <button class="btn btn-sm btn-outline-primary" onclick="editBanner(<?= htmlspecialchars(json_encode($banner), ENT_QUOTES) ?>)" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this banner?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= e($banner['id']) ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <form method="post" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Edit Banner</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div id="currentImagePreview" class="mb-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Replace Image (Optional)</label>
                        <input type="file" name="image" class="form-control bg-dark text-white border-secondary" accept="image/*">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alt Text (Optional)</label>
                        <input type="text" name="alt_text" id="editAltText" class="form-control bg-dark text-white border-secondary" placeholder="Describe the image">
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label">Link URL (Optional)</label>
                        <input type="text" name="link_url" id="editLinkUrl" class="form-control bg-dark text-white border-secondary" placeholder="e.g., #admission or https://...">
                        <small class="text-muted">Where should clicking this banner go?</small>
                    </div>
                </div>
                
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Drag and drop upload
const uploadZone = document.getElementById('uploadZone');
const imageInput = document.getElementById('imageInput');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadZone.addEventListener(eventName, e => {
        e.preventDefault();
        e.stopPropagation();
    });
});

['dragenter', 'dragover'].forEach(eventName => {
    uploadZone.addEventListener(eventName, () => uploadZone.classList.add('dragover'));
});

['dragleave', 'drop'].forEach(eventName => {
    uploadZone.addEventListener(eventName, () => uploadZone.classList.remove('dragover'));
});

uploadZone.addEventListener('drop', e => {
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        imageInput.files = files;
        document.getElementById('uploadForm').submit();
    }
});

// Edit banner
function editBanner(banner) {
    document.getElementById('editId').value = banner.id;
    document.getElementById('editAltText').value = banner.alt_text || '';
    document.getElementById('editLinkUrl').value = banner.link_url || '';
    
    const preview = document.getElementById('currentImagePreview');
    if (banner.image) {
        preview.innerHTML = `<img src="${banner.image}" style="max-width: 100%; max-height: 150px; border-radius: 8px;">`;
    } else {
        preview.innerHTML = '<span class="text-muted">No image</span>';
    }
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Drag and drop reordering
const bannerGrid = document.getElementById('bannerGrid');
if (bannerGrid) {
    let draggedItem = null;
    
    bannerGrid.querySelectorAll('.banner-card').forEach(card => {
        card.addEventListener('dragstart', e => {
            draggedItem = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        
        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            updateOrderNumbers();
            saveOrder();
        });
        
        card.addEventListener('dragover', e => {
            e.preventDefault();
            if (draggedItem && draggedItem !== card) {
                const rect = card.getBoundingClientRect();
                const midY = rect.top + rect.height / 2;
                if (e.clientY < midY) {
                    bannerGrid.insertBefore(draggedItem, card);
                } else {
                    bannerGrid.insertBefore(draggedItem, card.nextSibling);
                }
            }
        });
    });
}

function updateOrderNumbers() {
    document.querySelectorAll('.banner-card').forEach((card, index) => {
        card.querySelector('.banner-order').textContent = index + 1;
    });
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
