<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('teachers.read');

$canWrite = has_permission('*'); // admin/custom

// Handle create/update/delete
if (request_method() === 'POST') {
    csrf_verify_or_die();
    if (!$canWrite) {
        flash_set('danger', 'You do not have permission to modify teachers.');
        redirect('teachers');
    }

    $action = (string)post('action', '');

    if ($action === 'create' || $action === 'update') {
        $id = $action === 'update' ? (string)post('id', '') : '';
        
        // Basic Info
        $name = trim((string)post('name', ''));
        $phone = trim((string)post('phone', ''));
        $email = trim((string)post('email', ''));
        $dob = trim((string)post('dob', ''));
        $gender = trim((string)post('gender', 'male'));
        $address = trim((string)post('address', ''));
        
        // Professional Info
        $subject = trim((string)post('subject', ''));
        $designation = trim((string)post('designation', ''));
        $qualification = trim((string)post('qualification', ''));
        $experience = trim((string)post('experience', ''));
        $joining_date = trim((string)post('joining_date', ''));
        $salary = trim((string)post('salary', ''));
        
        // Documents
        $id_proof = trim((string)post('id_proof', ''));
        
        $status = trim((string)post('status', 'active'));

        if ($name === '' || $subject === '' || $joining_date === '') {
            flash_set('danger', 'Please fill all required fields.');
            redirect('teachers');
        }

        // Handle photo upload
        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $uploadResult = upload_image($_FILES['photo'], 'teacher');
            if ($uploadResult['success']) {
                $photo = $uploadResult['filename'];
            }
        }
        
        // Keep existing photo if not uploading new one
        if ($action === 'update' && $photo === '') {
            $existing = csv_find_by_id(DATA_PATH . '/teachers.csv', $id);
            $photo = $existing['photo'] ?? '';
        }

        $data = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'dob' => $dob,
            'gender' => $gender,
            'address' => $address,
            'subject' => $subject,
            'designation' => $designation,
            'qualification' => $qualification,
            'experience' => $experience,
            'joining_date' => $joining_date,
            'salary' => $salary,
            'id_proof' => $id_proof,
            'photo' => $photo,
            'status' => $status,
        ];

        if ($action === 'create') {
            csv_insert(DATA_PATH . '/teachers.csv', $data);
            flash_set('success', 'Teacher added successfully.');
        } else {
            csv_update_by_id(DATA_PATH . '/teachers.csv', $id, $data);
            flash_set('success', 'Teacher updated.');
        }
        redirect('teachers');
    }

    if ($action === 'delete') {
        $id = (string)post('id', '');
        if ($id !== '') {
            $teacher = csv_find_by_id(DATA_PATH . '/teachers.csv', $id);
            if ($teacher && !empty($teacher['photo'])) {
                delete_upload($teacher['photo']);
            }
            csv_delete_by_id(DATA_PATH . '/teachers.csv', $id);
        }
        flash_set('info', 'Teacher deleted.');
        redirect('teachers');
    }
    
    // Bulk Import
    if ($action === 'import') {
        if (empty($_FILES['import_file']['name'])) {
            flash_set('danger', 'Please select a CSV file to import.');
            redirect('teachers');
        }
        
        $file = $_FILES['import_file']['tmp_name'];
        $handle = fopen($file, 'r');
        if (!$handle) {
            flash_set('danger', 'Could not read the uploaded file.');
            redirect('teachers');
        }
        
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            flash_set('danger', 'Invalid CSV file.');
            redirect('teachers');
        }
        
        // Normalize headers
        $headers = array_map(function($h) { return strtolower(trim($h)); }, $headers);
        
        $imported = 0;
        $skipped = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) { $skipped++; continue; }
            
            $data = [];
            foreach ($headers as $i => $h) {
                $data[$h] = $row[$i] ?? '';
            }
            
            // Required fields check
            if (empty($data['name']) || empty($data['subject'])) {
                $skipped++;
                continue;
            }
            
            // Set defaults
            $data['joining_date'] = $data['joining_date'] ?? date('Y-m-d');
            $data['status'] = $data['status'] ?? 'active';
            $data['photo'] = ''; // Photos not imported via CSV
            
            csv_insert(DATA_PATH . '/teachers.csv', $data);
            $imported++;
        }
        
        fclose($handle);
        flash_set('success', "Import complete! $imported teachers imported, $skipped skipped.");
        redirect('teachers');
    }
}

// Handle CSV Export
if (get('export') === 'csv') {
    $teachers = csv_read_all(DATA_PATH . '/teachers.csv');
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="teachers_export_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Headers
    $exportHeaders = ['id', 'name', 'phone', 'email', 'dob', 'gender', 'address', 'subject', 'designation', 'qualification', 'experience', 'joining_date', 'salary', 'id_proof', 'status'];
    fputcsv($output, $exportHeaders);
    
    // Data
    foreach ($teachers as $t) {
        $row = [];
        foreach ($exportHeaders as $h) {
            $row[] = $t[$h] ?? '';
        }
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

// Download sample template
if (get('template') === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="teachers_import_template.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['name', 'phone', 'email', 'dob', 'gender', 'address', 'subject', 'designation', 'qualification', 'experience', 'joining_date', 'salary', 'id_proof', 'status']);
    fputcsv($output, ['John Doe', '9876543210', 'john@example.com', '1985-05-15', 'male', '123 Main St', 'Mathematics', 'Senior Teacher', 'M.Sc, B.Ed', '10', '2020-01-15', '50000', 'XXXX-XXXX-1234', 'active']);
    fclose($output);
    exit;
}

$q = trim((string)get('q', ''));
$teachers = csv_read_all(DATA_PATH . '/teachers.csv');
if ($q !== '') {
    $needle = mb_strtolower($q);
    $teachers = array_values(array_filter($teachers, function ($t) use ($needle) {
        $hay = mb_strtolower(($t['id'] ?? '') . ' ' . ($t['name'] ?? '') . ' ' . ($t['subject'] ?? '') . ' ' . ($t['designation'] ?? ''));
        return str_contains($hay, $needle);
    }));
}

$editId = (string)get('edit', '');
$editing = $editId ? csv_find_by_id(DATA_PATH . '/teachers.csv', $editId) : null;
$viewId = (string)get('view', '');
$viewing = $viewId ? csv_find_by_id(DATA_PATH . '/teachers.csv', $viewId) : null;

$title = 'Teachers';
$active = 'teachers';
$content = function () use ($canWrite, $teachers, $q, $editing, $viewing) {
?>
  <?php if ($viewing): ?>
    <!-- View Teacher Profile -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
          <h3 class="h5 mb-0">Teacher Profile</h3>
          <a href="<?= e(base_url('teachers')) ?>" class="btn btn-outline-light btn-sm">← Back to List</a>
        </div>
        
        <div class="row g-4">
          <div class="col-md-4 text-center">
            <?php if (!empty($viewing['photo'])): ?>
              <img src="<?= e(base_url('uploads/' . $viewing['photo'])) ?>" alt="<?= e($viewing['name']) ?>" 
                   class="rounded-circle mb-3" style="width:150px;height:150px;object-fit:cover;border:4px solid rgba(255,255,255,0.1);">
            <?php else: ?>
              <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                   style="width:150px;height:150px;font-size:48px;color:white;">
                <?= strtoupper(substr($viewing['name'] ?? 'T', 0, 1)) ?>
              </div>
            <?php endif; ?>
            <h4 class="mb-1"><?= e($viewing['name']) ?></h4>
            <span class="badge bg-primary mb-2"><?= e($viewing['designation'] ?? 'Teacher') ?></span>
            <p class="text-muted small"><?= e($viewing['subject']) ?></p>
          </div>
          
          <div class="col-md-8">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="p-3 rounded" style="background:rgba(255,255,255,0.03);">
                  <h6 class="text-muted small mb-3"><i class="bi bi-person me-2"></i>Personal Information</h6>
                  <p class="mb-2"><strong>Date of Birth:</strong> <?= e($viewing['dob'] ?? 'N/A') ?></p>
                  <p class="mb-2"><strong>Gender:</strong> <?= e(ucfirst($viewing['gender'] ?? 'N/A')) ?></p>
                  <p class="mb-2"><strong>Phone:</strong> <?= e($viewing['phone'] ?? 'N/A') ?></p>
                  <p class="mb-2"><strong>Email:</strong> <?= e($viewing['email'] ?? 'N/A') ?></p>
                  <p class="mb-0"><strong>Address:</strong> <?= e($viewing['address'] ?? 'N/A') ?></p>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="p-3 rounded" style="background:rgba(255,255,255,0.03);">
                  <h6 class="text-muted small mb-3"><i class="bi bi-briefcase me-2"></i>Professional Information</h6>
                  <p class="mb-2"><strong>Qualification:</strong> <?= e($viewing['qualification'] ?? 'N/A') ?></p>
                  <p class="mb-2"><strong>Experience:</strong> <?= e($viewing['experience'] ?? 'N/A') ?> years</p>
                  <p class="mb-2"><strong>Joining Date:</strong> <?= e($viewing['joining_date'] ?? 'N/A') ?></p>
                  <p class="mb-2"><strong>Salary:</strong> ₹<?= e($viewing['salary'] ?? 'N/A') ?></p>
                  <p class="mb-0"><strong>ID Proof:</strong> <?= e($viewing['id_proof'] ?? 'N/A') ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  <?php elseif ($editing || (get('action') === 'add')): ?>
    <!-- Add/Edit Form -->
    <div class="row g-3">
      <?php if ($canWrite): ?>
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h6 mb-0"><?= $editing ? 'Edit Teacher' : 'Add New Teacher' ?></h3>
                <a href="<?= e(base_url('teachers')) ?>" class="btn btn-outline-light btn-sm"><?= $editing ? 'Cancel' : 'View All Teachers' ?></a>
              </div>
              <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
                <?php if ($editing): ?>
                  <input type="hidden" name="id" value="<?= e($editing['id']) ?>">
                <?php endif; ?>
                
                <h6 class="text-muted small mb-2 mt-2"><i class="bi bi-person me-1"></i> Personal Info</h6>
                
                <div class="mb-2">
                  <label class="form-label">Full Name *</label>
                  <input class="form-control" name="name" required maxlength="120" value="<?= e($editing['name'] ?? '') ?>">
                </div>
                
                <div class="row g-2 mb-2">
                  <div class="col-6">
                    <label class="form-label">Phone</label>
                    <input class="form-control" name="phone" maxlength="15" value="<?= e($editing['phone'] ?? '') ?>">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" value="<?= e($editing['email'] ?? '') ?>">
                  </div>
                </div>
                
                <div class="row g-2 mb-2">
                  <div class="col-6">
                    <label class="form-label">Date of Birth</label>
                    <input class="form-control" type="date" name="dob" value="<?= e($editing['dob'] ?? '') ?>">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender">
                      <option value="male" <?= ($editing['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                      <option value="female" <?= ($editing['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                      <option value="other" <?= ($editing['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                  </div>
                </div>
                
                <div class="mb-2">
                  <label class="form-label">Address</label>
                  <textarea class="form-control" name="address" rows="2"><?= e($editing['address'] ?? '') ?></textarea>
                </div>
                
                <h6 class="text-muted small mb-2 mt-3"><i class="bi bi-briefcase me-1"></i> Professional Info</h6>
                
                <div class="row g-2 mb-2">
                  <div class="col-6">
                    <label class="form-label">Subject *</label>
                    <input class="form-control" name="subject" required value="<?= e($editing['subject'] ?? '') ?>">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Designation</label>
                    <input class="form-control" name="designation" value="<?= e($editing['designation'] ?? '') ?>" placeholder="e.g., Senior Teacher">
                  </div>
                </div>
                
                <div class="row g-2 mb-2">
                  <div class="col-6">
                    <label class="form-label">Qualification</label>
                    <input class="form-control" name="qualification" value="<?= e($editing['qualification'] ?? '') ?>" placeholder="e.g., M.Ed, B.Sc">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Experience (Years)</label>
                    <input class="form-control" type="number" name="experience" min="0" value="<?= e($editing['experience'] ?? '') ?>">
                  </div>
                </div>
                
                <div class="row g-2 mb-2">
                  <div class="col-6">
                    <label class="form-label">Joining Date *</label>
                    <input class="form-control" type="date" name="joining_date" required value="<?= e($editing['joining_date'] ?? date('Y-m-d')) ?>">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Salary (₹)</label>
                    <input class="form-control" type="number" name="salary" min="0" value="<?= e($editing['salary'] ?? '') ?>">
                  </div>
                </div>
                
                <h6 class="text-muted small mb-2 mt-3"><i class="bi bi-file-earmark me-1"></i> Documents</h6>
                
                <div class="mb-2">
                  <label class="form-label">Photo</label>
                  <input class="form-control" type="file" name="photo" accept="image/*">
                  <?php if ($editing && !empty($editing['photo'])): ?>
                    <small class="text-muted">Current: <?= e($editing['photo']) ?></small>
                  <?php endif; ?>
                </div>
                
                <div class="mb-2">
                  <label class="form-label">ID Proof Number</label>
                  <input class="form-control" name="id_proof" value="<?= e($editing['id_proof'] ?? '') ?>" placeholder="Aadhar / PAN">
                </div>
                
                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-select" name="status">
                    <option value="active" <?= ($editing['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($editing['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                  </select>
                </div>
                
                <button class="btn btn-primary" type="submit"><?= $editing ? 'Update Teacher' : 'Add Teacher' ?></button>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
  <?php else: ?>
    <!-- Teacher Records List -->
    <div class="row g-3">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
              <h3 class="h6 mb-0">Teacher Records <span class="badge bg-primary"><?= count($teachers) ?></span></h3>
              <div class="d-flex flex-wrap gap-2 align-items-center">
                <form class="d-flex gap-2" method="get">
                  <input class="form-control form-control-sm" name="q" placeholder="Search..." value="<?= e($q) ?>" style="width:150px;">
                  <button class="btn btn-sm btn-outline-light" type="submit"><i class="bi bi-search"></i></button>
                </form>
                <?php if ($canWrite): ?>
                <!-- Import/Export Dropdown -->
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-arrow-down-up me-1"></i>Import/Export
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Export</h6></li>
                    <li><a class="dropdown-item" href="<?= e(base_url('teachers?export=csv')) ?>"><i class="bi bi-download me-2"></i>Export All (CSV)</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Import</h6></li>
                    <li><a class="dropdown-item" href="<?= e(base_url('teachers?template=csv')) ?>"><i class="bi bi-file-earmark-arrow-down me-2"></i>Download Template</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bi bi-upload me-2"></i>Import CSV</a></li>
                  </ul>
                </div>
                <a href="<?= e(base_url('teachers?action=add')) ?>" class="btn btn-sm btn-primary">+ Add</a>
                <?php endif; ?>
              </div>
            </div>

            <div class="row g-3">
              <?php if (!$teachers): ?>
                <div class="col-12"><p class="text-muted">No teachers found.</p></div>
              <?php else: ?>
                <?php foreach ($teachers as $t): ?>
                  <div class="col-md-6 col-lg-4">
                    <div class="card h-100" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);">
                      <div class="card-body text-center p-3">
                        <?php if (!empty($t['photo'])): ?>
                          <img src="<?= e(base_url('uploads/' . $t['photo'])) ?>" alt="" 
                               class="rounded-circle mb-2" style="width:70px;height:70px;object-fit:cover;">
                        <?php else: ?>
                          <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-2" 
                               style="width:70px;height:70px;font-size:24px;color:white;">
                            <?= strtoupper(substr($t['name'] ?? 'T', 0, 1)) ?>
                          </div>
                        <?php endif; ?>
                        <h6 class="mb-1"><?= e($t['name']) ?></h6>
                        <small class="text-muted d-block mb-2"><?= e($t['subject']) ?> | <?= e($t['designation'] ?? 'Teacher') ?></small>
                        <span class="badge <?= ($t['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-secondary' ?> mb-2">
                          <?= e(ucfirst($t['status'] ?? 'active')) ?>
                        </span>
                        <div class="d-flex gap-1 justify-content-center mt-2">
                          <a href="<?= e(base_url('teachers?view=' . $t['id'])) ?>" class="btn btn-sm btn-outline-info">View</a>
                          <?php if ($canWrite): ?>
                            <a href="<?= e(base_url('teachers?edit=' . $t['id'])) ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this teacher?');">
                              <?= csrf_field() ?>
                              <input type="hidden" name="action" value="delete">
                              <input type="hidden" name="id" value="<?= e($t['id']) ?>">
                              <button class="btn btn-sm btn-outline-danger" type="submit">Del</button>
                            </form>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  
  <!-- Import Modal -->
  <?php if ($canWrite): ?>
  <div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Import Teachers</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="import">
          <div class="modal-body">
            <div class="alert alert-info small">
              <i class="bi bi-info-circle me-1"></i>
              <strong>Instructions:</strong>
              <ul class="mb-0 mt-2">
                <li>Download the <a href="<?= e(base_url('teachers?template=csv')) ?>">CSV template</a> first</li>
                <li>Fill in teacher data (name & subject are required)</li>
                <li>Save and upload the CSV file</li>
              </ul>
            </div>
            <div class="mb-3">
              <label class="form-label">Select CSV File</label>
              <input type="file" class="form-control" name="import_file" accept=".csv" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i>Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>
<?php
};

include __DIR__ . '/views/partials/layout.php';
