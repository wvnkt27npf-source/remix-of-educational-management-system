<?php
require_once __DIR__ . '/bootstrap.php';
logout_user();
flash_set('info', 'You have been logged out.');
redirect('login');
