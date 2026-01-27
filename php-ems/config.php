<?php
/**
 * Main Configuration File
 * Edit these settings to customize your school website
 */

// ============================================
// SCHOOL INFORMATION (Customize these)
// ============================================
define('SCHOOL_NAME', 'Delhi Public School');
define('SCHOOL_TAGLINE', 'Excellence in Education');
define('SCHOOL_SHORT_NAME', 'DPS');
define('SCHOOL_ESTABLISHED', '1998');
define('SCHOOL_AFFILIATION', 'CBSE Affiliated');

// School Statistics (displayed on homepage)
define('SCHOOL_YEARS', '25+');
define('SCHOOL_STUDENTS', '5000+');
define('SCHOOL_RESULTS', '98%');
define('SCHOOL_TEACHERS', '200+');

// ============================================
// CONTACT INFORMATION
// ============================================
define('SCHOOL_PHONE_1', '+91 123 456 7890');
define('SCHOOL_PHONE_2', '+91 123 456 7891');
define('SCHOOL_EMAIL', 'info@dps.edu.in');
define('SCHOOL_ADMISSION_EMAIL', 'admission@dps.edu.in');
define('SCHOOL_ADDRESS', '123 Education Lane, Sector 15, New Delhi - 110001');
define('SCHOOL_CITY', 'New Delhi');
define('SCHOOL_COUNTRY', 'India');

// Office Hours
define('SCHOOL_HOURS', 'Mon - Sat: 8:00 AM - 4:00 PM');
define('SCHOOL_HOURS_CLOSED', 'Sunday: Closed');

// ============================================
// SOCIAL MEDIA LINKS
// ============================================
define('SOCIAL_FACEBOOK', 'https://facebook.com/');
define('SOCIAL_TWITTER', 'https://twitter.com/');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/');
define('SOCIAL_YOUTUBE', 'https://youtube.com/');

// ============================================
// EMAIL NOTIFICATIONS (for admission alerts)
// ============================================
define('ADMIN_EMAIL', 'admin@dps.edu.in'); // Email to receive admission notifications
define('SMTP_ENABLED', false); // Set to true if you have SMTP configured
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM_NAME', SCHOOL_NAME);
define('SMTP_FROM_EMAIL', 'noreply@dps.edu.in');

// ============================================
// GALLERY IMAGES (URLs or local paths)
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
    [
        'url' => 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=600',
        'title' => 'Science Laboratory',
        'category' => 'campus'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600',
        'title' => 'Sports Day',
        'category' => 'events'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?w=600',
        'title' => 'Annual Function',
        'category' => 'events'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600',
        'title' => 'Student Activities',
        'category' => 'activities'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=600',
        'title' => 'Classroom Learning',
        'category' => 'activities'
    ],
    [
        'url' => 'https://images.unsplash.com/photo-1560785496-3c9d27877182?w=600',
        'title' => 'Computer Lab',
        'category' => 'campus'
    ],
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