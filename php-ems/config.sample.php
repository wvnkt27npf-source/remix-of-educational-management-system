<?php
/**
 * Sample Configuration File
 * Copy this to config.php and customize for your school
 */

// ============================================
// SCHOOL INFORMATION (Customize these)
// ============================================
define('SCHOOL_NAME', 'Your School Name');
define('SCHOOL_TAGLINE', 'Your School Tagline');
define('SCHOOL_SHORT_NAME', 'YSN');
define('SCHOOL_ESTABLISHED', '2000');
define('SCHOOL_AFFILIATION', 'CBSE Affiliated');

// School Statistics (displayed on homepage)
define('SCHOOL_YEARS', '20+');
define('SCHOOL_STUDENTS', '3000+');
define('SCHOOL_RESULTS', '95%');
define('SCHOOL_TEACHERS', '100+');

// ============================================
// CONTACT INFORMATION
// ============================================
define('SCHOOL_PHONE_1', '+91 000 000 0000');
define('SCHOOL_PHONE_2', '+91 000 000 0001');
define('SCHOOL_EMAIL', 'info@yourschool.edu.in');
define('SCHOOL_ADMISSION_EMAIL', 'admission@yourschool.edu.in');
define('SCHOOL_ADDRESS', '123 School Street, City, State - 000000');
define('SCHOOL_CITY', 'Your City');
define('SCHOOL_COUNTRY', 'India');

// Office Hours
define('SCHOOL_HOURS', 'Mon - Sat: 8:00 AM - 4:00 PM');
define('SCHOOL_HOURS_CLOSED', 'Sunday: Closed');

// ============================================
// SOCIAL MEDIA LINKS
// ============================================
define('SOCIAL_FACEBOOK', 'https://facebook.com/yourschool');
define('SOCIAL_TWITTER', 'https://twitter.com/yourschool');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/yourschool');
define('SOCIAL_YOUTUBE', 'https://youtube.com/yourschool');

// ============================================
// EMAIL NOTIFICATIONS (for admission alerts)
// ============================================
define('ADMIN_EMAIL', 'admin@yourschool.edu.in'); // Email to receive admission notifications
define('SMTP_ENABLED', false); // Set to true if you have SMTP configured
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM_NAME', SCHOOL_NAME);
define('SMTP_FROM_EMAIL', 'noreply@yourschool.edu.in');

// ============================================
// GALLERY IMAGES (URLs or local paths)
// Add your own images here
// ============================================
$GALLERY_IMAGES = [
    [
        'url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=600',
        'title' => 'Modern Classrooms',
        'category' => 'campus'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=600',
        'title' => 'School Library',
        'category' => 'campus'
    ],
    // Add more images as needed...
];

// ============================================
// APP SETTINGS (Advanced - usually don't change)
// ============================================
define('APP_NAME', 'Educational Management System');
define('APP_TIMEZONE', 'Asia/Kolkata');

// Base URL (optional). Example: https://example.com/ems
define('BASE_URL', '');

// Paths
define('BASE_PATH', __DIR__);
define('DATA_PATH', BASE_PATH . '/data');

// Security
define('SESSION_NAME', 'ems_session');
define('CSRF_TOKEN_KEY', '_csrf_token');

// Production hardening
define('APP_DEBUG', false);