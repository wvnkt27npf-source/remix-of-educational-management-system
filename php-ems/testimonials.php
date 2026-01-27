<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('*');

// Initialize CSV
csv_init(DATA_PATH . '/testimonials.csv', ['id', 'name', 'role', 'content', 'image', 'rating', 'status', 'created_at']);

if (request_method() === 'POST') {
    csrf_verify_or_die();
    $action = (string)post('action', '');

    if ($action === 'create') {
        $name = trim((string)post('name', ''));
        $role = trim((string)post('role', ''));
        $content = trim((string)post('content', ''));
        $rating = (int)post('rating', 5);
        $status = trim((string)post('status', 'published'));
        
        if ($name === '' || $content === '') {
            flash_set('danger', 'Name and testimonial content are required.');
            redirect('testimonials');
        }
        
        $imageUrl = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = upload_image($_FILES['image'], 'testimonial');
            if ($upload['success']) {
                $imageUrl = $upload['url'];
            }
        }
        
        csv_insert(DATA_PATH . '/testimonials.csv', [
            'name' => $name,
            'role' => $role,
            'content' => $content,
            'image' => $imageUrl,
            'rating' => min(5, max(1, $rating)),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        flash_set('success', 'Testimonial added.');
        redirect('testimonials');
    }

    if ($action === 'update') {
        $id = (string)post('id', '');
        $name = trim((string)post('name', ''));
        $role = trim((string)post('role', ''));
        $content = trim((string)post('content', ''));
        $rating = (int)post('rating', 5);
        $status = trim((string)post('status', 'published'));
        
        if ($id === '' || $name === '' || $content === '') {
            flash_set('danger', 'Name and content are required.');
            redirect('testimonials');
        }
        
        $updateData = [
            'name' => $name,
            'role' => $role,
            'content' => $content,
            'rating' => min(5, max(1, $rating)),
            'status' => $status,
        ];
        
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = upload_image($_FILES['image'], 'testimonial');
            if ($upload['success']) {
                $updateData['image'] = $upload['url'];
            }
        }
        
        csv_update_by_id(DATA_PATH . '/testimonials.csv', $id, $updateData);
        flash_set('success', 'Testimonial updated.');
        redirect('testimonials');
    }

    if ($action === 'delete') {
        $id = (string)post('id', '');
        if ($id !== '') {
            csv_delete_by_id(DATA_PATH . '/testimonials.csv', $id);
        }
        flash_set('info', 'Testimonial deleted.');
        redirect('testimonials');
    }
}

$testimonials = csv_read_all(DATA_PATH . '/testimonials.csv');
usort($testimonials, fn($a, $b) => (int)$b['id'] <=> (int)$a['id']);

$editId = (string)get('edit', '');
$editing = $editId ? csv_find_by_id(DATA_PATH . '/testimonials.csv', $editId) : null;

$title = 'Testimonials';
$active = 'testimonials';
$content = function () use ($testimonials, $editing) {
?>
  <?php if ($editing || get('action') === 'add'): ?>
    <!-- Add/Edit Form -->
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="h5 mb-0"><?= $editing ? 'Edit Testimonial' : 'Add Testimonial' ?></h3>
          <a href="testimonials" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
          </a>
        </div>
        
        <form method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
          <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= e($editing['id']) ?>">
          <?php endif; ?>
          
          <div class="row g-3">
            <div class="col-md-8">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Name <span class="text-danger">*</span></label>
                  <input class="form-control" name="name" required maxlength="100" value="<?= e($editing['name'] ?? '') ?>" placeholder="e.g. Rahul Sharma">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Role / Designation</label>
                  <input class="form-control" name="role" maxlength="100" value="<?= e($editing['role'] ?? '') ?>" placeholder="e.g. Parent of Class 10 Student">
                </div>
              </div>
              
              <div class="mt-3">
                <label class="form-label">Testimonial <span class="text-danger">*</span></label>
                <textarea class="form-control" name="content" rows="5" required maxlength="1000" placeholder="What they said about the school..."><?= e($editing['content'] ?? '') ?></textarea>
              </div>
            </div>
            
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Photo</label>
                <?php if ($editing && !empty($editing['image'])): ?>
                  <div class="mb-2">
                    <img src="<?= e($editing['image']) ?>" alt="Current" style="width:80px;height:80px;object-fit:cover;border-radius:50%;">
                  </div>
                <?php endif; ?>
                <input class="form-control" type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small class="text-muted">Optional. Square photo works best.</small>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Rating</label>
                <select class="form-select" name="rating">
                  <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= (int)($editing['rating'] ?? 5) === $i ? 'selected' : '' ?>><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                  <?php endfor; ?>
                </select>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                  <option value="published" <?= ($editing['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                  <option value="draft" <?= ($editing['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary" type="submit">
              <i class="bi bi-<?= $editing ? 'check-lg' : 'plus-lg' ?> me-1"></i>
              <?= $editing ? 'Update Testimonial' : 'Add Testimonial' ?>
            </button>
            <a href="testimonials" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  <?php else: ?>
    <!-- List View -->
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="h5 mb-0">All Testimonials</h3>
          <a href="testimonials?action=add" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Testimonial
          </a>
        </div>
        
        <?php if (empty($testimonials)): ?>
          <div class="text-center py-5">
            <i class="bi bi-chat-quote text-muted" style="font-size:48px;"></i>
            <p class="text-muted mt-3">No testimonials yet. Click "Add Testimonial" to create one.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th style="width:60px;">Photo</th>
                  <th>Name & Role</th>
                  <th>Testimonial</th>
                  <th style="width:100px;">Rating</th>
                  <th style="width:100px;">Status</th>
                  <th style="width:120px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($testimonials as $t): ?>
                  <tr>
                    <td>
                      <?php if (!empty($t['image'])): ?>
                        <img src="<?= e($t['image']) ?>" alt="" style="width:45px;height:45px;object-fit:cover;border-radius:50%;">
                      <?php else: ?>
                        <div style="width:45px;height:45px;border-radius:50%;background:linear-gradient(135deg,#4f6ef7,#a855f7);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:16px;">
                          <?= strtoupper(substr($t['name'] ?? 'U', 0, 1)) ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="fw-medium"><?= e($t['name']) ?></div>
                      <small class="text-muted"><?= e($t['role']) ?></small>
                    </td>
                    <td>
                      <small class="text-muted" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        "<?= e($t['content']) ?>"
                      </small>
                    </td>
                    <td>
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= (int)($t['rating'] ?? 5) ? '-fill' : '' ?>" style="color:#fbbf24;font-size:12px;"></i>
                      <?php endfor; ?>
                    </td>
                    <td>
                      <span class="badge <?= ($t['status'] ?? 'published') === 'published' ? 'bg-success' : 'bg-secondary' ?>">
                        <?= ucfirst(e($t['status'] ?? 'published')) ?>
                      </span>
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="testimonials?edit=<?= e($t['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this testimonial?');">
                          <?= csrf_field() ?>
                          <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="id" value="<?= e($t['id']) ?>">
                          <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
<?php
};

include __DIR__ . '/views/partials/layout.php';
