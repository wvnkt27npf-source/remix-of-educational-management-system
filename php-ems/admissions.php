<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('*');

// Handle actions
if (request_method() === 'POST') {
    csrf_verify_or_die();
    $action = (string)post('action', '');
    $id = (string)post('id', '');

    if ($action === 'update_status' && $id !== '') {
        $status = (string)post('status', 'pending');
        csv_update_by_id(DATA_PATH . '/admissions.csv', $id, ['status' => $status]);
        flash_set('success', 'Status updated successfully.');
        redirect('admissions');
    }

    if ($action === 'delete' && $id !== '') {
        csv_delete_by_id(DATA_PATH . '/admissions.csv', $id);
        flash_set('info', 'Inquiry deleted.');
        redirect('admissions');
    }

    // Convert admission to student
    if ($action === 'convert_to_student' && $id !== '') {
        $admission = csv_find_by_id(DATA_PATH . '/admissions.csv', $id);
        if ($admission) {
            // Initialize students.csv if needed
            csv_init(DATA_PATH . '/students.csv', ['id','name','class','section','roll_number','admission_date','status','father_name','mother_name','dob','blood_group','gender','religion','category','phone','alt_phone','email','address','city','state','pincode','photo','aadhar_number','birth_certificate','transfer_certificate','previous_school','previous_class','emergency_contact','medical_conditions']);
            
            // Map admission data to student fields
            $studentData = [
                'name' => $admission['student_name'] ?? '',
                'class' => $admission['class_applying'] ?? '',
                'section' => '',
                'roll_number' => '',
                'admission_date' => date('Y-m-d'),
                'status' => 'active',
                'father_name' => $admission['parent_name'] ?? '',
                'mother_name' => '',
                'dob' => $admission['dob'] ?? '',
                'blood_group' => '',
                'gender' => $admission['gender'] ?? '',
                'religion' => '',
                'category' => '',
                'phone' => $admission['phone'] ?? '',
                'alt_phone' => '',
                'email' => $admission['email'] ?? '',
                'address' => $admission['address'] ?? '',
                'city' => '',
                'state' => '',
                'pincode' => '',
                'photo' => '',
                'aadhar_number' => '',
                'birth_certificate' => '',
                'transfer_certificate' => '',
                'previous_school' => $admission['previous_school'] ?? '',
                'previous_class' => '',
                'emergency_contact' => $admission['phone'] ?? '',
                'medical_conditions' => ''
            ];
            
            csv_insert(DATA_PATH . '/students.csv', $studentData);
            
            // Update admission status to converted
            csv_update_by_id(DATA_PATH . '/admissions.csv', $id, ['status' => 'converted']);
            
            flash_set('success', 'Admission converted to student successfully! You can now edit additional details in Students section.');
            redirect('admissions');
        } else {
            flash_set('danger', 'Admission record not found.');
            redirect('admissions');
        }
    }
}

// Initialize CSV if not exists
csv_init(DATA_PATH . '/admissions.csv', ['id', 'student_name', 'parent_name', 'email', 'phone', 'dob', 'gender', 'class_applying', 'previous_school', 'address', 'message', 'status', 'created_at']);

$allAdmissions = csv_read_all(DATA_PATH . '/admissions.csv');
usort($allAdmissions, fn($a, $b) => (int)$b['id'] <=> (int)$a['id']); // Latest first

// Calculate stats
$stats = [
    'total' => count($allAdmissions),
    'pending' => count(array_filter($allAdmissions, fn($a) => ($a['status'] ?? 'pending') === 'pending')),
    'reviewed' => count(array_filter($allAdmissions, fn($a) => ($a['status'] ?? '') === 'reviewed')),
    'approved' => count(array_filter($allAdmissions, fn($a) => ($a['status'] ?? '') === 'approved')),
    'rejected' => count(array_filter($allAdmissions, fn($a) => ($a['status'] ?? '') === 'rejected')),
    'converted' => count(array_filter($allAdmissions, fn($a) => ($a['status'] ?? '') === 'converted')),
];

// Filter by status
$filter = get('status', '');
$admissions = $allAdmissions;
if ($filter !== '') {
    $admissions = array_filter($admissions, fn($a) => ($a['status'] ?? 'pending') === $filter);
}

$title = 'Admission Inquiries';
$active = 'admissions';
$content = function () use ($admissions, $filter, $stats) {
?>
<style>
  .admission-page {
    animation: fadeInUp 0.5s ease-out;
  }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .stats-card {
    background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.95));
    backdrop-filter: blur(20px);
    border: 1px solid rgba(148,163,184,0.1);
    border-radius: 16px;
    padding: 1.25rem;
    text-align: center;
    transition: all 0.3s ease;
  }
  .stats-card:hover {
    transform: translateY(-3px);
    border-color: rgba(148,163,184,0.25);
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
  }
  .stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    font-size: 1.5rem;
  }
  .stats-icon.total { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
  .stats-icon.pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
  .stats-icon.reviewed { background: linear-gradient(135deg, #06b6d4, #0891b2); }
  .stats-icon.approved { background: linear-gradient(135deg, #10b981, #059669); }
  .stats-icon.rejected { background: linear-gradient(135deg, #ef4444, #dc2626); }
  .stats-icon.converted { background: linear-gradient(135deg, #8b5cf6, #a855f7); }
  
  .stats-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #f1f5f9;
    margin-bottom: 0.25rem;
  }
  .stats-label {
    font-size: 0.8rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .filter-section {
    background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.95));
    backdrop-filter: blur(20px);
    border: 1px solid rgba(148,163,184,0.1);
    border-radius: 16px;
    padding: 1.25rem;
  }
  
  .filter-btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }
  .filter-btn.active-all {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    box-shadow: 0 4px 15px rgba(99,102,241,0.4);
  }
  .filter-btn.active-pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    box-shadow: 0 4px 15px rgba(245,158,11,0.4);
  }
  .filter-btn.active-reviewed {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: white;
    box-shadow: 0 4px 15px rgba(6,182,212,0.4);
  }
  .filter-btn.active-approved {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 15px rgba(16,185,129,0.4);
  }
  .filter-btn.active-rejected {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    box-shadow: 0 4px 15px rgba(239,68,68,0.4);
  }
  .filter-btn.active-converted {
    background: linear-gradient(135deg, #8b5cf6, #a855f7);
    color: white;
    box-shadow: 0 4px 15px rgba(139,92,246,0.4);
  }
  .filter-btn:not([class*="active-"]) {
    background: rgba(51,65,85,0.5);
    color: #94a3b8;
    border-color: rgba(148,163,184,0.2);
  }
  .filter-btn:not([class*="active-"]):hover {
    background: rgba(71,85,105,0.6);
    color: #e2e8f0;
  }
  
  .admission-card {
    background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.95));
    backdrop-filter: blur(20px);
    border: 1px solid rgba(148,163,184,0.1);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  .admission-card:hover {
    transform: translateY(-5px);
    border-color: rgba(148,163,184,0.25);
    box-shadow: 0 15px 50px rgba(0,0,0,0.3);
  }
  
  .admission-header {
    padding: 1.25rem;
    border-bottom: 1px solid rgba(148,163,184,0.1);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
  }
  .student-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 600;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
  }
  .avatar-male { background: linear-gradient(135deg, #3b82f6, #2563eb); }
  .avatar-female { background: linear-gradient(135deg, #ec4899, #db2777); }
  .avatar-other { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  
  .student-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #f1f5f9;
    margin-bottom: 0.25rem;
  }
  .parent-info {
    font-size: 0.85rem;
    color: #94a3b8;
  }
  .parent-info i { color: #64748b; }
  
  .status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .status-pending { background: rgba(245,158,11,0.2); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
  .status-reviewed { background: rgba(6,182,212,0.2); color: #22d3ee; border: 1px solid rgba(6,182,212,0.3); }
  .status-approved { background: rgba(16,185,129,0.2); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
  .status-rejected { background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
  .status-converted { background: rgba(139,92,246,0.2); color: #a78bfa; border: 1px solid rgba(139,92,246,0.3); }
  
  .convert-btn {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    color: white;
    border-radius: 8px;
    padding: 0.4rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
  }
  .convert-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(16,185,129,0.4);
    color: white;
  }
  .convert-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }
  
  .admission-body {
    padding: 1.25rem;
  }
  .info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
  }
  .info-item {
    background: rgba(51,65,85,0.3);
    border-radius: 10px;
    padding: 0.75rem;
  }
  .info-label {
    font-size: 0.7rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
  }
  .info-value {
    font-size: 0.9rem;
    color: #e2e8f0;
    font-weight: 500;
  }
  .info-value a {
    color: #60a5fa;
    text-decoration: none;
    transition: color 0.2s;
  }
  .info-value a:hover { color: #93c5fd; }
  
  .extra-info {
    background: rgba(51,65,85,0.2);
    border-radius: 10px;
    padding: 0.75rem;
    margin-bottom: 1rem;
  }
  .extra-info .info-label {
    margin-bottom: 0.35rem;
  }
  .extra-info .info-value {
    font-size: 0.85rem;
    line-height: 1.5;
  }
  
  .admission-footer {
    padding: 1rem 1.25rem;
    border-top: 1px solid rgba(148,163,184,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(15,23,42,0.5);
  }
  .timestamp {
    font-size: 0.8rem;
    color: #64748b;
  }
  .timestamp i { margin-right: 0.35rem; }
  
  .action-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  .status-select {
    background: rgba(51,65,85,0.8) !important;
    border: 1px solid rgba(148,163,184,0.2) !important;
    color: #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 0.4rem 0.75rem !important;
    font-size: 0.85rem !important;
    cursor: pointer;
    transition: all 0.2s;
  }
  .status-select:hover {
    border-color: rgba(148,163,184,0.4) !important;
  }
  .status-select:focus {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2) !important;
    outline: none !important;
  }
  .status-select option {
    background: #1e293b;
    color: #e2e8f0;
  }
  
  .delete-btn {
    background: rgba(239,68,68,0.15);
    border: 1px solid rgba(239,68,68,0.3);
    color: #f87171;
    border-radius: 8px;
    padding: 0.4rem 0.6rem;
    transition: all 0.2s;
  }
  .delete-btn:hover {
    background: rgba(239,68,68,0.25);
    border-color: rgba(239,68,68,0.5);
    color: #fca5a5;
  }
  
  .empty-state {
    background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.95));
    backdrop-filter: blur(20px);
    border: 1px solid rgba(148,163,184,0.1);
    border-radius: 16px;
    padding: 4rem 2rem;
    text-align: center;
  }
  .empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.1));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.5rem;
    color: #818cf8;
  }
  .empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #f1f5f9;
    margin-bottom: 0.5rem;
  }
  .empty-text {
    color: #94a3b8;
    font-size: 0.95rem;
  }
  
  .page-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  .header-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 8px 25px rgba(99,102,241,0.35);
  }
  .header-content h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #f1f5f9;
    margin: 0;
  }
  .header-content p {
    color: #94a3b8;
    font-size: 0.9rem;
    margin: 0.25rem 0 0;
  }
</style>

<div class="admission-page">
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-icon">
      <i class="bi bi-person-plus-fill"></i>
    </div>
    <div class="header-content">
      <h1>Admission Inquiries</h1>
      <p>Manage and review all admission applications</p>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg">
      <div class="stats-card">
        <div class="stats-icon total">
          <i class="bi bi-people-fill"></i>
        </div>
        <div class="stats-value"><?= $stats['total'] ?></div>
        <div class="stats-label">Total</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
      <div class="stats-card">
        <div class="stats-icon pending">
          <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="stats-value"><?= $stats['pending'] ?></div>
        <div class="stats-label">Pending</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
      <div class="stats-card">
        <div class="stats-icon reviewed">
          <i class="bi bi-eye-fill"></i>
        </div>
        <div class="stats-value"><?= $stats['reviewed'] ?></div>
        <div class="stats-label">Reviewed</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
      <div class="stats-card">
        <div class="stats-icon approved">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stats-value"><?= $stats['approved'] ?></div>
        <div class="stats-label">Approved</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
      <div class="stats-card">
        <div class="stats-icon rejected">
          <i class="bi bi-x-circle-fill"></i>
        </div>
        <div class="stats-value"><?= $stats['rejected'] ?></div>
        <div class="stats-label">Rejected</div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
      <div class="stats-card">
        <div class="stats-icon converted">
          <i class="bi bi-person-check-fill"></i>
        </div>
        <div class="stats-value"><?= $stats['converted'] ?></div>
        <div class="stats-label">Converted</div>
      </div>
    </div>
  </div>

  <!-- Filter Section -->
  <div class="filter-section mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <span class="text-muted me-2"><i class="bi bi-funnel me-1"></i>Filter:</span>
      <a href="admissions" class="filter-btn <?= $filter === '' ? 'active-all' : '' ?>">
        <i class="bi bi-grid-fill me-1"></i>All
      </a>
      <a href="admissions?status=pending" class="filter-btn <?= $filter === 'pending' ? 'active-pending' : '' ?>">
        <i class="bi bi-hourglass-split me-1"></i>Pending
      </a>
      <a href="admissions?status=reviewed" class="filter-btn <?= $filter === 'reviewed' ? 'active-reviewed' : '' ?>">
        <i class="bi bi-eye me-1"></i>Reviewed
      </a>
      <a href="admissions?status=approved" class="filter-btn <?= $filter === 'approved' ? 'active-approved' : '' ?>">
        <i class="bi bi-check-circle me-1"></i>Approved
      </a>
      <a href="admissions?status=rejected" class="filter-btn <?= $filter === 'rejected' ? 'active-rejected' : '' ?>">
        <i class="bi bi-x-circle me-1"></i>Rejected
      </a>
      <a href="admissions?status=converted" class="filter-btn <?= $filter === 'converted' ? 'active-converted' : '' ?>">
        <i class="bi bi-person-check me-1"></i>Converted
      </a>
    </div>
  </div>

  <?php if (empty($admissions)): ?>
    <div class="empty-state">
      <div class="empty-icon">
        <i class="bi bi-inbox"></i>
      </div>
      <div class="empty-title">No Inquiries Found</div>
      <div class="empty-text">
        <?php if ($filter !== ''): ?>
          No <?= e($filter) ?> admission inquiries at the moment.
        <?php else: ?>
          No admission inquiries have been received yet.
        <?php endif; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($admissions as $index => $a): 
        $status = $a['status'] ?? 'pending';
        $gender = strtolower($a['gender'] ?? '');
        $avatarClass = $gender === 'male' ? 'avatar-male' : ($gender === 'female' ? 'avatar-female' : 'avatar-other');
        $initials = strtoupper(substr($a['student_name'] ?? 'S', 0, 1));
      ?>
        <div class="col-12 col-xl-6" style="animation-delay: <?= $index * 0.05 ?>s">
          <div class="admission-card">
            <!-- Header -->
            <div class="admission-header">
              <div class="d-flex align-items-center">
                <div class="student-avatar <?= $avatarClass ?>">
                  <?= $initials ?>
                </div>
                <div>
                  <div class="student-name"><?= e($a['student_name']) ?></div>
                  <div class="parent-info">
                    <i class="bi bi-person me-1"></i><?= e($a['parent_name']) ?>
                  </div>
                </div>
              </div>
              <span class="status-badge status-<?= $status ?>">
                <?= ucfirst(e($status)) ?>
              </span>
            </div>
            
            <!-- Body -->
            <div class="admission-body">
              <div class="info-grid">
                <div class="info-item">
                  <div class="info-label">Class Applying</div>
                  <div class="info-value"><?= e($a['class_applying'] ?: 'N/A') ?></div>
                </div>
                <div class="info-item">
                  <div class="info-label">Gender</div>
                  <div class="info-value"><?= e(ucfirst($a['gender'] ?: 'N/A')) ?></div>
                </div>
                <div class="info-item">
                  <div class="info-label">Phone</div>
                  <div class="info-value">
                    <a href="tel:<?= e($a['phone']) ?>"><i class="bi bi-telephone me-1"></i><?= e($a['phone']) ?></a>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-label">Email</div>
                  <div class="info-value">
                    <a href="mailto:<?= e($a['email']) ?>"><i class="bi bi-envelope me-1"></i><?= e($a['email']) ?></a>
                  </div>
                </div>
                <?php if (!empty($a['dob'])): ?>
                <div class="info-item">
                  <div class="info-label">Date of Birth</div>
                  <div class="info-value"><i class="bi bi-calendar3 me-1"></i><?= e($a['dob']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($a['previous_school'])): ?>
                <div class="info-item">
                  <div class="info-label">Previous School</div>
                  <div class="info-value"><?= e($a['previous_school']) ?></div>
                </div>
                <?php endif; ?>
              </div>
              
              <?php if (!empty($a['address'])): ?>
              <div class="extra-info">
                <div class="info-label"><i class="bi bi-geo-alt me-1"></i>Address</div>
                <div class="info-value"><?= e($a['address']) ?></div>
              </div>
              <?php endif; ?>
              
              <?php if (!empty($a['message'])): ?>
              <div class="extra-info">
                <div class="info-label"><i class="bi bi-chat-text me-1"></i>Message</div>
                <div class="info-value"><?= e($a['message']) ?></div>
              </div>
              <?php endif; ?>
            </div>
            
            <!-- Footer -->
            <div class="admission-footer">
              <div class="timestamp">
                <i class="bi bi-clock"></i><?= e($a['created_at'] ?? 'N/A') ?>
              </div>
              <div class="action-group">
                <form method="post" class="d-inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="id" value="<?= e($a['id']) ?>">
                  <select name="status" class="status-select" onchange="this.form.submit()">
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                    <option value="reviewed" <?= $status === 'reviewed' ? 'selected' : '' ?>>👁 Reviewed</option>
                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>✅ Approved</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>❌ Rejected</option>
                    <option value="converted" <?= $status === 'converted' ? 'selected' : '' ?>>🎓 Converted</option>
                  </select>
                </form>
                <?php if ($status !== 'converted'): ?>
                <form method="post" class="d-inline" onsubmit="return confirm('Convert this admission to a student record?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="convert_to_student">
                  <input type="hidden" name="id" value="<?= e($a['id']) ?>">
                  <button class="convert-btn" type="submit" title="Add to Students">
                    <i class="bi bi-person-plus-fill"></i>Add to Student
                  </button>
                </form>
                <?php endif; ?>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this inquiry permanently?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= e($a['id']) ?>">
                  <button class="delete-btn" type="submit" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php
};

include __DIR__ . '/views/partials/layout.php';
