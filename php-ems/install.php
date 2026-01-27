<?php
require_once __DIR__ . '/bootstrap.php';

// Create first admin if users.csv has no users.
$users = csv_read_all(DATA_PATH . '/users.csv');
if (!empty($users)) {
    flash_set('info', 'Install already completed.');
    redirect('login');
}

// Seed admin
$adminPassword = 'admin123';
$row = [
    'username' => 'admin',
    'password' => password_hash($adminPassword, PASSWORD_DEFAULT),
    'role' => 'admin',
    'linked_id' => '',
];
csv_insert(DATA_PATH . '/users.csv', $row);

flash_set('success', 'Install complete. Admin user created (admin / admin123). Please change password after login.');
redirect('login');
