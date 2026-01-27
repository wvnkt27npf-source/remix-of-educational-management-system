<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('exams.write');

$exams = csv_read_all(DATA_PATH . '/exams.csv');
$classes = csv_read_all(DATA_PATH . '/classes.csv');
$notificationSent = false;
$newExamData = null;

// Handle form submissions
if (request_method() === 'POST') {
    csrf_verify_or_die();
    $action = post('action', '');
    
    if ($action === 'add') {
        $data = [
            'subject' => trim(post('subject', '')),
            'class' => trim(post('class', '')),
            'date' => trim(post('date', '')),
            'marks' => trim(post('marks', '100'))
        ];
        
        if (empty($data['subject']) || empty($data['class']) || empty($data['date'])) {
            flash_set('danger', 'All fields are required.');
        } else {
            $newExam = csv_insert(DATA_PATH . '/exams.csv', $data);
            $newExamData = $data;
            flash_set('success', 'Exam scheduled successfully!');
            
            // Check if notification should be sent
            if (post('send_notification') === '1') {
                $recipients = post('recipients', []);
                if (!empty($recipients)) {
                    $message = "📚 *New Exam Scheduled*\n\n";
                    $message .= "Subject: {$data['subject']}\n";
                    $message .= "Class: {$data['class']}\n";
                    $message .= "Date: " . date('d M Y', strtotime($data['date'])) . "\n";
                    $message .= "Total Marks: {$data['marks']}\n\n";
                    $message .= "Best of luck! 🍀";
                    
                    $result = send_notification_to_recipients($message, $recipients, $data['class']);
                    if ($result['telegram'] > 0 || $result['whatsapp'] > 0) {
                        flash_set('success', 'Exam scheduled and notifications sent!');
                    }
                }
            }
        }
        redirect('exams');
    }
    
    if ($action === 'edit') {
        $id = post('id', '');
        $data = [
            'subject' => trim(post('subject', '')),
            'class' => trim(post('class', '')),
            'date' => trim(post('date', '')),
            'marks' => trim(post('marks', '100'))
        ];
        
        if (csv_update_by_id(DATA_PATH . '/exams.csv', $id, $data)) {
            flash_set('success', 'Exam updated successfully!');
        } else {
            flash_set('danger', 'Failed to update exam.');
        }
        redirect('exams');
    }
    
    if ($action === 'delete') {
        $id = post('id', '');
        if (csv_delete_by_id(DATA_PATH . '/exams.csv', $id)) {
            flash_set('success', 'Exam deleted successfully!');
        } else {
            flash_set('danger', 'Failed to delete exam.');
        }
        redirect('exams');
    }
}

// Filter and sort
$filterClass = get('class', '');
if ($filterClass) {
    $exams = array_filter($exams, fn($e) => $e['class'] === $filterClass);
}
usort($exams, fn($a, $b) => strcmp($b['date'], $a['date']));

// Get unique classes
$uniqueClasses = array_unique(array_column(csv_read_all(DATA_PATH . '/exams.csv'), 'class'));
sort($uniqueClasses);

// Subject icons mapping
$subjectIcons = [
    'mathematics' => 'bi-calculator',
    'math' => 'bi-calculator',
    'english' => 'bi-book',
    'science' => 'bi-lightbulb',
    'physics' => 'bi-lightning',
    'chemistry' => 'bi-droplet',
    'biology' => 'bi-heart-pulse',
    'history' => 'bi-clock-history',
    'geography' => 'bi-globe',
    'computer' => 'bi-laptop',
    'hindi' => 'bi-translate',
    'economics' => 'bi-graph-up',
    'default' => 'bi-journal-text'
];

function getSubjectIcon($subject, $icons) {
    $lower = strtolower($subject);
    foreach ($icons as $key => $icon) {
        if (strpos($lower, $key) !== false) {
            return $icon;
        }
    }
    return $icons['default'];
}

$title = 'Examinations';
$active = 'exams';
$content = function () use ($exams, $classes, $uniqueClasses, $filterClass, $subjectIcons) {
?>
<style>
/* Exams Page Premium Styles */
.exams-hero {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(251, 191, 36, 0.15) 50%, rgba(252, 211, 77, 0.15) 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    overflow: hidden;
}
.exams-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(245, 158, 11, 0.1) 0%, transparent 70%);
    animation: pulse-slow 4s ease-in-out infinite;
}
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 0.8; }
}
.exams-title {
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
.exams-subtitle {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
}

/* Filter Bar */
.filter-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    align-items: center;
}
.filter-bar .form-select {
    max-width: 200px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    border-radius: 10px;
}
.filter-bar .form-select option {
    background: #1e1e2e;
}

/* Exam Grid */
.exam-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
}
.exam-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.exam-card:hover {
    transform: translateY(-5px);
    border-color: rgba(255,255,255,0.15);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}
.exam-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #f59e0b, #fbbf24, #fcd34d);
}
.exam-card.past::before {
    background: linear-gradient(90deg, #6b7280, #9ca3af);
}
.exam-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.exam-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(251, 191, 36, 0.2));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #fcd34d;
}
.exam-card.past .exam-icon {
    background: rgba(107, 114, 128, 0.2);
    color: #9ca3af;
}
.exam-info {
    flex: 1;
}
.exam-subject {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.25rem;
}
.exam-class {
    display: inline-block;
    padding: 4px 10px;
    background: rgba(99, 102, 241, 0.15);
    color: #a78bfa;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
}
.exam-details {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.25rem;
}
.exam-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.exam-detail-icon {
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
.exam-detail-value {
    font-weight: 600;
    color: #fff;
    font-size: 0.9rem;
}
.exam-detail-label {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.4);
}
.exam-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.exam-status.upcoming {
    background: rgba(34, 197, 94, 0.15);
    color: #6ee7b7;
}
.exam-status.today {
    background: rgba(245, 158, 11, 0.15);
    color: #fcd34d;
}
.exam-status.past {
    background: rgba(107, 114, 128, 0.15);
    color: #9ca3af;
}
.exam-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.exam-action-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.exam-action-btn.edit {
    background: rgba(245, 158, 11, 0.15);
    color: #fcd34d;
}
.exam-action-btn.edit:hover {
    background: rgba(245, 158, 11, 0.25);
}
.exam-action-btn.delete {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}
.exam-action-btn.delete:hover {
    background: rgba(239, 68, 68, 0.25);
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
    border-color: rgba(245, 158, 11, 0.5);
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    color: #fff;
}
.form-control::placeholder {
    color: rgba(255,255,255,0.3);
}
.form-select option {
    background: #1e1e2e;
}

/* Notification Section */
.notification-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.08);
}
.notification-toggle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.notification-toggle label {
    color: #fff;
    font-weight: 500;
}
.recipient-options {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.recipient-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.recipient-checkbox:hover {
    background: rgba(255,255,255,0.06);
}
.recipient-checkbox input {
    accent-color: #22c55e;
}
.recipient-checkbox span {
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
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
    background: rgba(245, 158, 11, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #fcd34d;
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
<div class="exams-hero animate-in">
    <div class="exams-title">
        <i class="bi bi-journal-text"></i>
        Examination Management
    </div>
    <div class="exams-subtitle">Schedule, manage, and track all examinations efficiently.</div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <select class="form-select" onchange="window.location.href='?class='+this.value">
        <option value="">All Classes</option>
        <?php foreach ($uniqueClasses as $c): ?>
            <option value="<?= e($c) ?>" <?= $filterClass === $c ? 'selected' : '' ?>>Class <?= e($c) ?></option>
        <?php endforeach; ?>
    </select>
    <div class="ms-auto">
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addExamModal">
            <i class="bi bi-plus-lg me-2"></i>Schedule Examination
        </button>
    </div>
</div>

<!-- Exam Grid -->
<?php if (empty($exams)): ?>
    <div class="empty-state animate-in">
        <div class="empty-state-icon"><i class="bi bi-journal-x"></i></div>
        <div class="empty-state-title">No Exams Scheduled</div>
        <div class="empty-state-desc">Start by scheduling your first examination.</div>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addExamModal">
            <i class="bi bi-plus-lg me-2"></i>Schedule First Exam
        </button>
    </div>
<?php else: ?>
    <div class="exam-grid">
        <?php foreach ($exams as $exam):
            $examDate = strtotime($exam['date']);
            $today = strtotime(date('Y-m-d'));
            $isPast = $examDate < $today;
            $isToday = $examDate === $today;
            $icon = getSubjectIcon($exam['subject'], $subjectIcons);
        ?>
            <div class="exam-card <?= $isPast ? 'past' : '' ?> animate-in">
                <div class="exam-header">
                    <div class="exam-icon"><i class="bi <?= $icon ?>"></i></div>
                    <div class="exam-info">
                        <div class="exam-subject"><?= e($exam['subject']) ?></div>
                        <span class="exam-class">Class <?= e($exam['class']) ?></span>
                    </div>
                </div>
                
                <div class="exam-details">
                    <div class="exam-detail">
                        <div class="exam-detail-icon"><i class="bi bi-calendar"></i></div>
                        <div>
                            <div class="exam-detail-value"><?= e(date('d M Y', $examDate)) ?></div>
                            <div class="exam-detail-label">Date</div>
                        </div>
                    </div>
                    <div class="exam-detail">
                        <div class="exam-detail-icon"><i class="bi bi-trophy"></i></div>
                        <div>
                            <div class="exam-detail-value"><?= e($exam['marks']) ?></div>
                            <div class="exam-detail-label">Marks</div>
                        </div>
                    </div>
                </div>
                
                <span class="exam-status <?= $isPast ? 'past' : ($isToday ? 'today' : 'upcoming') ?>">
                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                    <?= $isPast ? 'Completed' : ($isToday ? 'Today' : 'Upcoming') ?>
                </span>
                
                <div class="exam-actions">
                    <button class="exam-action-btn edit" onclick="editExam(<?= e(json_encode($exam)) ?>)">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="exam-action-btn delete" onclick="deleteExam('<?= e($exam['id']) ?>')">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Add Exam Modal with Notification -->
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="addExamForm">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Schedule New Examination</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Subject *</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g., Mathematics" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Class *</label>
                            <input type="text" name="class" class="form-control" placeholder="e.g., 10" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Total Marks</label>
                            <input type="number" name="marks" class="form-control" value="100" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Date *</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                    </div>
                    
                    <!-- Notification Section -->
                    <div class="notification-section">
                        <div class="notification-toggle">
                            <input type="checkbox" id="sendNotificationCheck" name="send_notification" value="1" class="form-check-input">
                            <label for="sendNotificationCheck">Send notification to recipients</label>
                        </div>
                        <div class="recipient-options" id="recipientOptions" style="display: none;">
                            <label class="recipient-checkbox">
                                <input type="checkbox" name="recipients[]" value="students">
                                <span><i class="bi bi-mortarboard me-1"></i>Students</span>
                            </label>
                            <label class="recipient-checkbox">
                                <input type="checkbox" name="recipients[]" value="teachers">
                                <span><i class="bi bi-person-workspace me-1"></i>Teachers</span>
                            </label>
                            <label class="recipient-checkbox">
                                <input type="checkbox" name="recipients[]" value="both">
                                <span><i class="bi bi-people me-1"></i>Both</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Schedule Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Exam Modal -->
<div class="modal fade" id="editExamModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editExamId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Examination</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Subject *</label>
                            <input type="text" name="subject" id="editExamSubject" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Class *</label>
                            <input type="text" name="class" id="editExamClass" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Total Marks</label>
                            <input type="number" name="marks" id="editExamMarks" class="form-control" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Date *</label>
                            <input type="date" name="date" id="editExamDate" class="form-control" required>
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
<div class="modal fade" id="deleteExamModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteExamId">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this examination?</p>
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
// Toggle notification options
document.getElementById('sendNotificationCheck').addEventListener('change', function() {
    document.getElementById('recipientOptions').style.display = this.checked ? 'flex' : 'none';
});

function editExam(exam) {
    document.getElementById('editExamId').value = exam.id || '';
    document.getElementById('editExamSubject').value = exam.subject || '';
    document.getElementById('editExamClass').value = exam.class || '';
    document.getElementById('editExamMarks').value = exam.marks || '100';
    document.getElementById('editExamDate').value = exam.date || '';
    new bootstrap.Modal(document.getElementById('editExamModal')).show();
}

function deleteExam(id) {
    document.getElementById('deleteExamId').value = id;
    new bootstrap.Modal(document.getElementById('deleteExamModal')).show();
}
</script>

<?php
};
include __DIR__ . '/views/partials/layout.php';
