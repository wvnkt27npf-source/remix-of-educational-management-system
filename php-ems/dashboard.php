<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('dashboard.read');

$students = csv_read_all(DATA_PATH . '/students.csv');
$teachers = csv_read_all(DATA_PATH . '/teachers.csv');
$users = csv_read_all(DATA_PATH . '/users.csv');
$events = recent_events(5);
$exams = upcoming_exams(5);

$title = 'Dashboard';
$active = 'dashboard';
$content = function () use ($students, $teachers, $users, $events, $exams) {
?>
<style>
/* Dashboard Premium Styles */
.dashboard-hero {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(168, 85, 247, 0.15) 50%, rgba(236, 72, 153, 0.15) 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    overflow: hidden;
}
.dashboard-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
    animation: pulse-slow 4s ease-in-out infinite;
}
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 0.8; }
}
.dashboard-greeting {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.7) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}
.dashboard-subtitle {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
}

/* Stat Cards */
.stat-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.stat-card:hover {
    transform: translateY(-5px);
    border-color: rgba(255,255,255,0.15);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 16px 16px 0 0;
}
.stat-card.students::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.stat-card.teachers::before { background: linear-gradient(90deg, #10b981, #34d399); }
.stat-card.exams::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.stat-card.events::before { background: linear-gradient(90deg, #ec4899, #f472b6); }

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}
.stat-card.students .stat-icon { background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2)); color: #a78bfa; }
.stat-card.teachers .stat-icon { background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(52, 211, 153, 0.2)); color: #6ee7b7; }
.stat-card.exams .stat-icon { background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(251, 191, 36, 0.2)); color: #fcd34d; }
.stat-card.events .stat-icon { background: linear-gradient(135deg, rgba(236, 72, 153, 0.2), rgba(244, 114, 182, 0.2)); color: #f9a8d4; }

.stat-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
    margin-bottom: 0.25rem;
}
.stat-label {
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.stat-trend {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 20px;
    margin-top: 0.75rem;
}
.stat-trend.up { background: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
.stat-trend.neutral { background: rgba(148, 163, 184, 0.15); color: #94a3b8; }

/* Content Cards */
.content-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    overflow: hidden;
    height: 100%;
}
.content-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.content-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.content-card-title i {
    font-size: 1.1rem;
    opacity: 0.7;
}
.content-card-body {
    padding: 1.25rem 1.5rem;
}

/* Exam Table */
.exam-table {
    width: 100%;
}
.exam-table th {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: rgba(255,255,255,0.4);
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.exam-table td {
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    vertical-align: middle;
}
.exam-table tr:last-child td {
    border-bottom: none;
}
.exam-table tr:hover td {
    background: rgba(255,255,255,0.02);
}
.exam-subject {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.exam-subject-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
    color: #a78bfa;
}
.exam-subject-name {
    font-weight: 600;
    color: #fff;
}
.exam-class-badge {
    display: inline-block;
    padding: 4px 10px;
    background: rgba(99, 102, 241, 0.15);
    color: #a78bfa;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
}
.exam-date {
    color: rgba(255,255,255,0.6);
    font-size: 0.9rem;
}
.exam-marks-badge {
    display: inline-block;
    padding: 4px 12px;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(52, 211, 153, 0.15));
    color: #6ee7b7;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Event Cards */
.event-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 0.75rem;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.04);
    transition: all 0.2s ease;
}
.event-item:hover {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.08);
}
.event-item:last-child {
    margin-bottom: 0;
}
.event-date-box {
    min-width: 50px;
    text-align: center;
    padding: 0.5rem;
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(244, 114, 182, 0.15));
    border-radius: 10px;
}
.event-date-day {
    font-size: 1.25rem;
    font-weight: 700;
    color: #f9a8d4;
    line-height: 1;
}
.event-date-month {
    font-size: 0.7rem;
    color: rgba(249, 168, 212, 0.7);
    text-transform: uppercase;
    font-weight: 600;
    margin-top: 2px;
}
.event-content {
    flex: 1;
    min-width: 0;
}
.event-title {
    font-weight: 600;
    color: #fff;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.event-desc {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.5);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}
.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    color: #fff;
    text-decoration: none;
    transition: all 0.2s ease;
}
.quick-action-btn:hover {
    background: rgba(255,255,255,0.06);
    border-color: rgba(255,255,255,0.1);
    color: #fff;
    transform: translateX(4px);
}
.quick-action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}
.quick-action-btn.students .quick-action-icon { background: rgba(99, 102, 241, 0.15); color: #a78bfa; }
.quick-action-btn.teachers .quick-action-icon { background: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
.quick-action-btn.exams .quick-action-icon { background: rgba(245, 158, 11, 0.15); color: #fcd34d; }
.quick-action-btn.events .quick-action-icon { background: rgba(236, 72, 153, 0.15); color: #f9a8d4; }

.quick-action-text {
    font-weight: 500;
    font-size: 0.9rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem;
    color: rgba(255,255,255,0.4);
}
.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* Animations */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-in {
    animation: fadeInUp 0.5s ease forwards;
}
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }
.delay-4 { animation-delay: 0.4s; }
</style>

<!-- Dashboard Hero -->
<div class="dashboard-hero animate-in">
    <div class="dashboard-greeting">
        <?php
        $hour = (int)date('H');
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
        ?>
        <?= $greeting ?>, <?= e($_SESSION['user']['name'] ?? 'Admin') ?>! 👋
    </div>
    <div class="dashboard-subtitle">Here's what's happening at your institution today.</div>
</div>

<!-- Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3 animate-in delay-1">
        <div class="stat-card students">
            <div class="stat-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <div class="stat-value"><?= (int)count($students) ?></div>
            <div class="stat-label">Total Students</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up"></i> Active</div>
        </div>
    </div>
    <div class="col-6 col-lg-3 animate-in delay-2">
        <div class="stat-card teachers">
            <div class="stat-icon"><i class="bi bi-person-workspace"></i></div>
            <div class="stat-value"><?= (int)count($teachers) ?></div>
            <div class="stat-label">Total Teachers</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up"></i> Active</div>
        </div>
    </div>
    <div class="col-6 col-lg-3 animate-in delay-3">
        <div class="stat-card exams">
            <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
            <div class="stat-value"><?= (int)count($exams) ?></div>
            <div class="stat-label">Upcoming Exams</div>
            <div class="stat-trend neutral"><i class="bi bi-clock"></i> Scheduled</div>
        </div>
    </div>
    <div class="col-6 col-lg-3 animate-in delay-4">
        <div class="stat-card events">
            <div class="stat-icon"><i class="bi bi-megaphone-fill"></i></div>
            <div class="stat-value"><?= (int)count($events) ?></div>
            <div class="stat-label">Recent Events</div>
            <div class="stat-trend neutral"><i class="bi bi-calendar-event"></i> Posted</div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row g-3">
    <!-- Upcoming Exams -->
    <div class="col-12 col-lg-7">
        <div class="content-card animate-in delay-2">
            <div class="content-card-header">
                <div class="content-card-title">
                    <i class="bi bi-calendar-check"></i> Upcoming Examinations
                </div>
                <?php if (has_permission('exams.write')): ?>
                    <a class="btn btn-sm btn-primary" href="<?= e(base_url('exams')) ?>">
                        <i class="bi bi-gear me-1"></i> Manage
                    </a>
                <?php endif; ?>
            </div>
            <div class="content-card-body">
                <?php if (!$exams): ?>
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <div>No upcoming exams scheduled</div>
                    </div>
                <?php else: ?>
                    <table class="exam-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Date</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $e): ?>
                                <tr>
                                    <td>
                                        <div class="exam-subject">
                                            <div class="exam-subject-icon"><i class="bi bi-book"></i></div>
                                            <span class="exam-subject-name"><?= e($e['subject']) ?></span>
                                        </div>
                                    </td>
                                    <td><span class="exam-class-badge"><?= e($e['class']) ?></span></td>
                                    <td class="exam-date"><?= e(date('d M Y', strtotime($e['date']))) ?></td>
                                    <td><span class="exam-marks-badge"><?= e($e['marks']) ?> marks</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Events & Quick Actions -->
    <div class="col-12 col-lg-5">
        <!-- Latest Events -->
        <div class="content-card animate-in delay-3 mb-3">
            <div class="content-card-header">
                <div class="content-card-title">
                    <i class="bi bi-megaphone"></i> Latest Events
                </div>
                <a class="btn btn-sm btn-outline-light" href="<?= e(base_url('events')) ?>">View All</a>
            </div>
            <div class="content-card-body">
                <?php if (!$events): ?>
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <div>No events posted yet</div>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($events, 0, 3) as $ev): 
                        $dateObj = strtotime($ev['date']);
                    ?>
                        <div class="event-item">
                            <div class="event-date-box">
                                <div class="event-date-day"><?= date('d', $dateObj) ?></div>
                                <div class="event-date-month"><?= date('M', $dateObj) ?></div>
                            </div>
                            <div class="event-content">
                                <div class="event-title"><?= e($ev['title']) ?></div>
                                <div class="event-desc"><?= e(mb_strimwidth((string)$ev['description'], 0, 80, '…')) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <?php if (has_permission('*')): ?>
        <div class="content-card animate-in delay-4">
            <div class="content-card-header">
                <div class="content-card-title">
                    <i class="bi bi-lightning"></i> Quick Actions
                </div>
            </div>
            <div class="content-card-body">
                <div class="quick-actions">
                    <a href="<?= e(base_url('students?action=add')) ?>" class="quick-action-btn students">
                        <div class="quick-action-icon"><i class="bi bi-person-plus"></i></div>
                        <div class="quick-action-text">Add Student</div>
                    </a>
                    <a href="<?= e(base_url('teachers?action=add')) ?>" class="quick-action-btn teachers">
                        <div class="quick-action-icon"><i class="bi bi-person-plus-fill"></i></div>
                        <div class="quick-action-text">Add Teacher</div>
                    </a>
                    <a href="<?= e(base_url('exams?action=add')) ?>" class="quick-action-btn exams">
                        <div class="quick-action-icon"><i class="bi bi-journal-plus"></i></div>
                        <div class="quick-action-text">Schedule Exam</div>
                    </a>
                    <a href="<?= e(base_url('events?action=add')) ?>" class="quick-action-btn events">
                        <div class="quick-action-icon"><i class="bi bi-calendar-plus"></i></div>
                        <div class="quick-action-text">Post Event</div>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
};

include __DIR__ . '/views/partials/layout.php';
