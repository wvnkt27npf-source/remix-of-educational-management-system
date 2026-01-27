<?php
require_once __DIR__ . '/bootstrap.php';
require_login();

$u = current_user();
$students = csv_read_all(DATA_PATH . '/students.csv');
$exams = csv_read_all(DATA_PATH . '/exams.csv');
$results = csv_read_all(DATA_PATH . '/exam_results.csv');

// Find student record for current user
$student = null;
$username = strtolower(trim($u['username'] ?? ''));
$linkedId = $u['linked_id'] ?? '';

foreach ($students as $s) {
    $sId = strtolower(trim($s['id'] ?? ''));
    $sEmail = strtolower(trim($s['email'] ?? ''));
    
    // Match by linked_id, student id, email, or username
    if (
        (!empty($linkedId) && $sId === strtolower($linkedId)) ||
        $sId === $username ||
        $sEmail === $username ||
        (!empty($s['admission_no']) && strtolower($s['admission_no']) === $username)
    ) {
        $student = $s;
        break;
    }
}

// Get student's results
$myResults = [];
if ($student) {
    foreach ($results as $r) {
        if ((string)$r['student_id'] === (string)$student['id']) {
            $myResults[] = $r;
        }
    }
}

// Helper function
function get_exam_by_id($exams, $id) {
    foreach ($exams as $e) {
        if ((string)$e['id'] === (string)$id) return $e;
    }
    return null;
}

// Calculate stats
$totalMarks = 0;
$obtainedMarks = 0;
$passedCount = 0;
foreach ($myResults as $r) {
    $exam = get_exam_by_id($exams, $r['exam_id']);
    if ($exam) {
        $totalMarks += (int)($exam['marks'] ?? 0);
        $obtainedMarks += (int)($r['marks_obtained'] ?? 0);
        if ((int)$r['marks_obtained'] >= ((int)$exam['marks'] * 0.33)) {
            $passedCount++;
        }
    }
}
$percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) : 0;

$title = 'My Results';
$active = 'my-results';
$content = function () use ($student, $myResults, $exams, $percentage, $passedCount, $obtainedMarks, $totalMarks) {
?>
<style>
/* My Results Premium Styles */
.results-hero {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(16, 185, 129, 0.15) 50%, rgba(6, 182, 212, 0.15) 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    overflow: hidden;
}
.results-hero::before {
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
.results-title {
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
.results-subtitle {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}
.stat-card.percentage::before { background: linear-gradient(90deg, #22c55e, #10b981); }
.stat-card.exams::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.stat-card.passed::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.stat-card.marks::before { background: linear-gradient(90deg, #ec4899, #f472b6); }

.stat-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 1rem;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.stat-card.percentage .stat-icon { background: rgba(34, 197, 94, 0.2); color: #6ee7b7; }
.stat-card.exams .stat-icon { background: rgba(99, 102, 241, 0.2); color: #a78bfa; }
.stat-card.passed .stat-icon { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
.stat-card.marks .stat-icon { background: rgba(236, 72, 153, 0.2); color: #f9a8d4; }

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
}
.stat-label {
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
    margin-top: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Results Grid */
.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.25rem;
}
.result-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.result-card:hover {
    transform: translateY(-5px);
    border-color: rgba(255,255,255,0.15);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}
.result-card.pass {
    border-top: 3px solid #22c55e;
}
.result-card.fail {
    border-top: 3px solid #ef4444;
}
.result-header {
    padding: 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.result-subject {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.result-subject-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a78bfa;
    font-size: 1.25rem;
}
.result-subject-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
}
.result-subject-date {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
}
.result-grade {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1.1rem;
}
.result-grade.a { background: rgba(34, 197, 94, 0.2); color: #6ee7b7; }
.result-grade.b { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
.result-grade.c { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
.result-grade.d { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
.result-grade.f { background: rgba(239, 68, 68, 0.3); color: #fca5a5; }

.result-body {
    padding: 1.25rem;
}
.result-marks {
    display: flex;
    justify-content: center;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.result-marks-obtained {
    font-size: 3rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
}
.result-marks-total {
    font-size: 1.25rem;
    color: rgba(255,255,255,0.4);
}

.result-progress {
    height: 8px;
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 1rem;
}
.result-progress-bar {
    height: 100%;
    border-radius: 4px;
    transition: width 0.5s ease;
}
.result-progress-bar.pass { background: linear-gradient(90deg, #22c55e, #10b981); }
.result-progress-bar.fail { background: linear-gradient(90deg, #ef4444, #f87171); }

.result-status {
    text-align: center;
    padding: 0.75rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
}
.result-status.pass {
    background: rgba(34, 197, 94, 0.1);
    color: #6ee7b7;
}
.result-status.fail {
    background: rgba(239, 68, 68, 0.1);
    color: #fca5a5;
}

.result-image-btn {
    display: block;
    width: 100%;
    padding: 0.75rem;
    margin-top: 0.75rem;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 10px;
    color: #a78bfa;
    text-align: center;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}
.result-image-btn:hover {
    background: rgba(99, 102, 241, 0.2);
    color: #c4b5fd;
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
}

/* Alert */
.alert-info-custom {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    color: #93c5fd;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.alert-info-custom i {
    font-size: 1.25rem;
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
<div class="results-hero animate-in">
    <div class="results-title">
        <i class="bi bi-trophy"></i>
        My Exam Results
    </div>
    <div class="results-subtitle">
        View your academic performance and exam scores
        <?php if ($student): ?>
            • <?= e($student['name']) ?> (Class <?= e($student['class']) ?>-<?= e($student['section'] ?? '') ?>)
        <?php endif; ?>
    </div>
</div>

<?php if (!$student): ?>
    <div class="alert-info-custom">
        <i class="bi bi-info-circle"></i>
        <span>Your account is not linked to a student record. Please contact the administrator for assistance.</span>
    </div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card percentage animate-in">
        <div class="stat-icon"><i class="bi bi-percent"></i></div>
        <div class="stat-value"><?= $percentage ?>%</div>
        <div class="stat-label">Overall Percentage</div>
    </div>
    <div class="stat-card exams animate-in">
        <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
        <div class="stat-value"><?= count($myResults) ?></div>
        <div class="stat-label">Total Exams</div>
    </div>
    <div class="stat-card passed animate-in">
        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
        <div class="stat-value"><?= $passedCount ?></div>
        <div class="stat-label">Exams Passed</div>
    </div>
    <div class="stat-card marks animate-in">
        <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
        <div class="stat-value"><?= $obtainedMarks ?>/<?= $totalMarks ?></div>
        <div class="stat-label">Total Marks</div>
    </div>
</div>

<!-- Results Grid -->
<?php if (empty($myResults)): ?>
    <div class="empty-state animate-in">
        <div class="empty-state-icon"><i class="bi bi-clipboard-x"></i></div>
        <div class="empty-state-title">No Results Yet</div>
        <div class="empty-state-desc">Your exam results will appear here once they are uploaded.</div>
    </div>
<?php else: ?>
    <div class="results-grid">
        <?php foreach ($myResults as $result):
            $exam = get_exam_by_id($exams, $result['exam_id']);
            $marks = (int)($result['marks_obtained'] ?? 0);
            $totalMarks = (int)($exam['marks'] ?? 100);
            $percentage = $totalMarks > 0 ? round(($marks / $totalMarks) * 100) : 0;
            $isPassing = $percentage >= 33;
            $grade = $result['grade'] ?? '';
            if (empty($grade)) {
                if ($percentage >= 90) $grade = 'A+';
                elseif ($percentage >= 80) $grade = 'A';
                elseif ($percentage >= 70) $grade = 'B+';
                elseif ($percentage >= 60) $grade = 'B';
                elseif ($percentage >= 50) $grade = 'C';
                elseif ($percentage >= 33) $grade = 'D';
                else $grade = 'F';
            }
            $gradeClass = strtolower(substr($grade, 0, 1));
        ?>
            <div class="result-card <?= $isPassing ? 'pass' : 'fail' ?> animate-in">
                <div class="result-header">
                    <div class="result-subject">
                        <div class="result-subject-icon"><i class="bi bi-book"></i></div>
                        <div>
                            <div class="result-subject-name"><?= e($exam['subject'] ?? 'Unknown') ?></div>
                            <div class="result-subject-date"><?= e(date('d M Y', strtotime($exam['date'] ?? 'now'))) ?></div>
                        </div>
                    </div>
                    <div class="result-grade <?= $gradeClass ?>"><?= e($grade) ?></div>
                </div>
                <div class="result-body">
                    <div class="result-marks">
                        <span class="result-marks-obtained"><?= $marks ?></span>
                        <span class="result-marks-total">/ <?= $totalMarks ?></span>
                    </div>
                    <div class="result-progress">
                        <div class="result-progress-bar <?= $isPassing ? 'pass' : 'fail' ?>" style="width: <?= $percentage ?>%;"></div>
                    </div>
                    <div class="result-status <?= $isPassing ? 'pass' : 'fail' ?>">
                        <i class="bi bi-<?= $isPassing ? 'check-circle' : 'x-circle' ?> me-1"></i>
                        <?= $isPassing ? 'PASSED' : 'NEEDS IMPROVEMENT' ?>
                    </div>
                    <?php if (!empty($result['result_image'])): ?>
                        <a href="<?= e(base_url('uploads/' . $result['result_image'])) ?>" target="_blank" class="result-image-btn">
                            <i class="bi bi-image me-1"></i>View Marksheet
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img id="previewImage" src="" style="max-width: 100%; max-height: 90vh; border-radius: 12px;">
            </div>
        </div>
    </div>
</div>

<?php
};
include __DIR__ . '/views/partials/layout.php';
