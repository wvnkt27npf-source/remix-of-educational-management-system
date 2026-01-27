<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('exams.write');

$exams = csv_read_all(DATA_PATH . '/exams.csv');
$students = csv_read_all(DATA_PATH . '/students.csv');
$results = csv_read_all(DATA_PATH . '/exam_results.csv');
$u = current_user();

// Get unique classes for filter
$allClasses = [];
foreach ($students as $s) {
    if (!empty($s['class'])) {
        $classKey = $s['class'] . (!empty($s['section']) ? '-' . $s['section'] : '');
        $allClasses[$classKey] = ['class' => $s['class'], 'section' => $s['section'] ?? ''];
    }
}
ksort($allClasses);

// Handle form submissions
if (request_method() === 'POST') {
    csrf_verify_or_die();
    $action = post('action', '');
    
    if ($action === 'add_result') {
        $data = [
            'exam_id' => trim(post('exam_id', '')),
            'student_id' => trim(post('student_id', '')),
            'marks_obtained' => trim(post('marks_obtained', '')),
            'grade' => trim(post('grade', '')),
            'remarks' => trim(post('remarks', '')),
            'result_image' => '',
            'uploaded_by' => $u['id'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Handle image upload
        if (!empty($_FILES['result_image']['name'])) {
            $upload = upload_image($_FILES['result_image'], 'result');
            if ($upload['success']) {
                $data['result_image'] = $upload['filename'];
            } else {
                flash_set('danger', 'Image upload failed: ' . $upload['error']);
                redirect('exam_results');
            }
        }
        
        if (empty($data['exam_id']) || empty($data['student_id'])) {
            flash_set('danger', 'Exam and Student are required.');
        } else {
            // Check for existing result
            $exists = false;
            foreach ($results as $r) {
                if ($r['exam_id'] === $data['exam_id'] && $r['student_id'] === $data['student_id']) {
                    $exists = true;
                    break;
                }
            }
            
            if ($exists) {
                flash_set('warning', 'Result already exists for this student in this exam. Use edit to update.');
            } else {
                csv_insert(DATA_PATH . '/exam_results.csv', $data);
                flash_set('success', 'Result added successfully!');
            }
        }
        redirect('exam_results');
    }
    
    if ($action === 'bulk_add') {
        $examId = post('exam_id', '');
        $studentIds = post('student_ids', []);
        $marks = post('marks', []);
        $grades = post('grades', []);
        
        if (empty($examId) || empty($studentIds)) {
            flash_set('danger', 'Please select an exam and add student marks.');
            redirect('exam_results');
        }
        
        // Handle bulk image uploads
        $uploadedImages = [];
        if (!empty($_FILES['result_images']['name'][0])) {
            foreach ($_FILES['result_images']['name'] as $idx => $fileName) {
                if (!empty($fileName)) {
                    $file = [
                        'name' => $_FILES['result_images']['name'][$idx],
                        'type' => $_FILES['result_images']['type'][$idx],
                        'tmp_name' => $_FILES['result_images']['tmp_name'][$idx],
                        'error' => $_FILES['result_images']['error'][$idx],
                        'size' => $_FILES['result_images']['size'][$idx]
                    ];
                    $upload = upload_image($file, 'result');
                    if ($upload['success']) {
                        $uploadedImages[$idx] = $upload['filename'];
                    }
                }
            }
        }
        
        // Refresh results to get latest
        $results = csv_read_all(DATA_PATH . '/exam_results.csv');
        
        $added = 0;
        foreach ($studentIds as $index => $studentId) {
            if (empty($studentId) || !isset($marks[$index]) || $marks[$index] === '') continue;
            
            // Check if result already exists
            $exists = false;
            foreach ($results as $r) {
                if ($r['exam_id'] === $examId && $r['student_id'] === $studentId) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $data = [
                    'exam_id' => $examId,
                    'student_id' => $studentId,
                    'marks_obtained' => $marks[$index],
                    'grade' => $grades[$index] ?? '',
                    'remarks' => '',
                    'result_image' => $uploadedImages[$index] ?? '',
                    'uploaded_by' => $u['id'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
                csv_insert(DATA_PATH . '/exam_results.csv', $data);
                $added++;
            }
        }
        
        flash_set('success', "$added results added successfully!");
        redirect('exam_results');
    }
    
    if ($action === 'edit_result') {
        $id = post('id', '');
        $data = [
            'marks_obtained' => trim(post('marks_obtained', '')),
            'grade' => trim(post('grade', '')),
            'remarks' => trim(post('remarks', ''))
        ];
        
        // Handle image upload
        if (!empty($_FILES['result_image']['name'])) {
            $upload = upload_image($_FILES['result_image'], 'result');
            if ($upload['success']) {
                // Delete old image if exists
                $oldResult = csv_find_by_id(DATA_PATH . '/exam_results.csv', $id);
                if ($oldResult && !empty($oldResult['result_image'])) {
                    delete_upload($oldResult['result_image']);
                }
                $data['result_image'] = $upload['filename'];
            }
        }
        
        if (csv_update_by_id(DATA_PATH . '/exam_results.csv', $id, $data)) {
            flash_set('success', 'Result updated successfully!');
        } else {
            flash_set('danger', 'Failed to update result.');
        }
        redirect('exam_results');
    }
    
    if ($action === 'delete_result') {
        $id = post('id', '');
        $result = csv_find_by_id(DATA_PATH . '/exam_results.csv', $id);
        if ($result && !empty($result['result_image'])) {
            delete_upload($result['result_image']);
        }
        if (csv_delete_by_id(DATA_PATH . '/exam_results.csv', $id)) {
            flash_set('success', 'Result deleted successfully!');
        } else {
            flash_set('danger', 'Failed to delete result.');
        }
        redirect('exam_results');
    }
    
    // Export Results
    if ($action === 'export') {
        $exportExam = post('export_exam', '');
        $exportData = $exportExam ? array_filter($results, fn($r) => $r['exam_id'] === $exportExam) : $results;
        
        $filename = 'exam_results_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, ['Student Name', 'Class', 'Section', 'Exam Subject', 'Exam Date', 'Total Marks', 'Marks Obtained', 'Grade', 'Remarks']);
        
        foreach ($exportData as $r) {
            $student = get_student_by_id($students, $r['student_id']);
            $exam = get_exam_by_id($exams, $r['exam_id']);
            fputcsv($output, [
                $student['name'] ?? 'Unknown',
                $student['class'] ?? '',
                $student['section'] ?? '',
                $exam['subject'] ?? 'Unknown',
                $exam['date'] ?? '',
                $exam['marks'] ?? '100',
                $r['marks_obtained'] ?? '',
                $r['grade'] ?? '',
                $r['remarks'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    // Import Results
    if ($action === 'import') {
        $importExam = post('import_exam', '');
        
        if (empty($importExam)) {
            flash_set('danger', 'Please select an exam for import.');
            redirect('exam_results');
        }
        
        if (empty($_FILES['import_file']['name'])) {
            flash_set('danger', 'Please select a CSV file to import.');
            redirect('exam_results');
        }
        
        $file = $_FILES['import_file']['tmp_name'];
        $handle = fopen($file, 'r');
        
        if (!$handle) {
            flash_set('danger', 'Failed to open import file.');
            redirect('exam_results');
        }
        
        // Skip header
        $header = fgetcsv($handle);
        
        $imported = 0;
        $skipped = 0;
        
        // Refresh results
        $results = csv_read_all(DATA_PATH . '/exam_results.csv');
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) continue;
            
            $studentName = trim($row[0] ?? '');
            $studentClass = trim($row[1] ?? '');
            $studentSection = trim($row[2] ?? '');
            $marksObtained = trim($row[6] ?? '');
            $grade = trim($row[7] ?? '');
            $remarks = trim($row[8] ?? '');
            
            if (empty($studentName) || $marksObtained === '') continue;
            
            // Find student by name and class
            $studentId = null;
            foreach ($students as $s) {
                if (strtolower(trim($s['name'])) === strtolower($studentName) && 
                    (empty($studentClass) || $s['class'] === $studentClass)) {
                    $studentId = $s['id'];
                    break;
                }
            }
            
            if (!$studentId) {
                $skipped++;
                continue;
            }
            
            // Check if already exists
            $exists = false;
            foreach ($results as $r) {
                if ($r['exam_id'] === $importExam && $r['student_id'] === $studentId) {
                    $exists = true;
                    break;
                }
            }
            
            if ($exists) {
                $skipped++;
                continue;
            }
            
            $data = [
                'exam_id' => $importExam,
                'student_id' => $studentId,
                'marks_obtained' => $marksObtained,
                'grade' => $grade,
                'remarks' => $remarks,
                'result_image' => '',
                'uploaded_by' => $u['id'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            csv_insert(DATA_PATH . '/exam_results.csv', $data);
            $imported++;
        }
        
        fclose($handle);
        
        flash_set('success', "$imported results imported, $skipped skipped (not found/duplicate).");
        redirect('exam_results');
    }
}

// Filter by exam
$selectedExam = get('exam', '');
$filteredResults = $selectedExam ? array_filter($results, fn($r) => $r['exam_id'] === $selectedExam) : $results;

// Helper functions
function get_exam_by_id($exams, $id) {
    foreach ($exams as $e) {
        if ((string)$e['id'] === (string)$id) return $e;
    }
    return null;
}

function get_student_by_id($students, $id) {
    foreach ($students as $s) {
        if ((string)$s['id'] === (string)$id) return $s;
    }
    return null;
}

// Build JSON data for JavaScript filtering
$studentsJson = json_encode(array_values($students));
$resultsJson = json_encode(array_values($results));
$examsJson = json_encode(array_values($exams));
$classesJson = json_encode(array_values($allClasses));

$title = 'Exam Results';
$active = 'exam-results';
$content = function () use ($exams, $students, $filteredResults, $selectedExam, $allClasses, $results, $studentsJson, $resultsJson, $examsJson, $classesJson) {
?>
<style>
/* Exam Results Premium Styles */
.results-hero {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.15) 0%, rgba(168, 85, 247, 0.15) 50%, rgba(192, 132, 252, 0.15) 100%);
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
    background: radial-gradient(circle, rgba(139, 92, 246, 0.1) 0%, transparent 70%);
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

/* Stats */
.result-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.result-stat {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.result-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(168, 85, 247, 0.2));
    color: #c4b5fd;
}
.result-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
}
.result-stat-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
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
    max-width: 300px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    border-radius: 10px;
}
.filter-bar .form-select option {
    background: #1e1e2e;
}

/* Results Table */
.results-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    overflow: hidden;
}
.results-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}
.results-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.results-table {
    width: 100%;
}
.results-table th {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: rgba(255,255,255,0.4);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.02);
}
.results-table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    vertical-align: middle;
    color: #fff;
}
.results-table tr:hover td {
    background: rgba(255,255,255,0.02);
}

.student-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.student-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a78bfa;
    font-weight: 600;
}
.student-name {
    font-weight: 600;
}
.student-class {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
}

.marks-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}
.marks-badge.pass {
    background: rgba(34, 197, 94, 0.15);
    color: #6ee7b7;
}
.marks-badge.fail {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}

.grade-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    background: rgba(99, 102, 241, 0.15);
    color: #a78bfa;
}

.result-image-thumb {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid rgba(255,255,255,0.1);
    transition: all 0.2s ease;
}
.result-image-thumb:hover {
    transform: scale(1.1);
    border-color: rgba(139, 92, 246, 0.5);
}

.action-btns {
    display: flex;
    gap: 0.5rem;
}
.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.action-btn.edit {
    background: rgba(245, 158, 11, 0.15);
    color: #fcd34d;
}
.action-btn.edit:hover {
    background: rgba(245, 158, 11, 0.25);
}
.action-btn.delete {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}
.action-btn.delete:hover {
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
    border-color: rgba(139, 92, 246, 0.5);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    color: #fff;
}
.form-control::placeholder {
    color: rgba(255,255,255,0.3);
}
.form-select option {
    background: #1e1e2e;
}

/* Bulk Entry */
.bulk-entry-row {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    padding: 0.75rem;
    background: rgba(255,255,255,0.02);
    border-radius: 10px;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}
.bulk-entry-row .form-select,
.bulk-entry-row .form-control {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
}
.bulk-entry-row .remove-row-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
    border-radius: 8px;
    cursor: pointer;
    flex-shrink: 0;
}
.bulk-entry-row .remove-row-btn:hover {
    background: rgba(239, 68, 68, 0.25);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}
.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: rgba(139, 92, 246, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #c4b5fd;
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

/* Pending Count Badge */
.pending-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(245, 158, 11, 0.15);
    color: #fcd34d;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
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
        <i class="bi bi-award"></i>
        Exam Results Management
    </div>
    <div class="results-subtitle">Upload and manage student exam results with marksheet images.</div>
</div>

<!-- Stats -->
<div class="result-stats">
    <div class="result-stat">
        <div class="result-stat-icon"><i class="bi bi-file-earmark-text"></i></div>
        <div>
            <div class="result-stat-value"><?= count($filteredResults) ?></div>
            <div class="result-stat-label">Total Results</div>
        </div>
    </div>
    <div class="result-stat">
        <div class="result-stat-icon"><i class="bi bi-journal-check"></i></div>
        <div>
            <div class="result-stat-value"><?= count($exams) ?></div>
            <div class="result-stat-label">Exams</div>
        </div>
    </div>
    <div class="ms-auto d-flex gap-2 flex-wrap">
        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#importExportModal">
            <i class="bi bi-arrow-left-right me-1"></i>Import/Export
        </button>
        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#bulkAddModal">
            <i class="bi bi-grid me-1"></i>Bulk Entry
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResultModal">
            <i class="bi bi-plus-lg me-1"></i>Add Result
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <select class="form-select" onchange="window.location.href='?exam='+this.value">
        <option value="">All Exams</option>
        <?php foreach ($exams as $exam): ?>
            <option value="<?= e($exam['id']) ?>" <?= $selectedExam === $exam['id'] ? 'selected' : '' ?>>
                <?= e($exam['subject']) ?> - Class <?= e($exam['class']) ?> (<?= e(date('d M Y', strtotime($exam['date']))) ?>)
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Results Table -->
<div class="results-card animate-in">
    <div class="results-card-header">
        <div class="results-card-title">
            <i class="bi bi-list-ul"></i>
            Results List
        </div>
    </div>
    
    <?php if (empty($filteredResults)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="bi bi-clipboard-x"></i></div>
            <div class="empty-state-title">No Results Found</div>
            <div class="empty-state-desc">Start by adding exam results for students.</div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Exam</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredResults as $result):
                        $student = get_student_by_id($students, $result['student_id']);
                        $exam = get_exam_by_id($exams, $result['exam_id']);
                        $marks = (int)($result['marks_obtained'] ?? 0);
                        $totalMarks = (int)($exam['marks'] ?? 100);
                        $isPassing = $marks >= ($totalMarks * 0.33);
                    ?>
                        <tr>
                            <td>
                                <div class="student-info">
                                    <div class="student-avatar">
                                        <?= strtoupper(substr($student['name'] ?? 'S', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="student-name"><?= e($student['name'] ?? 'Unknown') ?></div>
                                        <div class="student-class">Class <?= e($student['class'] ?? '-') ?>-<?= e($student['section'] ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= e($exam['subject'] ?? 'Unknown') ?></div>
                                <div class="text-muted small"><?= e(date('d M Y', strtotime($exam['date'] ?? 'now'))) ?></div>
                            </td>
                            <td>
                                <span class="marks-badge <?= $isPassing ? 'pass' : 'fail' ?>">
                                    <?= e($result['marks_obtained']) ?>/<?= e($exam['marks'] ?? '100') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($result['grade'])): ?>
                                    <span class="grade-badge"><?= e($result['grade']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($result['result_image'])): ?>
                                    <img src="<?= e(base_url('uploads/' . $result['result_image'])) ?>" 
                                         class="result-image-thumb" 
                                         onclick="showImage('<?= e(base_url('uploads/' . $result['result_image'])) ?>')"
                                         alt="Result">
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn edit" onclick="editResult(<?= e(json_encode($result)) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="action-btn delete" onclick="deleteResult('<?= e($result['id']) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add Result Modal -->
<div class="modal fade" id="addResultModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add_result">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Result</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Exam *</label>
                            <select name="exam_id" id="addExamSelect" class="form-select" required onchange="updateStudentDropdown()">
                                <option value="">-- Select Exam --</option>
                                <?php foreach ($exams as $exam): ?>
                                    <option value="<?= e($exam['id']) ?>" data-class="<?= e($exam['class']) ?>">
                                        <?= e($exam['subject']) ?> - Class <?= e($exam['class']) ?> (<?= e(date('d M', strtotime($exam['date']))) ?>) - <?= e($exam['marks']) ?> marks
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Class Filter</label>
                            <select id="addClassFilter" class="form-select" onchange="updateStudentDropdown()">
                                <option value="">All Classes</option>
                                <?php foreach ($allClasses as $key => $c): ?>
                                    <option value="<?= e($c['class']) ?>|<?= e($c['section']) ?>">
                                        Class <?= e($c['class']) ?><?= !empty($c['section']) ? '-' . $c['section'] : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label d-flex align-items-center gap-2">
                                Pending
                                <span id="pendingCount" class="pending-badge" style="display: none;">
                                    <i class="bi bi-clock"></i>
                                    <span id="pendingCountValue">0</span>
                                </span>
                            </label>
                            <div class="form-control" style="background: transparent; border: none; padding-left: 0;">
                                <small class="text-muted">Students without results</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Student *</label>
                            <select name="student_id" id="addStudentSelect" class="form-select" required>
                                <option value="">-- Select Exam First --</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Marks Obtained *</label>
                            <input type="number" name="marks_obtained" class="form-control" required min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Grade</label>
                            <select name="grade" class="form-select">
                                <option value="">-- Auto --</option>
                                <option value="A+">A+</option>
                                <option value="A">A</option>
                                <option value="B+">B+</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Result Image (Optional)</label>
                            <input type="file" name="result_image" class="form-control" accept="image/*">
                            <div class="form-text">Upload marksheet or result scan (Max 5MB)</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Result</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Add Modal -->
<div class="modal fade" id="bulkAddModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="bulk_add">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-grid me-2"></i>Bulk Entry</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Exam *</label>
                            <select name="exam_id" id="bulkExamSelect" class="form-select" required onchange="updateBulkStudents()">
                                <option value="">-- Select Exam --</option>
                                <?php foreach ($exams as $exam): ?>
                                    <option value="<?= e($exam['id']) ?>" data-class="<?= e($exam['class']) ?>">
                                        <?= e($exam['subject']) ?> - Class <?= e($exam['class']) ?> (<?= e(date('d M', strtotime($exam['date']))) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-flex align-items-center gap-2">
                                Pending Students
                                <span id="bulkPendingCount" class="pending-badge" style="display: none;">
                                    <i class="bi bi-clock"></i>
                                    <span id="bulkPendingValue">0</span>
                                </span>
                            </label>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="autoFillPendingStudents()">
                                <i class="bi bi-magic me-1"></i>Auto-fill Pending Students
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label mb-2">Student Marks</label>
                        <div class="small text-muted mb-2">
                            <i class="bi bi-info-circle me-1"></i>Students with existing results will be skipped.
                        </div>
                        <div id="bulkEntryContainer">
                            <!-- Will be populated dynamically -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-light mt-2" onclick="addBulkRow()">
                            <i class="bi bi-plus me-1"></i>Add Row
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save All</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import/Export Modal -->
<div class="modal fade" id="importExportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right me-2"></i>Import / Export Results</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Export Section -->
                <div class="mb-4">
                    <h6 class="text-white mb-3"><i class="bi bi-download me-2"></i>Export Results</h6>
                    <form method="POST" class="d-flex gap-2 align-items-end">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="export">
                        <div class="flex-grow-1">
                            <label class="form-label">Filter by Exam (Optional)</label>
                            <select name="export_exam" class="form-select">
                                <option value="">All Exams</option>
                                <?php foreach ($exams as $exam): ?>
                                    <option value="<?= e($exam['id']) ?>">
                                        <?= e($exam['subject']) ?> - Class <?= e($exam['class']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-download me-1"></i>Export CSV
                        </button>
                    </form>
                </div>
                
                <hr style="border-color: rgba(255,255,255,0.1);">
                
                <!-- Import Section -->
                <div>
                    <h6 class="text-white mb-3"><i class="bi bi-upload me-2"></i>Import Results</h6>
                    <form method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="import">
                        <div class="mb-3">
                            <label class="form-label">Select Exam *</label>
                            <select name="import_exam" class="form-select" required>
                                <option value="">-- Select Exam --</option>
                                <?php foreach ($exams as $exam): ?>
                                    <option value="<?= e($exam['id']) ?>">
                                        <?= e($exam['subject']) ?> - Class <?= e($exam['class']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CSV File *</label>
                            <input type="file" name="import_file" class="form-control" accept=".csv" required>
                            <div class="form-text">
                                CSV format: Student Name, Class, Section, Exam Subject, Exam Date, Total Marks, Marks Obtained, Grade, Remarks
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>Import Results
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Result Modal -->
<div class="modal fade" id="editResultModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="edit_result">
                <input type="hidden" name="id" id="editResultId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Result</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Marks Obtained *</label>
                            <input type="number" name="marks_obtained" id="editMarks" class="form-control" required min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Grade</label>
                            <select name="grade" id="editGrade" class="form-select">
                                <option value="">-- Auto --</option>
                                <option value="A+">A+</option>
                                <option value="A">A</option>
                                <option value="B+">B+</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" id="editRemarks" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Update Result Image</label>
                            <input type="file" name="result_image" class="form-control" accept="image/*">
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
<div class="modal fade" id="deleteResultModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_result">
                <input type="hidden" name="id" id="deleteResultId">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this result?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img id="previewImage" src="" style="max-width: 100%; max-height: 90vh; border-radius: 12px;">
            </div>
        </div>
    </div>
</div>

<script>
// Data from PHP
const allStudents = <?= $studentsJson ?>;
const allResults = <?= $resultsJson ?>;
const allExams = <?= $examsJson ?>;

function getExamById(id) {
    return allExams.find(e => String(e.id) === String(id));
}

function getStudentsWithoutResults(examId, classFilter = '', sectionFilter = '') {
    const examResults = allResults.filter(r => String(r.exam_id) === String(examId));
    const studentsWithResults = new Set(examResults.map(r => String(r.student_id)));
    
    return allStudents.filter(s => {
        if (studentsWithResults.has(String(s.id))) return false;
        if (classFilter && s.class !== classFilter) return false;
        if (sectionFilter && s.section !== sectionFilter) return false;
        return true;
    });
}

function updateStudentDropdown() {
    const examSelect = document.getElementById('addExamSelect');
    const studentSelect = document.getElementById('addStudentSelect');
    const classFilter = document.getElementById('addClassFilter');
    const pendingCount = document.getElementById('pendingCount');
    const pendingValue = document.getElementById('pendingCountValue');
    
    const examId = examSelect.value;
    
    if (!examId) {
        studentSelect.innerHTML = '<option value="">-- Select Exam First --</option>';
        pendingCount.style.display = 'none';
        return;
    }
    
    // Get class/section from filter
    let classVal = '', sectionVal = '';
    if (classFilter.value) {
        const parts = classFilter.value.split('|');
        classVal = parts[0] || '';
        sectionVal = parts[1] || '';
    }
    
    // Get exam class if no filter set
    const exam = getExamById(examId);
    if (!classVal && exam) {
        classVal = exam.class || '';
    }
    
    const pendingStudents = getStudentsWithoutResults(examId, classVal, sectionVal);
    
    // Update pending count
    pendingCount.style.display = 'inline-flex';
    pendingValue.textContent = pendingStudents.length;
    
    // Update student dropdown
    studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
    pendingStudents.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = `${s.name} (Class ${s.class}${s.section ? '-' + s.section : ''})`;
        studentSelect.appendChild(opt);
    });
}

function updateBulkStudents() {
    const examSelect = document.getElementById('bulkExamSelect');
    const pendingCount = document.getElementById('bulkPendingCount');
    const pendingValue = document.getElementById('bulkPendingValue');
    
    const examId = examSelect.value;
    
    if (!examId) {
        pendingCount.style.display = 'none';
        return;
    }
    
    const exam = getExamById(examId);
    const classVal = exam ? exam.class : '';
    const pendingStudents = getStudentsWithoutResults(examId, classVal);
    
    pendingCount.style.display = 'inline-flex';
    pendingValue.textContent = pendingStudents.length;
}

function autoFillPendingStudents() {
    const examSelect = document.getElementById('bulkExamSelect');
    const container = document.getElementById('bulkEntryContainer');
    
    const examId = examSelect.value;
    if (!examId) {
        alert('Please select an exam first.');
        return;
    }
    
    const exam = getExamById(examId);
    const classVal = exam ? exam.class : '';
    const pendingStudents = getStudentsWithoutResults(examId, classVal);
    
    if (pendingStudents.length === 0) {
        alert('No pending students found for this exam.');
        return;
    }
    
    // Clear existing rows
    container.innerHTML = '';
    
    // Add rows for all pending students
    pendingStudents.forEach((student, index) => {
        const row = createBulkRow(student.id);
        container.appendChild(row);
    });
}

function createBulkRow(preselectedStudentId = '') {
    const row = document.createElement('div');
    row.className = 'bulk-entry-row';
    
    let studentOptions = '<option value="">-- Student --</option>';
    allStudents.forEach(s => {
        const selected = String(s.id) === String(preselectedStudentId) ? 'selected' : '';
        studentOptions += `<option value="${s.id}" ${selected}>${s.name} (${s.class}${s.section ? '-' + s.section : ''})</option>`;
    });
    
    row.innerHTML = `
        <select name="student_ids[]" class="form-select" style="flex: 2;">${studentOptions}</select>
        <input type="number" name="marks[]" class="form-control" placeholder="Marks" style="flex: 1;">
        <select name="grades[]" class="form-select" style="flex: 1;">
            <option value="">Grade</option>
            <option value="A+">A+</option>
            <option value="A">A</option>
            <option value="B+">B+</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="F">F</option>
        </select>
        <input type="file" name="result_images[]" class="form-control" accept="image/*" style="flex: 1.5;">
        <button type="button" class="remove-row-btn" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
    `;
    
    return row;
}

function addBulkRow() {
    const container = document.getElementById('bulkEntryContainer');
    container.appendChild(createBulkRow());
}

// Add initial empty rows
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('bulkEntryContainer');
    for (let i = 0; i < 3; i++) {
        container.appendChild(createBulkRow());
    }
});

function editResult(data) {
    document.getElementById('editResultId').value = data.id || '';
    document.getElementById('editMarks').value = data.marks_obtained || '';
    document.getElementById('editGrade').value = data.grade || '';
    document.getElementById('editRemarks').value = data.remarks || '';
    new bootstrap.Modal(document.getElementById('editResultModal')).show();
}

function deleteResult(id) {
    document.getElementById('deleteResultId').value = id;
    new bootstrap.Modal(document.getElementById('deleteResultModal')).show();
}

function showImage(url) {
    document.getElementById('previewImage').src = url;
    new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
}
</script>

<?php
};
include __DIR__ . '/views/partials/layout.php';
