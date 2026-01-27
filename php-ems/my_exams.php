<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('exams.read');

// Student view: filtered by student's class
$student = student_record_for_current_user();
$user = current_user();

// Enhanced lookup: Try multiple methods to find student record
if (!$student && $user) {
    $students = csv_read_all(DATA_PATH . '/students.csv');
    $username = strtolower(trim((string)($user['username'] ?? '')));
    $linkedId = strtolower(trim((string)($user['linked_id'] ?? '')));
    
    // Try matching by various fields
    foreach ($students as $s) {
        $sId = strtolower(trim((string)($s['id'] ?? '')));
        $sName = strtolower(trim((string)($s['name'] ?? '')));
        $sFullName = strtolower(trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')));
        $sStudentId = strtolower(trim((string)($s['student_id'] ?? '')));
        $sAdmissionNo = strtolower(trim((string)($s['admission_no'] ?? '')));
        $sEmail = strtolower(trim((string)($s['email'] ?? '')));
        
        // Match linked_id by student id OR name
        if ($linkedId !== '' && ($sId === $linkedId || $sName === $linkedId || $sFullName === $linkedId)) {
            $student = $s;
            break;
        }
        // Match by student_id field
        if ($sStudentId !== '' && $sStudentId === $username) {
            $student = $s;
            break;
        }
        // Match by admission_no
        if ($sAdmissionNo !== '' && $sAdmissionNo === $username) {
            $student = $s;
            break;
        }
        // Match by email
        if ($sEmail !== '' && $sEmail === $username) {
            $student = $s;
            break;
        }
        // Match by id
        if ($sId === $username) {
            $student = $s;
            break;
        }
        // Match username containing student id
        if ($sId !== '' && strpos($username, $sId) !== false) {
            $student = $s;
            break;
        }
    }
}

// If still no student found, show all exams (no class filter)
$profileLinked = ($student !== null);

$class = $student['class'] ?? null;
$studentName = $student['name'] ?? $student['first_name'] ?? null;
if (!$studentName && $student) {
    $studentName = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
}
$exams = upcoming_exams(50, $class ? (string)$class : null);

// Calculate days until next exam
$nextExamDays = null;
if ($exams && count($exams) > 0) {
    $nextExamDate = strtotime($exams[0]['date']);
    $today = strtotime(date('Y-m-d'));
    if ($nextExamDate && $today) {
        $nextExamDays = (int)ceil(($nextExamDate - $today) / 86400);
    }
}

$title = 'My Exam Schedule';
$active = 'my-exams';
$content = function () use ($student, $class, $exams, $studentName, $nextExamDays, $user, $profileLinked) {
?>
<style>
.exam-hero {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.15) 0%, rgba(147, 51, 234, 0.1) 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    overflow: hidden;
}
.exam-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, transparent 70%);
    animation: pulse-glow 4s ease-in-out infinite;
}
@keyframes pulse-glow {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
}
.student-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    font-weight: 600;
    box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.5);
}
.stat-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    transition: all 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
    background: rgba(255,255,255,0.06);
    border-color: rgba(79, 70, 229, 0.3);
}
.stat-value {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #4f46e5, #9333ea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.stat-label {
    font-size: 0.8rem;
    color: #a0aec0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}
.exam-table-wrapper {
    background: rgba(255,255,255,0.02);
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    overflow: hidden;
}
.exam-table {
    margin-bottom: 0;
}
.exam-table thead th {
    background: rgba(79, 70, 229, 0.2);
    border: none;
    padding: 1rem 1.25rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
    color: #a0aec0;
}
.exam-table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.exam-table tbody tr:hover {
    background: rgba(79, 70, 229, 0.1);
}
.exam-table tbody td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
    border: none;
}
.subject-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.2) 0%, rgba(147, 51, 234, 0.15) 100%);
    border-radius: 8px;
    font-weight: 600;
}
.subject-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
.date-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #cbd5e1;
}
.date-badge i {
    color: #4f46e5;
}
.marks-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 60px;
    padding: 0.5rem 1rem;
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border-radius: 20px;
    font-weight: 600;
}
.countdown-card {
    background: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    color: white;
}
.countdown-value {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
}
.countdown-label {
    font-size: 0.8rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}
.empty-state-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    background: rgba(79, 70, 229, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #4f46e5;
}
.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.section-title i {
    color: #4f46e5;
}
.alert-profile {
    background: rgba(251, 191, 36, 0.1);
    border: 1px solid rgba(251, 191, 36, 0.3);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    color: #fbbf24;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.alert-profile i {
    font-size: 1.25rem;
}
</style>

<!-- Hero Section -->
<div class="exam-hero">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="student-avatar">
                <?= strtoupper(substr($studentName ?: ($user['username'] ?? 'S'), 0, 1)) ?>
            </div>
        </div>
        <div class="col">
            <h2 class="h4 mb-1">
                <?php if ($studentName): ?>
                    Welcome, <?= e($studentName) ?>! 👋
                <?php else: ?>
                    Welcome, Student! 👋
                <?php endif; ?>
            </h2>
            <p class="text-muted mb-0">
                <?php if ($class): ?>
                    <i class="bi bi-mortarboard me-1"></i> Class: <strong><?= e($class) ?></strong>
                <?php else: ?>
                    <i class="bi bi-info-circle me-1"></i> Your exam schedule is shown below
                <?php endif; ?>
            </p>
        </div>
        <?php if ($nextExamDays !== null && $nextExamDays >= 0): ?>
        <div class="col-auto">
            <div class="countdown-card">
                <div class="countdown-value"><?= $nextExamDays ?></div>
                <div class="countdown-label">Days to Next Exam</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$profileLinked): ?>
<div class="alert-profile mb-4">
    <i class="bi bi-info-circle"></i>
    <div>
        <strong>Note:</strong> Showing all scheduled exams. For personalized schedule based on your class, 
        ask the administrator to link your profile in the Users section.
    </div>
</div>
<?php endif; ?>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value"><?= count($exams) ?></div>
            <div class="stat-label">Total Exams</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value">
                <?php
                $totalMarks = 0;
                foreach ($exams as $e) {
                    $totalMarks += (int)($e['marks'] ?? 0);
                }
                echo $totalMarks;
                ?>
            </div>
            <div class="stat-label">Total Marks</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value">
                <?php
                $subjects = [];
                foreach ($exams as $e) {
                    $subjects[$e['subject'] ?? ''] = true;
                }
                echo count($subjects);
                ?>
            </div>
            <div class="stat-label">Subjects</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value"><?= e($class ?: '-') ?></div>
            <div class="stat-label">Your Class</div>
        </div>
    </div>
</div>

<!-- Exam Schedule -->
<div class="section-title">
    <i class="bi bi-journal-text fs-4"></i>
    <h3 class="h5 mb-0">Upcoming Exams</h3>
</div>

<?php if (!$exams || count($exams) === 0): ?>
<div class="exam-table-wrapper">
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="bi bi-calendar-check"></i>
        </div>
        <h4>No Upcoming Exams</h4>
        <p class="text-muted">
            <?php if ($class): ?>
                Great news! There are no scheduled exams for Class <?= e($class) ?> at the moment.
            <?php else: ?>
                No exams are currently scheduled. Check back later!
            <?php endif; ?>
        </p>
    </div>
</div>
<?php else: ?>
<div class="exam-table-wrapper">
    <table class="table exam-table">
        <thead>
            <tr>
                <th style="width: 40%;">Subject</th>
                <th style="width: 35%;">Exam Date</th>
                <th style="width: 25%;" class="text-center">Total Marks</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $subjectIcons = [
                'hindi' => '📖',
                'english' => '📚',
                'mathematics' => '📐',
                'math' => '📐',
                'maths' => '📐',
                'science' => '🔬',
                'physics' => '⚛️',
                'chemistry' => '🧪',
                'biology' => '🧬',
                'history' => '🏛️',
                'geography' => '🌍',
                'civics' => '⚖️',
                'economics' => '📊',
                'computer' => '💻',
                'physical education' => '🏃',
                'art' => '🎨',
                'music' => '🎵',
            ];
            foreach ($exams as $idx => $exam): 
                $subjectLower = strtolower($exam['subject'] ?? '');
                $icon = $subjectIcons[$subjectLower] ?? '📝';
                $examDate = strtotime($exam['date']);
                $isToday = date('Y-m-d') === $exam['date'];
                $isPast = $examDate < strtotime(date('Y-m-d'));
            ?>
            <tr>
                <td>
                    <div class="subject-badge">
                        <span class="subject-icon"><?= $icon ?></span>
                        <span><?= e($exam['subject']) ?></span>
                    </div>
                </td>
                <td>
                    <div class="date-badge">
                        <i class="bi bi-calendar3"></i>
                        <span>
                            <?= date('D, d M Y', $examDate) ?>
                            <?php if ($isToday): ?>
                                <span class="badge bg-danger ms-2">Today!</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </td>
                <td class="text-center">
                    <span class="marks-badge"><?= e($exam['marks']) ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Study Tips -->
<?php if ($exams && count($exams) > 0): ?>
<div class="card mt-4" style="background: rgba(79, 70, 229, 0.05); border: 1px solid rgba(79, 70, 229, 0.2);">
    <div class="card-body">
        <h5 class="card-title mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Study Tips</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-start gap-2">
                    <span class="badge bg-primary">1</span>
                    <span class="small">Create a study timetable and stick to it</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start gap-2">
                    <span class="badge bg-primary">2</span>
                    <span class="small">Take short breaks to refresh your mind</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start gap-2">
                    <span class="badge bg-primary">3</span>
                    <span class="small">Practice previous year question papers</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
};

include __DIR__ . '/views/partials/layout.php';
