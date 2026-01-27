<?php
require_once __DIR__ . '/bootstrap.php';
require_permission('*');

// Handle form submissions
if (request_method() === 'POST') {
    csrf_verify_or_die();
    $action = post('action', '');
    
    if ($action === 'save_email') {
        $settings = [
            'smtp_enabled' => post('smtp_enabled', '0'),
            'smtp_host' => trim(post('smtp_host', '')),
            'smtp_port' => trim(post('smtp_port', '587')),
            'smtp_username' => trim(post('smtp_username', '')),
            'smtp_password' => trim(post('smtp_password', '')),
            'smtp_encryption' => post('smtp_encryption', 'tls'),
            'smtp_from_email' => trim(post('smtp_from_email', '')),
            'smtp_from_name' => trim(post('smtp_from_name', APP_NAME)),
            'smtp_test_email' => trim(post('smtp_test_email', ''))
        ];
        foreach ($settings as $key => $value) {
            save_notification_setting($key, $value, 'email');
        }
        flash_set('success', 'Email settings saved successfully!');
        redirect('notification_settings');
    }
    
    if ($action === 'save_telegram') {
        $settings = [
            'telegram_enabled' => post('telegram_enabled', '0'),
            'telegram_token' => trim(post('telegram_token', '')),
            'telegram_chat_id' => trim(post('telegram_chat_id', ''))
        ];
        foreach ($settings as $key => $value) {
            save_notification_setting($key, $value, 'telegram');
        }
        flash_set('success', 'Telegram settings saved successfully!');
        redirect('notification_settings');
    }
    
    if ($action === 'save_whatsapp') {
        $settings = [
            'whatsapp_enabled' => post('whatsapp_enabled', '0'),
            'ultramsg_instance' => trim(post('ultramsg_instance', '')),
            'ultramsg_token' => trim(post('ultramsg_token', '')),
            'whatsapp_test_phone' => trim(post('test_phone', ''))
        ];
        foreach ($settings as $key => $value) {
            save_notification_setting($key, $value, 'whatsapp');
        }
        flash_set('success', 'WhatsApp settings saved successfully!');
        redirect('notification_settings');
    }
    
    if ($action === 'test_email') {
        $result = send_test_email();
        if ($result['success']) {
            flash_set('success', 'Test email sent successfully!');
        } else {
            flash_set('danger', 'Failed to send test email: ' . $result['error']);
        }
        redirect('notification_settings');
    }
    
    if ($action === 'test_telegram') {
        $result = send_telegram_message('🔔 Test notification from ' . APP_NAME);
        if ($result['success']) {
            flash_set('success', 'Test Telegram message sent successfully!');
        } else {
            flash_set('danger', 'Failed to send Telegram message: ' . $result['error']);
        }
        redirect('notification_settings');
    }
    
    if ($action === 'test_whatsapp') {
        $phone = trim(post('test_phone', ''));
        if (empty($phone)) {
            flash_set('danger', 'Please enter a phone number for testing.');
        } else {
            $result = send_whatsapp_message($phone, '🔔 Test notification from ' . APP_NAME);
            if ($result['success']) {
                flash_set('success', 'Test WhatsApp message sent successfully!');
            } else {
                flash_set('danger', 'Failed to send WhatsApp message: ' . $result['error']);
            }
        }
        redirect('notification_settings');
    }
}

// Get current settings
$emailSettings = [
    'smtp_enabled' => get_notification_setting('smtp_enabled', '0'),
    'smtp_host' => get_notification_setting('smtp_host', ''),
    'smtp_port' => get_notification_setting('smtp_port', '587'),
    'smtp_username' => get_notification_setting('smtp_username', ''),
    'smtp_password' => get_notification_setting('smtp_password', ''),
    'smtp_encryption' => get_notification_setting('smtp_encryption', 'tls'),
    'smtp_from_email' => get_notification_setting('smtp_from_email', ''),
    'smtp_from_name' => get_notification_setting('smtp_from_name', APP_NAME),
    'smtp_test_email' => get_notification_setting('smtp_test_email', '')
];

$telegramSettings = [
    'telegram_enabled' => get_notification_setting('telegram_enabled', '0'),
    'telegram_token' => get_notification_setting('telegram_token', ''),
    'telegram_chat_id' => get_notification_setting('telegram_chat_id', '')
];

$whatsappSettings = [
    'whatsapp_enabled' => get_notification_setting('whatsapp_enabled', '0'),
    'ultramsg_instance' => get_notification_setting('ultramsg_instance', ''),
    'ultramsg_token' => get_notification_setting('ultramsg_token', ''),
    'whatsapp_test_phone' => get_notification_setting('whatsapp_test_phone', '')
];

$title = 'Notification Settings';
$active = 'notification_settings';
$content = function () use ($emailSettings, $telegramSettings, $whatsappSettings) {
?>
<style>
/* Notification Settings Premium Styles */
.notify-hero {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(251, 191, 36, 0.15) 50%, rgba(252, 211, 77, 0.15) 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    position: relative;
    overflow: hidden;
}
.notify-hero::before {
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
.notify-title {
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
.notify-subtitle {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
}

/* Channel Cards */
.channel-card {
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}
.channel-card:hover {
    border-color: rgba(255,255,255,0.15);
}
.channel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.channel-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.channel-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.channel-icon.email { background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(248, 113, 113, 0.2)); color: #fca5a5; }
.channel-icon.telegram { background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(96, 165, 250, 0.2)); color: #93c5fd; }
.channel-icon.whatsapp { background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(74, 222, 128, 0.2)); color: #86efac; }

.channel-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
}
.channel-desc {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.5);
}
.channel-toggle {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.toggle-label {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.6);
}
.form-switch .form-check-input {
    width: 50px;
    height: 26px;
    background-color: rgba(255,255,255,0.1);
    border: none;
    cursor: pointer;
}
.form-switch .form-check-input:checked {
    background-color: #22c55e;
}
.form-switch .form-check-input:focus {
    box-shadow: none;
}

.channel-body {
    padding: 1.5rem;
}
.channel-body.collapsed {
    display: none;
}

/* Form Styles */
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
    color: #fff;
}
.form-text {
    color: rgba(255,255,255,0.4);
    font-size: 0.8rem;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}
.status-badge.enabled {
    background: rgba(34, 197, 94, 0.15);
    color: #6ee7b7;
}
.status-badge.disabled {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}

/* Action Buttons */
.channel-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}

/* Input Group */
.input-group-text {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-right: none;
    color: rgba(255,255,255,0.5);
}
.input-group .form-control {
    border-left: none;
}

/* Password Toggle */
.password-toggle {
    position: relative;
}
.password-toggle .toggle-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(255,255,255,0.4);
    cursor: pointer;
    padding: 0;
}
.password-toggle .toggle-btn:hover {
    color: rgba(255,255,255,0.7);
}

/* Info Card */
.info-card {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}
.info-card-title {
    font-weight: 600;
    color: #93c5fd;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.info-card-text {
    color: rgba(255,255,255,0.6);
    font-size: 0.9rem;
    margin: 0;
}
.info-card-text a {
    color: #93c5fd;
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
<div class="notify-hero animate-in">
    <div class="notify-title">
        <i class="bi bi-bell"></i>
        Notification Settings
    </div>
    <div class="notify-subtitle">Configure email, Telegram, and WhatsApp notifications for your institution.</div>
</div>

<!-- Email SMTP Settings -->
<div class="channel-card animate-in">
    <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_email">
        
        <div class="channel-header">
            <div class="channel-info">
                <div class="channel-icon email"><i class="bi bi-envelope-fill"></i></div>
                <div>
                    <div class="channel-name">Email (SMTP)</div>
                    <div class="channel-desc">Send notifications via email using SMTP server</div>
                </div>
            </div>
            <div class="channel-toggle">
                <span class="status-badge <?= $emailSettings['smtp_enabled'] === '1' ? 'enabled' : 'disabled' ?>">
                    <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                    <?= $emailSettings['smtp_enabled'] === '1' ? 'Enabled' : 'Disabled' ?>
                </span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="smtp_enabled" value="1" <?= $emailSettings['smtp_enabled'] === '1' ? 'checked' : '' ?>>
                </div>
            </div>
        </div>
        
        <div class="channel-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">SMTP Host</label>
                    <input type="text" name="smtp_host" class="form-control" value="<?= e($emailSettings['smtp_host']) ?>" placeholder="smtp.gmail.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Port</label>
                    <input type="text" name="smtp_port" class="form-control" value="<?= e($emailSettings['smtp_port']) ?>" placeholder="587">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="smtp_username" class="form-control" value="<?= e($emailSettings['smtp_username']) ?>" placeholder="your-email@gmail.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <div class="password-toggle">
                        <input type="password" name="smtp_password" class="form-control" value="<?= e($emailSettings['smtp_password']) ?>" placeholder="App Password">
                        <button type="button" class="toggle-btn" onclick="togglePassword(this)"><i class="bi bi-eye"></i></button>
                    </div>
                    <div class="form-text">For Gmail, use App Password (16 characters)</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Encryption</label>
                    <select name="smtp_encryption" class="form-select">
                        <option value="tls" <?= $emailSettings['smtp_encryption'] === 'tls' ? 'selected' : '' ?>>TLS</option>
                        <option value="ssl" <?= $emailSettings['smtp_encryption'] === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="none" <?= $emailSettings['smtp_encryption'] === 'none' ? 'selected' : '' ?>>None</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">From Email</label>
                    <input type="email" name="smtp_from_email" class="form-control" value="<?= e($emailSettings['smtp_from_email']) ?>" placeholder="noreply@school.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label">From Name</label>
                    <input type="text" name="smtp_from_name" class="form-control" value="<?= e($emailSettings['smtp_from_name']) ?>" placeholder="<?= e(APP_NAME) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Test Email Address</label>
                    <input type="email" name="smtp_test_email" class="form-control" value="<?= e($emailSettings['smtp_test_email']) ?>" placeholder="test@example.com">
                    <div class="form-text">Email address where test emails will be sent</div>
                </div>
            </div>
            
            <div class="channel-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
                <button type="submit" name="action" value="test_email" class="btn btn-outline-light"><i class="bi bi-send me-1"></i>Send Test Email</button>
            </div>
        </div>
    </form>
</div>

<!-- Telegram Settings -->
<div class="channel-card animate-in">
    <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_telegram">
        
        <div class="channel-header">
            <div class="channel-info">
                <div class="channel-icon telegram"><i class="bi bi-telegram"></i></div>
                <div>
                    <div class="channel-name">Telegram</div>
                    <div class="channel-desc">Send notifications via Telegram bot</div>
                </div>
            </div>
            <div class="channel-toggle">
                <span class="status-badge <?= $telegramSettings['telegram_enabled'] === '1' ? 'enabled' : 'disabled' ?>">
                    <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                    <?= $telegramSettings['telegram_enabled'] === '1' ? 'Enabled' : 'Disabled' ?>
                </span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="telegram_enabled" value="1" <?= $telegramSettings['telegram_enabled'] === '1' ? 'checked' : '' ?>>
                </div>
            </div>
        </div>
        
        <div class="channel-body">
            <div class="info-card">
                <div class="info-card-title"><i class="bi bi-info-circle"></i> How to setup Telegram Bot</div>
                <p class="info-card-text">
                    1. Message <a href="https://t.me/BotFather" target="_blank">@BotFather</a> on Telegram<br>
                    2. Send /newbot and follow instructions to get your Bot Token<br>
                    3. Add your bot to a group/channel and get the Chat ID
                </p>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Bot Token</label>
                    <div class="password-toggle">
                        <input type="password" name="telegram_token" class="form-control" value="<?= e($telegramSettings['telegram_token']) ?>" placeholder="123456:ABC-DEF...">
                        <button type="button" class="toggle-btn" onclick="togglePassword(this)"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Chat ID</label>
                    <input type="text" name="telegram_chat_id" class="form-control" value="<?= e($telegramSettings['telegram_chat_id']) ?>" placeholder="-1001234567890">
                    <div class="form-text">Group/Channel ID (starts with -100 for groups)</div>
                </div>
            </div>
            
            <div class="channel-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
                <button type="submit" name="action" value="test_telegram" class="btn btn-outline-light"><i class="bi bi-send me-1"></i>Send Test Message</button>
            </div>
        </div>
    </form>
</div>

<!-- WhatsApp (UltraMSG) Settings -->
<div class="channel-card animate-in">
    <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_whatsapp">
        
        <div class="channel-header">
            <div class="channel-info">
                <div class="channel-icon whatsapp"><i class="bi bi-whatsapp"></i></div>
                <div>
                    <div class="channel-name">WhatsApp (UltraMSG)</div>
                    <div class="channel-desc">Send notifications via WhatsApp using UltraMSG API</div>
                </div>
            </div>
            <div class="channel-toggle">
                <span class="status-badge <?= $whatsappSettings['whatsapp_enabled'] === '1' ? 'enabled' : 'disabled' ?>">
                    <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                    <?= $whatsappSettings['whatsapp_enabled'] === '1' ? 'Enabled' : 'Disabled' ?>
                </span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="whatsapp_enabled" value="1" <?= $whatsappSettings['whatsapp_enabled'] === '1' ? 'checked' : '' ?>>
                </div>
            </div>
        </div>
        
        <div class="channel-body">
            <div class="info-card">
                <div class="info-card-title"><i class="bi bi-info-circle"></i> UltraMSG Setup</div>
                <p class="info-card-text">
                    1. Sign up at <a href="https://ultramsg.com" target="_blank">ultramsg.com</a><br>
                    2. Create an instance and link your WhatsApp number<br>
                    3. Copy your Instance ID and Token from the dashboard
                </p>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Instance ID</label>
                    <input type="text" name="ultramsg_instance" class="form-control" value="<?= e($whatsappSettings['ultramsg_instance']) ?>" placeholder="instance12345">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Token</label>
                    <div class="password-toggle">
                        <input type="password" name="ultramsg_token" class="form-control" value="<?= e($whatsappSettings['ultramsg_token']) ?>" placeholder="your-token-here">
                        <button type="button" class="toggle-btn" onclick="togglePassword(this)"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Test Phone Number</label>
                    <input type="text" name="test_phone" class="form-control" value="<?= e($whatsappSettings['whatsapp_test_phone']) ?>" placeholder="+919876543210">
                    <div class="form-text">Enter phone with country code for testing</div>
                </div>
            </div>
            
            <div class="channel-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
                <button type="submit" name="action" value="test_whatsapp" class="btn btn-outline-light"><i class="bi bi-send me-1"></i>Send Test Message</button>
            </div>
        </div>
    </form>
</div>

<script>
function togglePassword(btn) {
    const input = btn.parentElement.querySelector('input');
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?php
};
include __DIR__ . '/views/partials/layout.php';
