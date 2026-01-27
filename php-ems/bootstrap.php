<?php
require_once __DIR__ . '/config.php';

date_default_timezone_set(APP_TIMEZONE);

// Error handling
if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}

// Session
session_name(SESSION_NAME);
session_start();

require_once __DIR__ . '/functions.php';

// Ensure CSV files exist
csv_init(DATA_PATH . '/users.csv', ['id', 'username', 'password', 'role', 'linked_id']);

// MIGRATION: Add missing linked_id header to existing users.csv
csv_ensure_headers(DATA_PATH . '/users.csv', ['id', 'username', 'password', 'role', 'linked_id']);

// Students with comprehensive details
csv_init(DATA_PATH . '/students.csv', [
    'id', 'name', 'class', 'section', 'roll_number', 'admission_date', 'status',
    // Personal Info (Admin editable only)
    'father_name', 'mother_name', 'dob', 'blood_group', 'gender', 'religion', 'category',
    'phone', 'alt_phone', 'email', 'address', 'city', 'state', 'pincode',
    // Documents
    'photo', 'aadhar_number', 'birth_certificate', 'transfer_certificate',
    // Academic
    'previous_school', 'previous_class',
    // Editable by student
    'emergency_contact', 'medical_conditions'
]);

// Teachers with comprehensive details  
csv_init(DATA_PATH . '/teachers.csv', [
    'id', 'name', 'phone', 'email', 'dob', 'gender', 'address',
    'subject', 'designation', 'qualification', 'experience', 'joining_date', 'salary',
    'id_proof', 'photo', 'status'
]);

csv_init(DATA_PATH . '/exams.csv', ['id', 'subject', 'class', 'date', 'marks']);
csv_init(DATA_PATH . '/events.csv', ['id', 'title', 'date', 'description']);
csv_init(DATA_PATH . '/news.csv', ['id', 'title', 'content', 'image', 'date', 'status', 'created_at']);
csv_init(DATA_PATH . '/testimonials.csv', ['id', 'name', 'role', 'content', 'image', 'rating', 'status', 'created_at']);

// Exam Results
csv_init(DATA_PATH . '/exam_results.csv', [
    'id', 'exam_id', 'student_id', 'marks_obtained', 'grade', 
    'remarks', 'result_image', 'uploaded_by', 'created_at'
]);

// Notification Settings
csv_init(DATA_PATH . '/notification_settings.csv', [
    'id', 'key', 'value', 'channel'
]);

// Classes
csv_init(DATA_PATH . '/classes.csv', [
    'id', 'name', 'section', 'class_teacher_id', 'academic_year', 
    'room_number', 'status', 'created_at'
]);

// Hero Banners for landing page slider
csv_init(DATA_PATH . '/hero_banners.csv', [
    'id', 'title', 'subtitle', 'theme', 'image', 'badge_text',
    'cta_primary_text', 'cta_primary_link', 'cta_secondary_text', 'cta_secondary_link',
    'is_active', 'display_order', 'start_date', 'end_date', 'created_at'
]);

// Seed default banners if empty
$heroBanners = csv_read_all(DATA_PATH . '/hero_banners.csv');
if (empty($heroBanners)) {
    $defaultBanners = [
        [
            'title' => 'Shaping Tomorrow\'s Leaders Today',
            'subtitle' => 'Join our legacy of academic excellence. Experience world-class education with modern facilities and dedicated faculty.',
            'theme' => 'admission',
            'image' => '',
            'badge_text' => 'Admissions Open 2025-26',
            'cta_primary_text' => 'Apply Now',
            'cta_primary_link' => '#admission',
            'cta_secondary_text' => 'Learn More',
            'cta_secondary_link' => '#about',
            'is_active' => '1',
            'display_order' => '1',
            'start_date' => '',
            'end_date' => '',
            'created_at' => date('Y-m-d H:i:s'),
        ],
        [
            'title' => 'Welcome to a New Beginning',
            'subtitle' => 'A fresh start awaits! Embark on an exciting journey of learning, discovery, and growth with us.',
            'theme' => 'welcome',
            'image' => '',
            'badge_text' => 'New Session Starting Soon',
            'cta_primary_text' => 'Explore Programs',
            'cta_primary_link' => '#programs',
            'cta_secondary_text' => 'Contact Us',
            'cta_secondary_link' => '#contact',
            'is_active' => '1',
            'display_order' => '2',
            'start_date' => '',
            'end_date' => '',
            'created_at' => date('Y-m-d H:i:s'),
        ],
        [
            'title' => 'Celebrating Excellence',
            'subtitle' => 'Our students consistently achieve outstanding results. Join the tradition of success and achievement.',
            'theme' => 'achievement',
            'image' => '',
            'badge_text' => '100% Board Results',
            'cta_primary_text' => 'View Results',
            'cta_primary_link' => '#achievements',
            'cta_secondary_text' => 'Meet Our Toppers',
            'cta_secondary_link' => '#toppers',
            'is_active' => '1',
            'display_order' => '3',
            'start_date' => '',
            'end_date' => '',
            'created_at' => date('Y-m-d H:i:s'),
        ],
        [
            'title' => 'Celebrate With Joy',
            'subtitle' => 'Wishing our entire school family a wonderful celebration filled with happiness and prosperity!',
            'theme' => 'festival',
            'image' => '',
            'badge_text' => 'Happy Festivities!',
            'cta_primary_text' => 'View Gallery',
            'cta_primary_link' => '#gallery',
            'cta_secondary_text' => 'Upcoming Events',
            'cta_secondary_link' => '#events',
            'is_active' => '0',
            'display_order' => '4',
            'start_date' => '',
            'end_date' => '',
            'created_at' => date('Y-m-d H:i:s'),
        ],
        [
            'title' => 'Summer Adventures Await',
            'subtitle' => 'Fun-filled summer camps with sports, arts, science experiments, and outdoor adventures for all ages!',
            'theme' => 'vacation',
            'image' => '',
            'badge_text' => 'Summer Camp 2025',
            'cta_primary_text' => 'Register Now',
            'cta_primary_link' => '#admission',
            'cta_secondary_text' => 'View Activities',
            'cta_secondary_link' => '#programs',
            'is_active' => '0',
            'display_order' => '5',
            'start_date' => '',
            'end_date' => '',
            'created_at' => date('Y-m-d H:i:s'),
        ],
    ];
    foreach ($defaultBanners as $banner) {
        csv_insert(DATA_PATH . '/hero_banners.csv', $banner);
    }
}
