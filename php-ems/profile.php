<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('profile.read');

$u = current_user();
$student = student_record_for_current_user();
$teacher = teacher_record_for_current_user();
$isAdmin = has_permission('*');

// Enhanced student lookup (same as my_exams.php)
if (!$student && $u && strtolower((string)($u['role'] ?? '')) === 'student') {
    $students = csv_read_all(DATA_PATH . '/students.csv');
    $username = strtolower(trim((string)($u['username'] ?? '')));
    $linkedId = strtolower(trim((string)($u['linked_id'] ?? '')));
    
    foreach ($students as $s) {
        $sId = strtolower(trim((string)($s['id'] ?? '')));
        $sName = strtolower(trim((string)($s['name'] ?? '')));
        $sFullName = strtolower(trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')));
        
        if ($linkedId !== '' && ($sId === $linkedId || $sName === $linkedId || $sFullName === $linkedId)) {
            $student = $s;
            break;
        }
        if ($sId === $username || strpos($username, $sId) !== false) {
            $student = $s;
            break;
        }
    }
}

// Enhanced teacher lookup
if (!$teacher && $u && strtolower((string)($u['role'] ?? '')) === 'teacher') {
    $teachers = csv_read_all(DATA_PATH . '/teachers.csv');
    $username = strtolower(trim((string)($u['username'] ?? '')));
    $linkedId = strtolower(trim((string)($u['linked_id'] ?? '')));
    
    foreach ($teachers as $t) {
        $tId = strtolower(trim((string)($t['id'] ?? '')));
        $tName = strtolower(trim((string)($t['name'] ?? '')));
        $tFullName = strtolower(trim(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? '')));
        $tEmployeeId = strtolower(trim((string)($t['employee_id'] ?? '')));
        $tEmail = strtolower(trim((string)($t['email'] ?? '')));
        
        // Match linked_id by teacher id OR name
        if ($linkedId !== '' && ($tId === $linkedId || $tName === $linkedId || $tFullName === $linkedId)) {
            $teacher = $t;
            break;
        }
        // Match by employee_id
        if ($tEmployeeId !== '' && $tEmployeeId === $username) {
            $teacher = $t;
            break;
        }
        // Match by email
        if ($tEmail !== '' && $tEmail === $username) {
            $teacher = $t;
            break;
        }
        // Match by id
        if ($tId === $username) {
            $teacher = $t;
            break;
        }
        // Match username containing teacher id
        if ($tId !== '' && strpos($username, $tId) !== false) {
            $teacher = $t;
            break;
        }
    }
}

// If still no teacher found but user is teacher role, show teacher interface anyway
$showTeacherInterface = ($teacher !== null) || (strtolower((string)($u['role'] ?? '')) === 'teacher');

// Handle student editable fields update
if (request_method() === 'POST' && $student && !$isAdmin) {
    csrf_verify_or_die();
    
    $emergency_contact = trim((string)post('emergency_contact', ''));
    $medical_conditions = trim((string)post('medical_conditions', ''));
    
    csv_update_by_id(DATA_PATH . '/students.csv', $student['id'], [
        'emergency_contact' => $emergency_contact,
        'medical_conditions' => $medical_conditions,
    ]);
    
    flash_set('success', 'Profile updated successfully.');
    redirect('profile');
}

$title = 'Profile';
$active = 'profile';
$content = function () use ($u, $student, $teacher, $isAdmin, $showTeacherInterface) {
?>
<style>
/* Profile Hero */
.profile-hero {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.2) 0%, rgba(147, 51, 234, 0.15) 50%, rgba(236, 72, 153, 0.1) 100%);
    border-radius: 24px;
    padding: 3rem 2rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}
.profile-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.profile-avatar {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.2);
    box-shadow: 0 20px 60px -15px rgba(79, 70, 229, 0.5);
    position: relative;
    z-index: 2;
}
.profile-avatar-placeholder {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5 0%, #9333ea 50%, #ec4899 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5rem;
    color: white;
    font-weight: 700;
    border: 5px solid rgba(255,255,255,0.2);
    box-shadow: 0 20px 60px -15px rgba(79, 70, 229, 0.5);
    position: relative;
    z-index: 2;
}
.profile-name {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    position: relative;
    z-index: 2;
}
.profile-role {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
    position: relative;
    z-index: 2;
}
.profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-top: 1.5rem;
    position: relative;
    z-index: 2;
}
.profile-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #94a3b8;
    font-size: 0.9rem;
}
.profile-meta-item i {
    color: #4f46e5;
}

/* Info Cards */
.info-card {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}
.info-card:hover {
    transform: translateY(-4px);
    border-color: rgba(79, 70, 229, 0.3);
    box-shadow: 0 20px 40px -15px rgba(79, 70, 229, 0.2);
}
.info-card-header {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.15) 0%, rgba(147, 51, 234, 0.1) 100%);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.info-card-header i {
    font-size: 1.25rem;
    color: #4f46e5;
}
.info-card-header h4 {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0;
}
.info-card-body {
    padding: 1.25rem;
}
.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.info-item:first-child {
    padding-top: 0;
}
.info-label {
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
}
.info-value {
    color: #e2e8f0;
    font-size: 0.9rem;
    font-weight: 600;
    text-align: right;
    max-width: 60%;
}

/* Editable Section */
.editable-card {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(6, 182, 212, 0.05) 100%);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 16px;
    overflow: hidden;
}
.editable-card-header {
    background: rgba(16, 185, 129, 0.1);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(16, 185, 129, 0.15);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.editable-card-header i {
    font-size: 1.25rem;
    color: #10b981;
}
.editable-card-header h4 {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0;
    color: #10b981;
}
.editable-card-body {
    padding: 1.25rem;
}

/* Stats Row */
.stats-row {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 2rem;
}
.stat-pill {
    flex: 1;
    min-width: 120px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    text-align: center;
    transition: all 0.3s ease;
}
.stat-pill:hover {
    transform: translateY(-2px);
    border-color: rgba(79, 70, 229, 0.3);
}
.stat-pill-value {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #4f46e5, #9333ea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.stat-pill-label {
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1.5rem;
    position: relative;
    z-index: 2;
}
.quick-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.25rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #e2e8f0;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}
.quick-action-btn:hover {
    background: rgba(79, 70, 229, 0.2);
    border-color: rgba(79, 70, 229, 0.3);
    color: white;
    transform: translateY(-2px);
}
.quick-action-btn i {
    font-size: 1rem;
}

/* Admin Badge */
.admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    border-radius: 12px;
    color: white;
    font-weight: 600;
}
</style>

<?php 
$photo = '';
$name = $u['username'] ?? 'User';
$email = '';
$phone = '';

if ($student) {
    if (!empty($student['photo'])) $photo = $student['photo'];
    $name = $student['name'] ?? trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
    $email = $student['email'] ?? '';
    $phone = $student['phone'] ?? '';
} elseif ($teacher) {
    if (!empty($teacher['photo'])) $photo = $teacher['photo'];
    $name = $teacher['name'] ?? trim(($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? ''));
    $email = $teacher['email'] ?? '';
    $phone = $teacher['phone'] ?? '';
} elseif ($showTeacherInterface) {
    // Teacher role but no linked record - use username
    $name = $u['username'] ?? 'Teacher';
}
if (!$name) $name = $u['username'] ?? 'User';
?>

<!-- Profile Hero Section -->
<div class="profile-hero">
    <div class="row align-items-center">
        <div class="col-auto">
            <?php if ($photo): ?>
                <img src="<?= e(base_url('uploads/' . $photo)) ?>" alt="Profile" class="profile-avatar" style="object-fit:cover;">
            <?php else: ?>
                <div class="profile-avatar-placeholder">
                    <?= strtoupper(substr($name, 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col">
            <h1 class="profile-name"><?= e($name) ?></h1>
            <div class="profile-role">
                <?php if ($student): ?>
                    <i class="bi bi-mortarboard-fill"></i> Student
                <?php elseif ($teacher || $showTeacherInterface): ?>
                    <i class="bi bi-person-workspace"></i> Teacher
                <?php elseif ($isAdmin): ?>
                    <i class="bi bi-shield-fill-check"></i> Administrator
                <?php else: ?>
                    <i class="bi bi-person"></i> <?= e(ucfirst($u['role'] ?? 'User')) ?>
                <?php endif; ?>
            </div>
            
            <div class="profile-meta">
                <div class="profile-meta-item">
                    <i class="bi bi-person-badge"></i>
                    <span>@<?= e($u['username'] ?? '') ?></span>
                </div>
                <?php if ($email): ?>
                <div class="profile-meta-item">
                    <i class="bi bi-envelope"></i>
                    <span><?= e($email) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($phone): ?>
                <div class="profile-meta-item">
                    <i class="bi bi-telephone"></i>
                    <span><?= e($phone) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="quick-actions">
                <a href="<?= e(base_url('change_password')) ?>" class="quick-action-btn">
                    <i class="bi bi-key"></i> Change Password
                </a>
                <?php if ($student): ?>
                <a href="<?= e(base_url('my_exams')) ?>" class="quick-action-btn">
                    <i class="bi bi-journal-text"></i> My Exams
                </a>
                <?php endif; ?>
                <?php if ($teacher || $showTeacherInterface): ?>
                <a href="<?= e(base_url('exams')) ?>" class="quick-action-btn">
                    <i class="bi bi-journal-text"></i> Manage Exams
                </a>
                <a href="<?= e(base_url('students')) ?>" class="quick-action-btn">
                    <i class="bi bi-people"></i> View Students
                </a>
                <?php endif; ?>
                <?php if ($isAdmin): ?>
                <a href="<?= e(base_url('users')) ?>" class="quick-action-btn">
                    <i class="bi bi-people"></i> Manage Users
                </a>
                <a href="<?= e(base_url('site_settings')) ?>" class="quick-action-btn">
                    <i class="bi bi-gear"></i> Site Settings
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($student): ?>
<!-- Student Stats -->
<div class="stats-row">
    <div class="stat-pill">
        <div class="stat-pill-value"><?= e($student['class'] ?? '-') ?></div>
        <div class="stat-pill-label">Class</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-value"><?= e($student['section'] ?? '-') ?></div>
        <div class="stat-pill-label">Section</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-value"><?= e($student['roll_number'] ?? '-') ?></div>
        <div class="stat-pill-label">Roll No.</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-value"><?= e($student['admission_date'] ? date('Y', strtotime($student['admission_date'])) : '-') ?></div>
        <div class="stat-pill-label">Batch</div>
    </div>
</div>

<div class="row g-4">
    <!-- Academic Information -->
    <div class="col-md-6 col-lg-4">
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-book"></i>
                <h4>Academic Details</h4>
            </div>
            <div class="info-card-body">
                <div class="info-item">
                    <span class="info-label">Student ID</span>
                    <span class="info-value"><?= e($student['id']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Admission No.</span>
                    <span class="info-value"><?= e($student['admission_no'] ?? $student['id']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Admission Date</span>
                    <span class="info-value"><?= e($student['admission_date'] ? date('d M Y', strtotime($student['admission_date'])) : 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span class="badge bg-success"><?= e(ucfirst($student['status'] ?? 'Active')) ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Personal Information -->
    <div class="col-md-6 col-lg-4">
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-person"></i>
                <h4>Personal Details</h4>
            </div>
            <div class="info-card-body">
                <div class="info-item">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value"><?= e($student['dob'] ? date('d M Y', strtotime($student['dob'])) : 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?= e(ucfirst($student['gender'] ?? 'N/A')) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Blood Group</span>
                    <span class="info-value"><?= e($student['blood_group'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Religion</span>
                    <span class="info-value"><?= e($student['religion'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Family Information -->
    <div class="col-md-6 col-lg-4">
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-people"></i>
                <h4>Family Details</h4>
            </div>
            <div class="info-card-body">
                <div class="info-item">
                    <span class="info-label">Father's Name</span>
                    <span class="info-value"><?= e($student['father_name'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mother's Name</span>
                    <span class="info-value"><?= e($student['mother_name'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Guardian Phone</span>
                    <span class="info-value"><?= e($student['guardian_phone'] ?? $student['phone'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contact Information -->
    <div class="col-md-6 col-lg-4">
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-geo-alt"></i>
                <h4>Contact Details</h4>
            </div>
            <div class="info-card-body">
                <div class="info-item">
                    <span class="info-label">Address</span>
                    <span class="info-value"><?= e($student['address'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">City</span>
                    <span class="info-value"><?= e($student['city'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">State</span>
                    <span class="info-value"><?= e($student['state'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Editable Information -->
    <div class="col-md-6 col-lg-8">
        <div class="editable-card">
            <div class="editable-card-header">
                <i class="bi bi-pencil-square"></i>
                <h4>Update Your Information</h4>
            </div>
            <div class="editable-card-body">
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Emergency Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input class="form-control" name="emergency_contact" 
                                       value="<?= e($student['emergency_contact'] ?? '') ?>" 
                                       placeholder="+91 98765 43210">
                            </div>
                            <div class="form-text">Whom should we contact in case of emergency?</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Medical Conditions / Allergies</label>
                            <textarea class="form-control" name="medical_conditions" rows="3" 
                                      placeholder="Any allergies, medical conditions, or special requirements..."><?= e($student['medical_conditions'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-check2-circle me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php elseif ($teacher || $showTeacherInterface): ?>
<!-- Teacher Enhanced Profile -->
<style>
/* Teacher Specific Styles */
.teacher-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(6, 182, 212, 0.1) 100%);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 16px;
    margin-bottom: 2rem;
}
.teacher-hero-badge i {
    font-size: 2rem;
    color: #10b981;
}
.teacher-hero-badge-text h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #10b981;
}
.teacher-hero-badge-text p {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
}

.teacher-stat-card {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(147, 51, 234, 0.08) 100%);
    border: 1px solid rgba(79, 70, 229, 0.15);
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.teacher-stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, transparent 70%);
    transition: all 0.5s ease;
}
.teacher-stat-card:hover::before {
    transform: scale(1.5);
}
.teacher-stat-card:hover {
    transform: translateY(-5px);
    border-color: rgba(79, 70, 229, 0.3);
    box-shadow: 0 20px 40px -15px rgba(79, 70, 229, 0.3);
}
.teacher-stat-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    position: relative;
    z-index: 2;
}
.teacher-stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #e2e8f0;
    position: relative;
    z-index: 2;
}
.teacher-stat-label {
    font-size: 0.8rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 2;
}

.subject-badge-large {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
    border-radius: 50px;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.5);
}
.subject-badge-large i {
    font-size: 1.25rem;
}

.timeline-card {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.5rem;
    position: relative;
    margin-left: 2rem;
}
.timeline-card::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #4f46e5 0%, #9333ea 50%, #ec4899 100%);
    border-radius: 2px;
}
.timeline-card::after {
    content: '';
    position: absolute;
    left: -2.5rem;
    top: 1.5rem;
    width: 12px;
    height: 12px;
    background: #4f46e5;
    border-radius: 50%;
    border: 3px solid rgba(30, 41, 59, 1);
}

.skill-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(79, 70, 229, 0.1);
    border: 1px solid rgba(79, 70, 229, 0.2);
    border-radius: 8px;
    font-size: 0.85rem;
    color: #a5b4fc;
    margin: 0.25rem;
    transition: all 0.3s ease;
}
.skill-tag:hover {
    background: rgba(79, 70, 229, 0.2);
    transform: translateY(-2px);
}

.achievement-card {
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
    border: 1px solid rgba(251, 191, 36, 0.2);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}
.achievement-card:hover {
    transform: translateX(5px);
    border-color: rgba(251, 191, 36, 0.4);
}
.achievement-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
}
.achievement-text h5 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #e2e8f0;
}
.achievement-text p {
    margin: 0;
    font-size: 0.8rem;
    color: #64748b;
}
</style>

<!-- Teacher Welcome Badge -->
<div class="teacher-hero-badge">
    <i class="bi bi-award-fill"></i>
    <div class="teacher-hero-badge-text">
        <h4>Welcome, <?= e(explode(' ', $name)[0]) ?>!</h4>
        <p>Empowering minds, shaping futures</p>
    </div>
</div>

<!-- Subject & Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-4">
        <div class="text-center mb-4 mb-lg-0">
            <div class="subject-badge-large">
                <i class="bi bi-book"></i>
                <?= e($teacher['subject'] ?? 'Subject Specialist') ?>
            </div>
            <p class="text-muted mt-2 mb-0">Primary Subject</p>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="teacher-stat-card">
                    <div class="teacher-stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="teacher-stat-value"><?= e(($teacher['experience'] ?? '0') ?: '0') ?>+</div>
                    <div class="teacher-stat-label">Years Experience</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="teacher-stat-card">
                    <div class="teacher-stat-icon">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <div class="teacher-stat-value"><?= e(($teacher['qualification'] ?? '-') ?: '-') ?></div>
                    <div class="teacher-stat-label">Qualification</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="teacher-stat-card">
                    <div class="teacher-stat-icon">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                    <div class="teacher-stat-value"><?= e(($teacher['designation'] ?? 'Teacher') ?: 'Teacher') ?></div>
                    <div class="teacher-stat-label">Designation</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="teacher-stat-card">
                    <div class="teacher-stat-icon">
                        <i class="bi bi-calendar-heart"></i>
                    </div>
                    <div class="teacher-stat-value"><?= !empty($teacher['joining_date']) ? date('Y', strtotime($teacher['joining_date'])) : date('Y') ?></div>
                    <div class="teacher-stat-label">Since</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$teacher): ?>
<!-- Profile Not Linked Notice -->
<div class="alert-profile mb-4" style="background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 12px; padding: 1rem 1.25rem; color: #fbbf24; display: flex; align-items: center; gap: 0.75rem;">
    <i class="bi bi-info-circle" style="font-size: 1.25rem;"></i>
    <div>
        <strong>Profile Not Linked:</strong> Your teacher profile is not linked to your account yet. 
        Please contact the administrator to link your profile for complete information display.
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Professional Journey -->
    <div class="col-lg-8">
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-briefcase-fill"></i>
                <h4>Professional Journey</h4>
            </div>
            <div class="info-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="timeline-card mb-3">
                            <h6 class="text-primary mb-1">Current Position</h6>
                            <p class="mb-0 fw-semibold"><?= e($teacher['designation'] ?? 'Teacher') ?></p>
                            <p class="text-muted small mb-0"><?= e(SCHOOL_NAME) ?></p>
                        </div>
                        <div class="timeline-card">
                            <h6 class="text-primary mb-1">Joined On</h6>
                            <p class="mb-0 fw-semibold"><?= e($teacher['joining_date'] ? date('d F Y', strtotime($teacher['joining_date'])) : 'N/A') ?></p>
                            <p class="text-muted small mb-0">
                                <?php 
                                if (!empty($teacher['joining_date'])) {
                                    $years = (int)((time() - strtotime($teacher['joining_date'])) / (365.25 * 24 * 60 * 60));
                                    echo $years . ' years of dedicated service';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted small mb-3"><i class="bi bi-lightning me-1"></i> Skills & Expertise</h6>
                        <div class="d-flex flex-wrap">
                            <span class="skill-tag"><i class="bi bi-book"></i> <?= e($teacher['subject'] ?? 'Teaching') ?></span>
                            <span class="skill-tag"><i class="bi bi-people"></i> Student Mentoring</span>
                            <span class="skill-tag"><i class="bi bi-clipboard-check"></i> Exam Coordination</span>
                            <span class="skill-tag"><i class="bi bi-graph-up"></i> Progress Tracking</span>
                            <?php if (!empty($teacher['specialization'])): ?>
                            <span class="skill-tag"><i class="bi bi-star"></i> <?= e($teacher['specialization']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Achievements & Recognition -->
    <div class="col-lg-4">
        <div class="info-card h-100">
            <div class="info-card-header">
                <i class="bi bi-trophy-fill"></i>
                <h4>Recognition</h4>
            </div>
            <div class="info-card-body">
                <div class="achievement-card mb-3">
                    <div class="achievement-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div class="achievement-text">
                        <h5>Dedicated Educator</h5>
                        <p><?= e($teacher['experience'] ?? '0') ?>+ years of teaching excellence</p>
                    </div>
                </div>
                <div class="achievement-card mb-3">
                    <div class="achievement-icon">
                        <i class="bi bi-book-fill"></i>
                    </div>
                    <div class="achievement-text">
                        <h5>Subject Expert</h5>
                        <p><?= e($teacher['subject'] ?? 'Multi-subject') ?> Specialist</p>
                    </div>
                </div>
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <div class="achievement-text">
                        <h5>Valued Team Member</h5>
                        <p>Part of <?= e(SCHOOL_SHORT_NAME ?? 'School') ?> family</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($teacher): ?>
    <!-- Personal Information -->
    <div class="col-md-6 col-lg-4">
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-person-fill"></i>
                <h4>Personal Details</h4>
            </div>
            <div class="info-card-body">
                <div class="info-item">
                    <span class="info-label">Employee ID</span>
                    <span class="info-value"><?= e($teacher['employee_id'] ?? $teacher['id'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value"><?= !empty($teacher['dob']) ? date('d M Y', strtotime($teacher['dob'])) : 'N/A' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?= e(ucfirst($teacher['gender'] ?? 'N/A')) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Marital Status</span>
                    <span class="info-value"><?= e(ucfirst($teacher['marital_status'] ?? 'N/A')) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span class="badge bg-success"><?= e(ucfirst($teacher['status'] ?? 'Active')) ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($teacher): ?>
    <!-- Contact Information -->
    <div class="col-md-6 col-lg-4">
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-telephone-fill"></i>
                <h4>Contact Details</h4>
            </div>
            <div class="info-card-body">
                <div class="info-item">
                    <span class="info-label">Phone</span>
                    <span class="info-value"><?= e($teacher['phone'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value" style="font-size:0.8rem;"><?= e($teacher['email'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Emergency</span>
                    <span class="info-value"><?= e($teacher['emergency_contact'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Address</span>
                    <span class="info-value"><?= e($teacher['address'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($teacher): ?>
    <!-- Documents & Bank -->
    <div class="col-md-6 col-lg-4">
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-file-earmark-lock-fill"></i>
                <h4>Secure Documents</h4>
            </div>
            <div class="info-card-body">
                <div class="info-item">
                    <span class="info-label">Aadhar</span>
                    <span class="info-value">
                        <?php if (!empty($teacher['aadhar'])): ?>
                            ****-****-<?= substr($teacher['aadhar'], -4) ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">PAN</span>
                    <span class="info-value">
                        <?php if (!empty($teacher['pan'])): ?>
                            <?= substr($teacher['pan'], 0, 2) ?>****<?= substr($teacher['pan'], -2) ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bank Account</span>
                    <span class="info-value">
                        <?php if (!empty($teacher['bank_account'])): ?>
                            ****<?= substr($teacher['bank_account'], -4) ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bank Name</span>
                    <span class="info-value"><?= e($teacher['bank_name'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php else: ?>
<!-- Admin/Other Profile -->
<div class="row g-4">
    <div class="col-12">
        <?php if ($isAdmin): ?>
        <div class="admin-badge mb-4">
            <i class="bi bi-shield-fill-check"></i>
            <span>Full Administrative Access</span>
        </div>
        
        <div class="row g-3">
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-speedometer2"></i>
                        <h4>Quick Stats</h4>
                    </div>
                    <div class="info-card-body">
                        <?php
                        $studentCount = count(csv_read_all(DATA_PATH . '/students.csv'));
                        $teacherCount = count(csv_read_all(DATA_PATH . '/teachers.csv'));
                        $userCount = count(csv_read_all(DATA_PATH . '/users.csv'));
                        ?>
                        <div class="info-item">
                            <span class="info-label">Total Students</span>
                            <span class="info-value"><?= $studentCount ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total Teachers</span>
                            <span class="info-value"><?= $teacherCount ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">User Accounts</span>
                            <span class="info-value"><?= $userCount ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-lightning"></i>
                        <h4>Admin Quick Links</h4>
                    </div>
                    <div class="info-card-body">
                        <div class="row g-2">
                            <div class="col-6 col-md-4">
                                <a href="<?= e(base_url('students')) ?>" class="quick-action-btn w-100 justify-content-center">
                                    <i class="bi bi-people"></i> Students
                                </a>
                            </div>
                            <div class="col-6 col-md-4">
                                <a href="<?= e(base_url('teachers')) ?>" class="quick-action-btn w-100 justify-content-center">
                                    <i class="bi bi-person-workspace"></i> Teachers
                                </a>
                            </div>
                            <div class="col-6 col-md-4">
                                <a href="<?= e(base_url('exams')) ?>" class="quick-action-btn w-100 justify-content-center">
                                    <i class="bi bi-journal-text"></i> Exams
                                </a>
                            </div>
                            <div class="col-6 col-md-4">
                                <a href="<?= e(base_url('users')) ?>" class="quick-action-btn w-100 justify-content-center">
                                    <i class="bi bi-shield-lock"></i> Users
                                </a>
                            </div>
                            <div class="col-6 col-md-4">
                                <a href="<?= e(base_url('admissions')) ?>" class="quick-action-btn w-100 justify-content-center">
                                    <i class="bi bi-person-plus"></i> Admissions
                                </a>
                            </div>
                            <div class="col-6 col-md-4">
                                <a href="<?= e(base_url('site_settings')) ?>" class="quick-action-btn w-100 justify-content-center">
                                    <i class="bi bi-gear"></i> Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="info-card">
            <div class="info-card-body text-center py-5">
                <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Welcome, <?= e($u['username'] ?? 'User') ?>!</h4>
                <p class="text-muted">You are logged in as <strong><?= e(ucfirst($u['role'] ?? 'User')) ?></strong>.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php
};

include __DIR__ . '/views/partials/layout.php';
