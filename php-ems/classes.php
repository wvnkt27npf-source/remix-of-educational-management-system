<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('*');

$classes = csv_read_all(DATA_PATH . '/classes.csv');
$teachers = csv_read_all(DATA_PATH . '/teachers.csv');
$students = csv_read_all(DATA_PATH . '/students.csv');

// Handle form submissions
if (request_method() === 'POST') {
    csrf_verify_or_die();
    $action = post('action', '');
    
    if ($action === 'add') {
        $data = [
            'name' => trim(post('name', '')),
            'section' => trim(post('section', '')),
            'class_teacher_id' => trim(post('class_teacher_id', '')),
            'academic_year' => trim(post('academic_year', date('Y') . '-' . (date('Y') + 1))),
            'room_number' => trim(post('room_number', '')),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if (empty($data['name'])) {
            flash_set('danger', 'Class name is required.');
        } else {
            csv_insert(DATA_PATH . '/classes.csv', $data);
            flash_set('success', 'Class added successfully!');
        }
        redirect('classes');
    }
    
    if ($action === 'edit') {
        $id = post('id', '');
        $data = [
            'name' => trim(post('name', '')),
            'section' => trim(post('section', '')),
            'class_teacher_id' => trim(post('class_teacher_id', '')),
            'academic_year' => trim(post('academic_year', '')),
            'room_number' => trim(post('room_number', '')),
            'status' => post('status', 'active')
        ];
        
        if (csv_update_by_id(DATA_PATH . '/classes.csv', $id, $data)) {
            flash_set('success', 'Class updated successfully!');
        } else {
            flash_set('danger', 'Failed to update class.');
        }
        redirect('classes');
    }
    
    if ($action === 'delete') {
        $id = post('id', '');
        if (csv_delete_by_id(DATA_PATH . '/classes.csv', $id)) {
            flash_set('success', 'Class deleted successfully!');
        } else {
            flash_set('danger', 'Failed to delete class.');
        }
        redirect('classes');
    }
}

// Get teacher name by ID
function get_teacher_name_by_id($teachers, $id) {
    foreach ($teachers as $t) {
        if ((string)$t['id'] === (string)$id) {
            return $t['name'] . ' (' . ($t['subject'] ?? 'N/A') . ')';
        }
    }
    return '-';
}

// Count students in a class
function count_class_students($students, $className, $section = '') {
    $count = 0;
    foreach ($students as $s) {
        if ((string)$s['class'] === (string)$className) {
            if (empty($section) || (string)$s['section'] === (string)$section) {
                $count++;
            }
        }
    }
    return $count;
}

$title = 'Class Management';
$active = 'classes';
$content = function () use ($classes, $teachers, $students) {
?>
<style>
/* Classes Page Premium Styles */
.classes-hero {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(16, 185, 129, 0.15) 50%, rgba(6, 182, 212, 0.15) 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    overflow: hidden;
}
.classes-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(34, 197, 94, 0.1) 0%, transparent 70%);
    animation: pulse-slow 4s ease-in-out infinite;
}
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 0.8; }
}
.classes-title {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.7) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.classes-subtitle {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
}

/* Class Cards Grid */
.class-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
}
.class-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.class-card:hover {
    transform: translateY(-5px);
    border-color: rgba(255,255,255,0.15);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}
.class-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #22c55e, #10b981, #06b6d4);
}
.class-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}
.class-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(16, 185, 129, 0.2));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #6ee7b7;
}
.class-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}
.class-section {
    font-size: 1rem;
    color: rgba(255,255,255,0.5);
    font-weight: 500;
}
.class-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}
.class-status.active {
    background: rgba(34, 197, 94, 0.15);
    color: #6ee7b7;
}
.class-status.inactive {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}
.class-info {
    margin-bottom: 1.25rem;
}
.class-info-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.class-info-row:last-child {
    border-bottom: none;
}
.class-info-icon {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.5);
    font-size: 0.9rem;
}
.class-info-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.class-info-value {
    font-weight: 600;
    color: #fff;
    font-size: 0.9rem;
}
.class-actions {
    display: flex;
    gap: 0.5rem;
}
.class-action-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}
.class-action-btn.view {
    background: rgba(99, 102, 241, 0.15);
    color: #a78bfa;
}
.class-action-btn.view:hover {
    background: rgba(99, 102, 241, 0.25);
    color: #c4b5fd;
}
.class-action-btn.edit {
    background: rgba(245, 158, 11, 0.15);
    color: #fcd34d;
}
.class-action-btn.edit:hover {
    background: rgba(245, 158, 11, 0.25);
    color: #fde68a;
}
.class-action-btn.delete {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}
.class-action-btn.delete:hover {
    background: rgba(239, 68, 68, 0.25);
    color: #fecaca;
}

/* Stats Bar */
.class-stats-bar {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.class-stat {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.class-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.class-stat.total .class-stat-icon {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(16, 185, 129, 0.2));
    color: #6ee7b7;
}
.class-stat.active .class-stat-icon {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    color: #a78bfa;
}
.class-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    line-height: 1;
}
.class-stat-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Modal Styles */
.modal-content {
    background: linear-gradient(135deg, #1e1e2e 0%, #2d2d3f 100%);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
}
.modal-header {
    border-bottom: 1px solid rgba(255,255,255,0.08);
    padding: 1.25rem 1.5rem;
}
.modal-title {
    font-weight: 600;
    color: #fff;
}
.modal-body {
    padding: 1.5rem;
}
.modal-footer {
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 1rem 1.5rem;
}
.form-label {
    color: rgba(255,255,255,0.7);
    font-weight: 500;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}
.form-control, .form-select {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    border-radius: 10px;
    padding: 0.75rem 1rem;
}
.form-control:focus, .form-select:focus {
    background: rgba(255,255,255,0.08);
    border-color: rgba(34, 197, 94, 0.5);
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    color: #fff;
}
.form-control::placeholder {
    color: rgba(255,255,255,0.3);
}
.form-select option {
    background: #1e1e2e;
    color: #fff;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255,255,255,0.02);
    border-radius: 16px;
    border: 1px dashed rgba(255,255,255,0.1);
}
.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: rgba(34, 197, 94, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #6ee7b7;
}
.empty-state-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 0.5rem;
}
.empty-state-desc {
    color: rgba(255,255,255,0.5);
    margin-bottom: 1.5rem;
}

/* Animations */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-in {
    animation: fadeInUp 0.5s ease forwards;
}
</style>

<!-- Hero Section -->
<div class="classes-hero animate-in">
    <div class="classes-title">
        <i class="bi bi-building"></i>
        Class Management
    </div>
    <div class="classes-subtitle">Organize classes, assign teachers, and manage student groups efficiently.</div>
</div>

<!-- Stats Bar -->
<div class="class-stats-bar">
    <div class="class-stat total">
        <div class="class-stat-icon"><i class="bi bi-building"></i></div>
        <div>
            <div class="class-stat-value"><?= count($classes) ?></div>
            <div class="class-stat-label">Total Classes</div>
        </div>
    </div>
    <div class="class-stat active">
        <div class="class-stat-icon"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="class-stat-value"><?= count(array_filter($classes, fn($c) => ($c['status'] ?? 'active') === 'active')) ?></div>
            <div class="class-stat-label">Active Classes</div>
        </div>
    </div>
    <div class="ms-auto">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClassModal">
            <i class="bi bi-plus-lg me-2"></i>Add New Class
        </button>
    </div>
</div>

<!-- Class Grid -->
<?php if (empty($classes)): ?>
    <div class="empty-state animate-in">
        <div class="empty-state-icon"><i class="bi bi-building"></i></div>
        <div class="empty-state-title">No Classes Created Yet</div>
        <div class="empty-state-desc">Start by adding your first class to organize students.</div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClassModal">
            <i class="bi bi-plus-lg me-2"></i>Add First Class
        </button>
    </div>
<?php else: ?>
    <div class="class-grid">
        <?php foreach ($classes as $class): 
            $teacherName = get_teacher_name_by_id($teachers, $class['class_teacher_id'] ?? '');
            $studentCount = count_class_students($students, $class['name'], $class['section'] ?? '');
        ?>
            <div class="class-card animate-in">
                <div class="class-header">
                    <div>
                        <div class="class-name">
                            Class <?= e($class['name']) ?>
                            <?php if (!empty($class['section'])): ?>
                                <span class="class-section">- <?= e($class['section']) ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="class-status <?= ($class['status'] ?? 'active') === 'active' ? 'active' : 'inactive' ?>">
                            <?= e($class['status'] ?? 'active') ?>
                        </span>
                    </div>
                    <div class="class-icon"><i class="bi bi-building"></i></div>
                </div>
                
                <div class="class-info">
                    <div class="class-info-row">
                        <div class="class-info-icon"><i class="bi bi-person-workspace"></i></div>
                        <div>
                            <div class="class-info-label">Class Teacher</div>
                            <div class="class-info-value"><?= e($teacherName) ?></div>
                        </div>
                    </div>
                    <div class="class-info-row">
                        <div class="class-info-icon"><i class="bi bi-people"></i></div>
                        <div>
                            <div class="class-info-label">Students</div>
                            <div class="class-info-value"><?= $studentCount ?> enrolled</div>
                        </div>
                    </div>
                    <div class="class-info-row">
                        <div class="class-info-icon"><i class="bi bi-door-open"></i></div>
                        <div>
                            <div class="class-info-label">Room</div>
                            <div class="class-info-value"><?= e($class['room_number'] ?? '-') ?></div>
                        </div>
                    </div>
                    <div class="class-info-row">
                        <div class="class-info-icon"><i class="bi bi-calendar"></i></div>
                        <div>
                            <div class="class-info-label">Academic Year</div>
                            <div class="class-info-value"><?= e($class['academic_year'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="class-actions">
                    <a href="<?= e(base_url('students?class=' . urlencode($class['name']) . '&section=' . urlencode($class['section'] ?? ''))) ?>" class="class-action-btn view">
                        <i class="bi bi-eye"></i> View
                    </a>
                    <button class="class-action-btn edit" onclick="editClass(<?= e(json_encode($class)) ?>)">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="class-action-btn delete" onclick="deleteClass('<?= e($class['id']) ?>', 'Class <?= e($class['name']) ?><?= !empty($class['section']) ? '-' . e($class['section']) : '' ?>')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Class</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Class Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., 10" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control" placeholder="e.g., A">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Class Teacher</label>
                            <select name="class_teacher_id" class="form-select">
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= e($t['id']) ?>"><?= e($t['name']) ?> (<?= e($t['subject'] ?? 'N/A') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Academic Year</label>
                            <input type="text" name="academic_year" class="form-control" value="<?= date('Y') ?>-<?= date('Y') + 1 ?>" placeholder="e.g., 2025-2026">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="room_number" class="form-control" placeholder="e.g., 101">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Add Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editClassId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Class</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Class Name *</label>
                            <input type="text" name="name" id="editClassName" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" id="editClassSection" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Class Teacher</label>
                            <select name="class_teacher_id" id="editClassTeacher" class="form-select">
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= e($t['id']) ?>"><?= e($t['name']) ?> (<?= e($t['subject'] ?? 'N/A') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Academic Year</label>
                            <input type="text" name="academic_year" id="editClassYear" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="room_number" id="editClassRoom" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select name="status" id="editClassStatus" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteClassId">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete <strong id="deleteClassName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editClass(classData) {
    document.getElementById('editClassId').value = classData.id || '';
    document.getElementById('editClassName').value = classData.name || '';
    document.getElementById('editClassSection').value = classData.section || '';
    document.getElementById('editClassTeacher').value = classData.class_teacher_id || '';
    document.getElementById('editClassYear').value = classData.academic_year || '';
    document.getElementById('editClassRoom').value = classData.room_number || '';
    document.getElementById('editClassStatus').value = classData.status || 'active';
    new bootstrap.Modal(document.getElementById('editClassModal')).show();
}

function deleteClass(id, name) {
    document.getElementById('deleteClassId').value = id;
    document.getElementById('deleteClassName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteClassModal')).show();
}
</script>

<?php
};
include __DIR__ . '/views/partials/layout.php';
