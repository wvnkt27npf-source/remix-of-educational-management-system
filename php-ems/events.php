<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('events.read');

$events = csv_read_all(DATA_PATH . '/events.csv');
$canWrite = has_permission('events.write') || has_permission('*');

// Handle form submissions
if (request_method() === 'POST' && $canWrite) {
    csrf_verify_or_die();
    $action = post('action', '');
    
    if ($action === 'add') {
        $data = [
            'title' => trim(post('title', '')),
            'date' => trim(post('date', '')),
            'description' => trim(post('description', ''))
        ];
        
        if (empty($data['title']) || empty($data['date'])) {
            flash_set('danger', 'Title and date are required.');
        } else {
            csv_insert(DATA_PATH . '/events.csv', $data);
            flash_set('success', 'Event posted successfully!');
            
            // Check if notification should be sent
            if (post('send_notification') === '1') {
                $recipients = post('recipients', []);
                if (!empty($recipients)) {
                    $message = "📢 *New Event Posted*\n\n";
                    $message .= "Title: {$data['title']}\n";
                    $message .= "Date: " . date('d M Y', strtotime($data['date'])) . "\n";
                    if (!empty($data['description'])) {
                        $message .= "Details: {$data['description']}\n";
                    }
                    $message .= "\nStay tuned for more updates! 🎉";
                    
                    $result = send_notification_to_recipients($message, $recipients);
                    if ($result['telegram'] > 0 || $result['whatsapp'] > 0) {
                        flash_set('success', 'Event posted and notifications sent!');
                    }
                }
            }
        }
        redirect('events');
    }
    
    if ($action === 'edit') {
        $id = post('id', '');
        $data = [
            'title' => trim(post('title', '')),
            'date' => trim(post('date', '')),
            'description' => trim(post('description', ''))
        ];
        
        if (csv_update_by_id(DATA_PATH . '/events.csv', $id, $data)) {
            flash_set('success', 'Event updated successfully!');
        } else {
            flash_set('danger', 'Failed to update event.');
        }
        redirect('events');
    }
    
    if ($action === 'delete') {
        $id = post('id', '');
        if (csv_delete_by_id(DATA_PATH . '/events.csv', $id)) {
            flash_set('success', 'Event deleted successfully!');
        } else {
            flash_set('danger', 'Failed to delete event.');
        }
        redirect('events');
    }
}

// Sort by date descending
usort($events, fn($a, $b) => strcmp($b['date'], $a['date']));

// Get featured event (most recent)
$featuredEvent = !empty($events) ? $events[0] : null;

$title = 'Events & Announcements';
$active = 'events';
$content = function () use ($events, $featuredEvent, $canWrite) {
?>
<style>
/* Events Page Premium Styles */
.events-hero {
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.15) 0%, rgba(244, 114, 182, 0.15) 50%, rgba(251, 146, 203, 0.15) 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    overflow: hidden;
}
.events-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(236, 72, 153, 0.1) 0%, transparent 70%);
    animation: pulse-slow 4s ease-in-out infinite;
}
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 0.8; }
}
.events-title {
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
.events-subtitle {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
}

/* Featured Event */
.featured-event {
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.1), rgba(244, 114, 182, 0.1));
    border: 1px solid rgba(236, 72, 153, 0.2);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.featured-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, #ec4899, #f472b6);
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.featured-date {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}
.featured-date-box {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.2), rgba(244, 114, 182, 0.2));
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.featured-date-day {
    font-size: 1.75rem;
    font-weight: 800;
    color: #f9a8d4;
    line-height: 1;
}
.featured-date-month {
    font-size: 0.8rem;
    color: rgba(249, 168, 212, 0.8);
    text-transform: uppercase;
    font-weight: 600;
}
.featured-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.75rem;
}
.featured-desc {
    color: rgba(255,255,255,0.6);
    font-size: 1rem;
    line-height: 1.6;
}

/* Timeline */
.events-timeline {
    position: relative;
    padding-left: 2rem;
}
.events-timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, rgba(236, 72, 153, 0.5), rgba(244, 114, 182, 0.2));
}
.event-item {
    position: relative;
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    transition: all 0.3s ease;
}
.event-item:hover {
    transform: translateX(5px);
    border-color: rgba(255,255,255,0.15);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
.event-item::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 1.75rem;
    width: 12px;
    height: 12px;
    background: linear-gradient(135deg, #ec4899, #f472b6);
    border-radius: 50%;
    transform: translateX(-5px);
}
.event-item.past::before {
    background: rgba(107, 114, 128, 0.5);
}
.event-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}
.event-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    margin-right: 1rem;
}
.event-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
    white-space: nowrap;
}
.event-date i {
    color: #f9a8d4;
}
.event-desc {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
    line-height: 1.6;
}
.event-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.event-action-btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.event-action-btn.edit {
    background: rgba(245, 158, 11, 0.15);
    color: #fcd34d;
}
.event-action-btn.edit:hover {
    background: rgba(245, 158, 11, 0.25);
}
.event-action-btn.delete {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}
.event-action-btn.delete:hover {
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
    border-color: rgba(236, 72, 153, 0.5);
    box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
    color: #fff;
}
.form-control::placeholder {
    color: rgba(255,255,255,0.3);
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
    background: rgba(236, 72, 153, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #f9a8d4;
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
<div class="events-hero animate-in">
    <div class="events-title">
        <i class="bi bi-megaphone"></i>
        Events & Announcements
    </div>
    <div class="events-subtitle">Stay updated with the latest happenings at your institution.</div>
</div>

<?php if ($canWrite): ?>
    <div class="mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="bi bi-plus-lg me-2"></i>Post New Event
        </button>
    </div>
<?php endif; ?>

<?php if (empty($events)): ?>
    <div class="empty-state animate-in">
        <div class="empty-state-icon"><i class="bi bi-calendar-x"></i></div>
        <div class="empty-state-title">No Events Posted</div>
        <div class="empty-state-desc">There are no events or announcements at this time.</div>
        <?php if ($canWrite): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class="bi bi-plus-lg me-2"></i>Post First Event
            </button>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Featured Event -->
    <?php if ($featuredEvent): 
        $fDate = strtotime($featuredEvent['date']);
    ?>
        <div class="featured-event animate-in">
            <span class="featured-badge"><i class="bi bi-star-fill me-1"></i>Latest</span>
            <div class="featured-date">
                <div class="featured-date-box">
                    <div class="featured-date-day"><?= date('d', $fDate) ?></div>
                    <div class="featured-date-month"><?= date('M', $fDate) ?></div>
                </div>
                <div>
                    <div class="featured-title"><?= e($featuredEvent['title']) ?></div>
                    <div class="featured-desc"><?= e($featuredEvent['description'] ?? '') ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Events Timeline -->
    <div class="events-timeline">
        <?php foreach (array_slice($events, 1) as $event):
            $eventDate = strtotime($event['date']);
            $isPast = $eventDate < strtotime(date('Y-m-d'));
        ?>
            <div class="event-item <?= $isPast ? 'past' : '' ?> animate-in">
                <div class="event-header">
                    <div class="event-title"><?= e($event['title']) ?></div>
                    <div class="event-date">
                        <i class="bi bi-calendar3"></i>
                        <?= date('d M Y', $eventDate) ?>
                    </div>
                </div>
                <?php if (!empty($event['description'])): ?>
                    <div class="event-desc"><?= e($event['description']) ?></div>
                <?php endif; ?>
                
                <?php if ($canWrite): ?>
                    <div class="event-actions">
                        <button class="event-action-btn edit" onclick="editEvent(<?= e(json_encode($event)) ?>)">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </button>
                        <button class="event-action-btn delete" onclick="deleteEvent('<?= e($event['id']) ?>')">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($canWrite): ?>
<!-- Add Event Modal with Notification -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Post New Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Event Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g., Annual Sports Day" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Date *</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Event details..."></textarea>
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
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Post Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editEventId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Event Title *</label>
                            <input type="text" name="title" id="editEventTitle" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Date *</label>
                            <input type="date" name="date" id="editEventDate" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editEventDesc" class="form-control" rows="3"></textarea>
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
<div class="modal fade" id="deleteEventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteEventId">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete this event?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Toggle notification options
document.getElementById('sendNotificationCheck')?.addEventListener('change', function() {
    document.getElementById('recipientOptions').style.display = this.checked ? 'flex' : 'none';
});

function editEvent(event) {
    document.getElementById('editEventId').value = event.id || '';
    document.getElementById('editEventTitle').value = event.title || '';
    document.getElementById('editEventDate').value = event.date || '';
    document.getElementById('editEventDesc').value = event.description || '';
    new bootstrap.Modal(document.getElementById('editEventModal')).show();
}

function deleteEvent(id) {
    document.getElementById('deleteEventId').value = id;
    new bootstrap.Modal(document.getElementById('deleteEventModal')).show();
}
</script>

<?php
};
include __DIR__ . '/views/partials/layout.php';
