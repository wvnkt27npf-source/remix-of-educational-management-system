<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('*');

// Initialize CSV
csv_init(DATA_PATH . '/news.csv', ['id', 'title', 'content', 'image', 'date', 'status', 'created_at']);

if (request_method() === 'POST') {
    csrf_verify_or_die();
    $action = (string)post('action', '');

    if ($action === 'create') {
        $title = trim((string)post('title', ''));
        $content = trim((string)post('content', ''));
        $date = trim((string)post('date', ''));
        $status = trim((string)post('status', 'published'));
        
        if ($title === '' || $content === '') {
            flash_set('danger', 'Title and content are required.');
            redirect('news');
        }
        
        $imageUrl = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = upload_image($_FILES['image'], 'news');
            if ($upload['success']) {
                $imageUrl = $upload['url'];
            } else {
                flash_set('danger', $upload['error']);
                redirect('news');
            }
        }
        
        csv_insert(DATA_PATH . '/news.csv', [
            'title' => $title,
            'content' => $content,
            'image' => $imageUrl,
            'date' => $date ?: date('Y-m-d'),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        flash_set('success', 'News article created.');
        redirect('news');
    }

    if ($action === 'update') {
        $id = (string)post('id', '');
        $title = trim((string)post('title', ''));
        $content = trim((string)post('content', ''));
        $date = trim((string)post('date', ''));
        $status = trim((string)post('status', 'published'));
        
        if ($id === '' || $title === '' || $content === '') {
            flash_set('danger', 'Title and content are required.');
            redirect('news');
        }
        
        $updateData = [
            'title' => $title,
            'content' => $content,
            'date' => $date,
            'status' => $status,
        ];
        
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = upload_image($_FILES['image'], 'news');
            if ($upload['success']) {
                $updateData['image'] = $upload['url'];
            }
        }
        
        csv_update_by_id(DATA_PATH . '/news.csv', $id, $updateData);
        flash_set('success', 'News article updated.');
        redirect('news');
    }

    if ($action === 'delete') {
        $id = (string)post('id', '');
        if ($id !== '') {
            csv_delete_by_id(DATA_PATH . '/news.csv', $id);
        }
        flash_set('info', 'News article deleted.');
        redirect('news');
    }
}

$news = csv_read_all(DATA_PATH . '/news.csv');
usort($news, fn($a, $b) => (int)$b['id'] <=> (int)$a['id']);

$editId = (string)get('edit', '');
$editing = $editId ? csv_find_by_id(DATA_PATH . '/news.csv', $editId) : null;

$title = 'News & Announcements';
$active = 'news';
$content = function () use ($news, $editing) {
?>
  <?php if ($editing || get('action') === 'add'): ?>
    <!-- Add/Edit Form -->
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="h5 mb-0"><?= $editing ? 'Edit Article' : 'Add News Article' ?></h3>
          <a href="news" class="btn btn-outline-secondary btn-sm">
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
              <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input class="form-control" name="title" required maxlength="200" value="<?= e($editing['title'] ?? '') ?>" placeholder="Enter news title">
              </div>
              
              <div class="mb-3">
                <label class="form-label">Content <span class="text-danger">*</span></label>
                <textarea class="form-control" name="content" rows="6" required maxlength="5000" placeholder="Write the news content..."><?= e($editing['content'] ?? '') ?></textarea>
              </div>
            </div>
            
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Featured Image</label>
                <?php if ($editing && !empty($editing['image'])): ?>
                  <div class="mb-2">
                    <img src="<?= e($editing['image']) ?>" alt="Current" class="img-fluid rounded" style="max-height:150px;">
                  </div>
                <?php endif; ?>
                <input class="form-control" type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small class="text-muted">Max 5MB. JPG, PNG, GIF, WEBP</small>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Date</label>
                <input class="form-control" type="date" name="date" value="<?= e($editing['date'] ?? date('Y-m-d')) ?>">
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
              <?= $editing ? 'Update Article' : 'Create Article' ?>
            </button>
            <a href="news" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  <?php else: ?>
    <!-- List View -->
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="h5 mb-0">All News Articles</h3>
          <a href="news?action=add" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add News
          </a>
        </div>
        
        <?php if (empty($news)): ?>
          <div class="text-center py-5">
            <i class="bi bi-newspaper text-muted" style="font-size:48px;"></i>
            <p class="text-muted mt-3">No news articles yet. Click "Add News" to create one.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th style="width:60px;">Image</th>
                  <th>Title</th>
                  <th style="width:120px;">Date</th>
                  <th style="width:100px;">Status</th>
                  <th style="width:120px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($news as $n): ?>
                  <tr>
                    <td>
                      <?php if (!empty($n['image'])): ?>
                        <img src="<?= e($n['image']) ?>" alt="" style="width:50px;height:40px;object-fit:cover;border-radius:6px;">
                      <?php else: ?>
                        <div style="width:50px;height:40px;border-radius:6px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;">
                          <i class="bi bi-image text-muted"></i>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="fw-medium"><?= e($n['title']) ?></div>
                      <small class="text-muted" style="display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">
                        <?= e(substr($n['content'], 0, 80)) ?>...
                      </small>
                    </td>
                    <td><small><?= e($n['date']) ?></small></td>
                    <td>
                      <span class="badge <?= ($n['status'] ?? 'published') === 'published' ? 'bg-success' : 'bg-secondary' ?>">
                        <?= ucfirst(e($n['status'] ?? 'published')) ?>
                      </span>
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="news?edit=<?= e($n['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this article?');">
                          <?= csrf_field() ?>
                          <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="id" value="<?= e($n['id']) ?>">
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
