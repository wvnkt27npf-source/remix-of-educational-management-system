<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('students.read');

$canWrite = has_permission('*'); // admin/custom only can edit core details

// Handle create/update/delete
if (request_method() === 'POST') {
    csrf_verify_or_die();
    if (!$canWrite) {
        flash_set('danger', 'You do not have permission to modify students.');
        redirect('students');
    }

    $action = (string)post('action', '');

    if ($action === 'create' || $action === 'update') {
        $id = $action === 'update' ? (string)post('id', '') : '';
        
        // Basic Info
        $name = trim((string)post('name', ''));
        $class = trim((string)post('class', ''));
        $section = trim((string)post('section', ''));
        $roll_number = trim((string)post('roll_number', ''));
        $admission_date = trim((string)post('admission_date', ''));
        $status = trim((string)post('status', 'active'));
        
        // Personal Info (Admin only)
        $father_name = trim((string)post('father_name', ''));
        $mother_name = trim((string)post('mother_name', ''));
        $dob = trim((string)post('dob', ''));
        $blood_group = trim((string)post('blood_group', ''));
        $gender = trim((string)post('gender', 'male'));
        $religion = trim((string)post('religion', ''));
        $category = trim((string)post('category', ''));
        $phone = trim((string)post('phone', ''));
        $alt_phone = trim((string)post('alt_phone', ''));
        $email = trim((string)post('email', ''));
        $address = trim((string)post('address', ''));
        $city = trim((string)post('city', ''));
        $state = trim((string)post('state', ''));
        $pincode = trim((string)post('pincode', ''));
        
        // Documents
        $aadhar_number = trim((string)post('aadhar_number', ''));
        $previous_school = trim((string)post('previous_school', ''));
        $previous_class = trim((string)post('previous_class', ''));
        
        // Student editable
        $emergency_contact = trim((string)post('emergency_contact', ''));
        $medical_conditions = trim((string)post('medical_conditions', ''));

        if ($name === '' || $class === '' || $admission_date === '') {
            flash_set('danger', 'Please fill all required fields.');
            redirect('students');
        }

        // Handle photo upload
        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $uploadResult = upload_image($_FILES['photo'], 'student');
            if ($uploadResult['success']) {
                $photo = $uploadResult['filename'];
            }
        }
        
        // Keep existing photo/docs if not uploading new ones
        if ($action === 'update' && $photo === '') {
            $existing = csv_find_by_id(DATA_PATH . '/students.csv', $id);
            $photo = $existing['photo'] ?? '';
        }

        $data = [
            'name' => $name,
            'class' => $class,
            'section' => $section,
            'roll_number' => $roll_number,
            'admission_date' => $admission_date,
            'status' => $status,
            'father_name' => $father_name,
            'mother_name' => $mother_name,
            'dob' => $dob,
            'blood_group' => $blood_group,
            'gender' => $gender,
            'religion' => $religion,
            'category' => $category,
            'phone' => $phone,
            'alt_phone' => $alt_phone,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'photo' => $photo,
            'aadhar_number' => $aadhar_number,
            'birth_certificate' => '',
            'transfer_certificate' => '',
            'previous_school' => $previous_school,
            'previous_class' => $previous_class,
            'emergency_contact' => $emergency_contact,
            'medical_conditions' => $medical_conditions,
        ];

        if ($action === 'create') {
            csv_insert(DATA_PATH . '/students.csv', $data);
            flash_set('success', 'Student added successfully.');
        } else {
            csv_update_by_id(DATA_PATH . '/students.csv', $id, $data);
            flash_set('success', 'Student updated.');
        }
        redirect('students');
    }

    if ($action === 'delete') {
        $id = (string)post('id', '');
        if ($id !== '') {
            $student = csv_find_by_id(DATA_PATH . '/students.csv', $id);
            if ($student && !empty($student['photo'])) {
                delete_upload($student['photo']);
            }
            csv_delete_by_id(DATA_PATH . '/students.csv', $id);
        }
        flash_set('info', 'Student deleted.');
        redirect('students');
    }
    
    // Bulk Import
    if ($action === 'import') {
        if (empty($_FILES['import_file']['name'])) {
            flash_set('danger', 'Please select a CSV file to import.');
            redirect('students');
        }
        
        $file = $_FILES['import_file']['tmp_name'];
        $handle = fopen($file, 'r');
        if (!$handle) {
            flash_set('danger', 'Could not read the uploaded file.');
            redirect('students');
        }
        
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            flash_set('danger', 'Invalid CSV file.');
            redirect('students');
        }
        
        // Normalize headers
        $headers = array_map(function($h) { return strtolower(trim($h)); }, $headers);
        
        $imported = 0;
        $skipped = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) { $skipped++; continue; }
            
            $data = [];
            foreach ($headers as $i => $h) {
                $data[$h] = $row[$i] ?? '';
            }
            
            // Required fields check
            if (empty($data['name']) || empty($data['class'])) {
                $skipped++;
                continue;
            }
            
            // Set defaults
            $data['admission_date'] = $data['admission_date'] ?? date('Y-m-d');
            $data['status'] = $data['status'] ?? 'active';
            $data['photo'] = ''; // Photos not imported via CSV
            
            csv_insert(DATA_PATH . '/students.csv', $data);
            $imported++;
        }
        
        fclose($handle);
        flash_set('success', "Import complete! $imported students imported, $skipped skipped.");
        redirect('students');
    }
}

// Handle CSV Export
if (get('export') === 'csv') {
    $students = csv_read_all(DATA_PATH . '/students.csv');
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_export_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Headers
    $exportHeaders = ['id', 'name', 'class', 'section', 'roll_number', 'admission_date', 'status', 'father_name', 'mother_name', 'dob', 'blood_group', 'gender', 'religion', 'category', 'phone', 'alt_phone', 'email', 'address', 'city', 'state', 'pincode', 'aadhar_number', 'previous_school', 'previous_class', 'emergency_contact', 'medical_conditions'];
    fputcsv($output, $exportHeaders);
    
    // Data
    foreach ($students as $s) {
        $row = [];
        foreach ($exportHeaders as $h) {
            $row[] = $s[$h] ?? '';
        }
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

// Download sample template
if (get('template') === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_import_template.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['name', 'class', 'section', 'roll_number', 'admission_date', 'status', 'father_name', 'mother_name', 'dob', 'blood_group', 'gender', 'religion', 'category', 'phone', 'email', 'address', 'city', 'state', 'pincode']);
    fputcsv($output, ['Rahul Kumar', '10', 'A', '1', '2024-04-01', 'active', 'Ramesh Kumar', 'Sunita Devi', '2010-05-15', 'B+', 'male', 'Hindu', 'General', '9876543210', 'rahul@example.com', '123 Main Street', 'Delhi', 'Delhi', '110001']);
    fclose($output);
    exit;
}

$q = trim((string)get('q', ''));
$students = csv_read_all(DATA_PATH . '/students.csv');
if ($q !== '') {
    $needle = mb_strtolower($q);
    $students = array_values(array_filter($students, function ($s) use ($needle) {
        $hay = mb_strtolower(($s['id'] ?? '') . ' ' . ($s['name'] ?? '') . ' ' . ($s['class'] ?? '') . ' ' . ($s['roll_number'] ?? '') . ' ' . ($s['father_name'] ?? ''));
        return str_contains($hay, $needle);
    }));
}

$editId = (string)get('edit', '');
$editing = $editId ? csv_find_by_id(DATA_PATH . '/students.csv', $editId) : null;
$viewId = (string)get('view', '');
$viewing = $viewId ? csv_find_by_id(DATA_PATH . '/students.csv', $viewId) : null;

$title = 'Students';
$active = 'students';
$content = function () use ($canWrite, $students, $q, $editing, $viewing) {
?>
  <?php if ($viewing): ?>
    <!-- View Student Profile -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
          <h3 class="h5 mb-0">Student Profile</h3>
          <div class="d-flex gap-2">
            <?php if ($canWrite): ?>
              <a href="<?= e(base_url('students?edit=' . $viewing['id'])) ?>" class="btn btn-warning btn-sm">Edit</a>
            <?php endif; ?>
            <a href="<?= e(base_url('students')) ?>" class="btn btn-outline-light btn-sm">← Back</a>
          </div>
        </div>
        
        <div class="row g-4">
          <!-- Left Column - Photo & Basic -->
          <div class="col-md-3 text-center">
            <?php if (!empty($viewing['photo'])): ?>
              <img src="<?= e(base_url('uploads/' . $viewing['photo'])) ?>" alt="<?= e($viewing['name']) ?>" 
                   class="rounded-3 mb-3" style="width:180px;height:180px;object-fit:cover;border:4px solid rgba(255,255,255,0.1);">
            <?php else: ?>
              <div class="rounded-3 bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                   style="width:180px;height:180px;font-size:64px;color:white;">
                <?= strtoupper(substr($viewing['name'] ?? 'S', 0, 1)) ?>
              </div>
            <?php endif; ?>
            <h4 class="mb-1"><?= e($viewing['name']) ?></h4>
            <span class="badge bg-primary mb-2">Class <?= e($viewing['class']) ?><?= !empty($viewing['section']) ? '-' . e($viewing['section']) : '' ?></span>
            <p class="text-muted small mb-1">Roll No: <?= e($viewing['roll_number'] ?? 'N/A') ?></p>
            <span class="badge <?= ($viewing['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-secondary' ?>">
              <?= e(ucfirst($viewing['status'] ?? 'active')) ?>
            </span>
          </div>
          
          <!-- Right Column - Details -->
          <div class="col-md-9">
            <div class="row g-3">
              <!-- Personal Information -->
              <div class="col-md-6">
                <div class="p-3 rounded h-100" style="background:rgba(255,255,255,0.03);">
                  <h6 class="text-muted small mb-3"><i class="bi bi-person me-2"></i>Personal Information</h6>
                  <div class="row">
                    <div class="col-6 mb-2"><small class="text-muted">Father's Name</small><p class="mb-0 fw-semibold"><?= e($viewing['father_name'] ?? 'N/A') ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Mother's Name</small><p class="mb-0 fw-semibold"><?= e($viewing['mother_name'] ?? 'N/A') ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Date of Birth</small><p class="mb-0 fw-semibold"><?= e($viewing['dob'] ?? 'N/A') ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Blood Group</small><p class="mb-0 fw-semibold"><?= e($viewing['blood_group'] ?? 'N/A') ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Gender</small><p class="mb-0 fw-semibold"><?= e(ucfirst($viewing['gender'] ?? 'N/A')) ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Religion</small><p class="mb-0 fw-semibold"><?= e($viewing['religion'] ?? 'N/A') ?></p></div>
                  </div>
                </div>
              </div>
              
              <!-- Contact Information -->
              <div class="col-md-6">
                <div class="p-3 rounded h-100" style="background:rgba(255,255,255,0.03);">
                  <h6 class="text-muted small mb-3"><i class="bi bi-telephone me-2"></i>Contact Information</h6>
                  <div class="row">
                    <div class="col-6 mb-2"><small class="text-muted">Phone</small><p class="mb-0 fw-semibold"><?= e($viewing['phone'] ?? 'N/A') ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Alt. Phone</small><p class="mb-0 fw-semibold"><?= e($viewing['alt_phone'] ?? 'N/A') ?></p></div>
                    <div class="col-12 mb-2"><small class="text-muted">Email</small><p class="mb-0 fw-semibold"><?= e($viewing['email'] ?? 'N/A') ?></p></div>
                    <div class="col-12"><small class="text-muted">Address</small><p class="mb-0 fw-semibold"><?= e($viewing['address'] ?? '') ?><?= !empty($viewing['city']) ? ', ' . e($viewing['city']) : '' ?><?= !empty($viewing['state']) ? ', ' . e($viewing['state']) : '' ?> <?= e($viewing['pincode'] ?? '') ?></p></div>
                  </div>
                </div>
              </div>
              
              <!-- Academic Information -->
              <div class="col-md-6">
                <div class="p-3 rounded h-100" style="background:rgba(255,255,255,0.03);">
                  <h6 class="text-muted small mb-3"><i class="bi bi-mortarboard me-2"></i>Academic Information</h6>
                  <div class="row">
                    <div class="col-6 mb-2"><small class="text-muted">Admission Date</small><p class="mb-0 fw-semibold"><?= e($viewing['admission_date'] ?? 'N/A') ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Category</small><p class="mb-0 fw-semibold"><?= e($viewing['category'] ?? 'N/A') ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Previous School</small><p class="mb-0 fw-semibold"><?= e($viewing['previous_school'] ?? 'N/A') ?></p></div>
                    <div class="col-6 mb-2"><small class="text-muted">Previous Class</small><p class="mb-0 fw-semibold"><?= e($viewing['previous_class'] ?? 'N/A') ?></p></div>
                  </div>
                </div>
              </div>
              
              <!-- Documents & Medical -->
              <div class="col-md-6">
                <div class="p-3 rounded h-100" style="background:rgba(255,255,255,0.03);">
                  <h6 class="text-muted small mb-3"><i class="bi bi-file-earmark me-2"></i>Documents & Medical</h6>
                  <div class="row">
                    <div class="col-12 mb-2"><small class="text-muted">Aadhar Number</small><p class="mb-0 fw-semibold"><?= e($viewing['aadhar_number'] ?? 'N/A') ?></p></div>
                    <div class="col-12 mb-2"><small class="text-muted">Emergency Contact</small><p class="mb-0 fw-semibold"><?= e($viewing['emergency_contact'] ?? 'N/A') ?></p></div>
                    <div class="col-12"><small class="text-muted">Medical Conditions</small><p class="mb-0 fw-semibold"><?= e($viewing['medical_conditions'] ?? 'None') ?></p></div>
                  </div>
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
                <h3 class="h6 mb-0"><?= $editing ? 'Edit Student' : 'Add New Student' ?></h3>
                <a href="<?= e(base_url('students')) ?>" class="btn btn-outline-light btn-sm"><?= $editing ? 'Cancel' : 'View All Students' ?></a>
              </div>
              
              <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
                <?php if ($editing): ?>
                  <input type="hidden" name="id" value="<?= e($editing['id']) ?>">
                <?php endif; ?>
                
                <div class="row g-3">
                  <!-- Basic Info Section -->
                  <div class="col-12">
                    <h6 class="text-primary mb-2"><i class="bi bi-info-circle me-1"></i> Basic Information</h6>
                  </div>
                  
                  <div class="col-md-3">
                    <label class="form-label">Full Name *</label>
                    <input class="form-control" name="name" required maxlength="120" value="<?= e($editing['name'] ?? '') ?>">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Class *</label>
                    <input class="form-control" name="class" required maxlength="20" value="<?= e($editing['class'] ?? '') ?>" placeholder="e.g., 10">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Section</label>
                    <input class="form-control" name="section" maxlength="10" value="<?= e($editing['section'] ?? '') ?>" placeholder="e.g., A">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Roll Number</label>
                    <input class="form-control" name="roll_number" maxlength="20" value="<?= e($editing['roll_number'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Admission Date *</label>
                    <input class="form-control" type="date" name="admission_date" required value="<?= e($editing['admission_date'] ?? date('Y-m-d')) ?>">
                  </div>
                  
                  <!-- Personal Info Section (Admin Only) -->
                  <div class="col-12 mt-4">
                    <h6 class="text-primary mb-2"><i class="bi bi-person me-1"></i> Personal Information <small class="text-warning">(Admin Only)</small></h6>
                  </div>
                  
                  <div class="col-md-3">
                    <label class="form-label">Father's Name</label>
                    <input class="form-control" name="father_name" value="<?= e($editing['father_name'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Mother's Name</label>
                    <input class="form-control" name="mother_name" value="<?= e($editing['mother_name'] ?? '') ?>">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Date of Birth</label>
                    <input class="form-control" type="date" name="dob" value="<?= e($editing['dob'] ?? '') ?>">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Blood Group</label>
                    <select class="form-select" name="blood_group">
                      <option value="">Select</option>
                      <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                        <option value="<?= $bg ?>" <?= ($editing['blood_group'] ?? '') === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender">
                      <option value="male" <?= ($editing['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                      <option value="female" <?= ($editing['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                      <option value="other" <?= ($editing['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                  </div>
                  
                  <div class="col-md-3">
                    <label class="form-label">Religion</label>
                    <input class="form-control" name="religion" value="<?= e($editing['religion'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category">
                      <option value="">Select</option>
                      <?php foreach (['General', 'OBC', 'SC', 'ST', 'EWS'] as $cat): ?>
                        <option value="<?= $cat ?>" <?= ($editing['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Photo</label>
                    <input class="form-control" type="file" name="photo" accept="image/*">
                    <?php if ($editing && !empty($editing['photo'])): ?>
                      <small class="text-muted">Current: <?= e($editing['photo']) ?></small>
                    <?php endif; ?>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                      <option value="active" <?= ($editing['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                      <option value="inactive" <?= ($editing['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                  </div>
                  
                  <!-- Contact Section -->
                  <div class="col-12 mt-4">
                    <h6 class="text-primary mb-2"><i class="bi bi-telephone me-1"></i> Contact Information</h6>
                  </div>
                  
                  <div class="col-md-3">
                    <label class="form-label">Phone</label>
                    <input class="form-control" name="phone" maxlength="15" value="<?= e($editing['phone'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Alternate Phone</label>
                    <input class="form-control" name="alt_phone" maxlength="15" value="<?= e($editing['alt_phone'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" value="<?= e($editing['email'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Pincode</label>
                    <input class="form-control" name="pincode" maxlength="10" value="<?= e($editing['pincode'] ?? '') ?>">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input class="form-control" name="address" value="<?= e($editing['address'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">City</label>
                    <input class="form-control" name="city" value="<?= e($editing['city'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">State</label>
                    <input class="form-control" name="state" value="<?= e($editing['state'] ?? '') ?>">
                  </div>
                  
                  <!-- Documents Section -->
                  <div class="col-12 mt-4">
                    <h6 class="text-primary mb-2"><i class="bi bi-file-earmark me-1"></i> Documents & Previous School</h6>
                  </div>
                  
                  <div class="col-md-3">
                    <label class="form-label">Aadhar Number</label>
                    <input class="form-control" name="aadhar_number" maxlength="14" value="<?= e($editing['aadhar_number'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Previous School</label>
                    <input class="form-control" name="previous_school" value="<?= e($editing['previous_school'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Previous Class</label>
                    <input class="form-control" name="previous_class" value="<?= e($editing['previous_class'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Emergency Contact</label>
                    <input class="form-control" name="emergency_contact" value="<?= e($editing['emergency_contact'] ?? '') ?>">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label">Medical Conditions</label>
                    <textarea class="form-control" name="medical_conditions" rows="2" placeholder="Any allergies, medical conditions, etc."><?= e($editing['medical_conditions'] ?? '') ?></textarea>
                  </div>
                  
                  <div class="col-12 mt-3">
                    <button class="btn btn-primary" type="submit"><?= $editing ? 'Update Student' : 'Add Student' ?></button>
                    <?php if ($editing): ?>
                      <a class="btn btn-outline-light ms-2" href="<?= e(base_url('students')) ?>">Cancel</a>
                    <?php endif; ?>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
  <?php else: ?>
    <!-- Student List View -->
    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
          <h3 class="h6 mb-0">Student Records <span class="badge bg-primary"><?= count($students) ?></span></h3>
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
                <li><a class="dropdown-item" href="<?= e(base_url('students?export=csv')) ?>"><i class="bi bi-download me-2"></i>Export All (CSV)</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Import</h6></li>
                <li><a class="dropdown-item" href="<?= e(base_url('students?template=csv')) ?>"><i class="bi bi-file-earmark-arrow-down me-2"></i>Download Template</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bi bi-upload me-2"></i>Import CSV</a></li>
              </ul>
            </div>
            <a href="<?= e(base_url('students?action=add')) ?>" class="btn btn-sm btn-primary">+ Add</a>
            <?php endif; ?>
          </div>
        </div>

        <div class="row g-3">
          <?php if (!$students): ?>
            <div class="col-12"><p class="text-muted">No students found.</p></div>
          <?php else: ?>
            <?php foreach ($students as $s): ?>
              <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);">
                  <div class="card-body text-center p-3">
                    <?php if (!empty($s['photo'])): ?>
                      <img src="<?= e(base_url('uploads/' . $s['photo'])) ?>" alt="" 
                           class="rounded-circle mb-2" style="width:60px;height:60px;object-fit:cover;">
                    <?php else: ?>
                      <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-2" 
                           style="width:60px;height:60px;font-size:20px;color:white;">
                        <?= strtoupper(substr($s['name'] ?? 'S', 0, 1)) ?>
                      </div>
                    <?php endif; ?>
                    <h6 class="mb-1" style="font-size:0.9rem;"><?= e($s['name']) ?></h6>
                    <small class="text-muted d-block mb-1">Class <?= e($s['class']) ?><?= !empty($s['section']) ? '-' . e($s['section']) : '' ?></small>
                    <small class="text-muted d-block mb-2">Roll: <?= e($s['roll_number'] ?? 'N/A') ?></small>
                    <span class="badge <?= ($s['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-secondary' ?> mb-2" style="font-size:0.7rem;">
                      <?= e(ucfirst($s['status'] ?? 'active')) ?>
                    </span>
                    <div class="d-flex gap-1 justify-content-center mt-2">
                      <a href="<?= e(base_url('students?view=' . $s['id'])) ?>" class="btn btn-sm btn-outline-info" style="font-size:0.75rem;">View</a>
                      <?php if ($canWrite): ?>
                        <a href="<?= e(base_url('students?edit=' . $s['id'])) ?>" class="btn btn-sm btn-outline-warning" style="font-size:0.75rem;">Edit</a>
                        <form method="post" class="d-inline" onsubmit="return confirm('Delete?');">
                          <?= csrf_field() ?>
                          <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="id" value="<?= e($s['id']) ?>">
                          <button class="btn btn-sm btn-outline-danger" type="submit" style="font-size:0.75rem;">Del</button>
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
  <?php endif; ?>
  
  <!-- Import Modal -->
  <?php if ($canWrite): ?>
  <div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Import Students</h5>
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
                <li>Download the <a href="<?= e(base_url('students?template=csv')) ?>">CSV template</a> first</li>
                <li>Fill in student data (name & class are required)</li>
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
