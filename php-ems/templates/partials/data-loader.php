<?php
/**
 * Shared Data Loader for Landing Page Templates
 * This file loads all common data needed by templates
 */

// Bootstrap is already loaded by index.php router

// Load site settings from CSV
$settingsFile = DATA_PATH . '/site_settings.csv';
$settings = [];
if (file_exists($settingsFile)) {
    $allSettings = csv_read_all($settingsFile);
    foreach ($allSettings as $s) {
        $settings[$s['key']] = $s['value'];
    }
}

// Helper function to get setting with fallback
if (!function_exists('getSetting')) {
    function getSetting($key, $default = '') {
        global $settings;
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
}

// Load gallery images
global $GALLERY_IMAGES;
if (!isset($GALLERY_IMAGES) || empty($GALLERY_IMAGES)) {
    $GALLERY_IMAGES = [
        ['url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=500', 'title' => 'Students Learning'],
        ['url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=500', 'title' => 'Campus Life'],
        ['url' => 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=500', 'title' => 'Science Lab'],
        ['url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=500', 'title' => 'Classroom'],
        ['url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=500', 'title' => 'Graduation'],
        ['url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=500', 'title' => 'Library'],
        ['url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=500', 'title' => 'Sports'],
        ['url' => 'https://images.unsplash.com/photo-1564429238535-7c49fa056583?w=500', 'title' => 'Activities'],
    ];
}

// Load news articles (published only)
csv_init(DATA_PATH . '/news.csv', array('id', 'title', 'content', 'image', 'date', 'status', 'created_at'));
$allNews = csv_read_all(DATA_PATH . '/news.csv');
$newsArticles = array();
foreach ($allNews as $n) {
    if (isset($n['status']) && $n['status'] === 'published') {
        $newsArticles[] = $n;
    } elseif (!isset($n['status'])) {
        $newsArticles[] = $n;
    }
}
usort($newsArticles, function($a, $b) {
    $dateA = isset($a['date']) ? $a['date'] : '';
    $dateB = isset($b['date']) ? $b['date'] : '';
    return strcmp((string)$dateB, (string)$dateA);
});
$newsArticles = array_slice($newsArticles, 0, 6);

// Load testimonials (published only)
csv_init(DATA_PATH . '/testimonials.csv', array('id', 'name', 'role', 'content', 'image', 'rating', 'status', 'created_at'));
$allTestimonials = csv_read_all(DATA_PATH . '/testimonials.csv');
$testimonials = array();
foreach ($allTestimonials as $t) {
    if (isset($t['status']) && $t['status'] === 'published') {
        $testimonials[] = $t;
    } elseif (!isset($t['status'])) {
        $testimonials[] = $t;
    }
}
$testimonials = array_slice($testimonials, 0, 6);

// Load hero banners (active only, respecting date range)
$heroBanners = csv_read_all(DATA_PATH . '/hero_banners.csv');
$today = date('Y-m-d');
$activeBanners = array();
foreach ($heroBanners as $b) {
    $isActive = isset($b['is_active']) && $b['is_active'] === '1';
    if (!$isActive) continue;
    
    $startOk = empty($b['start_date']) || $b['start_date'] <= $today;
    $endOk = empty($b['end_date']) || $b['end_date'] >= $today;
    
    if ($startOk && $endOk) {
        $activeBanners[] = $b;
    }
}
usort($activeBanners, function($a, $b) {
    $orderA = isset($a['display_order']) ? (int)$a['display_order'] : 0;
    $orderB = isset($b['display_order']) ? (int)$b['display_order'] : 0;
    return $orderA - $orderB;
});
$activeBanners = array_values($activeBanners);

// Initialize admissions CSV
csv_init(DATA_PATH . '/admissions.csv', array('id', 'student_name', 'parent_name', 'email', 'phone', 'dob', 'gender', 'class_applying', 'previous_school', 'address', 'message', 'status', 'created_at'));

// Handle admission form submission
$success = false;
$error = '';
if (request_method() === 'POST') {
    $student_name = trim((string)post('student_name', ''));
    $parent_name = trim((string)post('parent_name', ''));
    $email = trim((string)post('email', ''));
    $phone = trim((string)post('phone', ''));
    $dob = trim((string)post('dob', ''));
    $gender = trim((string)post('gender', ''));
    $class_applying = trim((string)post('class_applying', ''));
    $previous_school = trim((string)post('previous_school', ''));
    $address = trim((string)post('address', ''));
    $message = trim((string)post('message', ''));

    if ($student_name === '' || $parent_name === '' || $email === '' || $phone === '' || $class_applying === '') {
        $error = 'Please fill all required fields.';
    } else {
        $admissionData = array(
            'student_name' => $student_name,
            'parent_name' => $parent_name,
            'email' => $email,
            'phone' => $phone,
            'dob' => $dob,
            'gender' => $gender,
            'class_applying' => $class_applying,
            'previous_school' => $previous_school,
            'address' => $address,
            'message' => $message,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        );
        
        csv_insert(DATA_PATH . '/admissions.csv', $admissionData);
        $success = true;
    }
}

// Get all settings with defaults
$schoolName = getSetting('school_name', 'Delhi Public School');
$schoolTagline = getSetting('school_tagline', 'Excellence in Education');
$schoolLogo = getSetting('school_logo', '');
$heroTitle = getSetting('hero_title', 'Shaping Tomorrow\'s Leaders Today');
$heroSubtitle = getSetting('hero_subtitle', 'Where Excellence Meets Innovation');
$heroImage = getSetting('hero_image', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920');
$admissionOpen = getSetting('admission_open', '1') === '1';
$admissionYear = getSetting('admission_year', '2025-26');

$statYears = getSetting('stat_years', '25+');
$statStudents = getSetting('stat_students', '5000+');
$statTeachers = getSetting('stat_teachers', '200+');
$statResults = getSetting('stat_results', '98%');

$phone1 = getSetting('phone_1', '+91 123 456 7890');
$phone2 = getSetting('phone_2', '+91 123 456 7891');
$emailContact = getSetting('email', 'info@school.edu.in');
$admissionEmail = getSetting('admission_email', 'admission@school.edu.in');
$address = getSetting('address', '123 Education Lane, New Delhi');
$officeHours = getSetting('office_hours', 'Mon - Sat: 8:00 AM - 4:00 PM');

$aboutTitle = getSetting('about_title', 'Building Character, Shaping Futures');
$aboutText = getSetting('about_text', 'Our institution has been a beacon of educational excellence...');
$aboutImage = getSetting('about_image', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800');

$principalName = getSetting('principal_name', 'Dr. Rajesh Kumar');
$principalTitle = getSetting('principal_title', 'Principal & Director');
$principalMessage = getSetting('principal_message', 'Education is not just about acquiring knowledge...');
$principalImage = getSetting('principal_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400');

$socialFacebook = getSetting('social_facebook', '#');
$socialInstagram = getSetting('social_instagram', '#');
$socialTwitter = getSetting('social_twitter', '#');
$socialYoutube = getSetting('social_youtube', '#');
$socialLinkedin = getSetting('social_linkedin', '#');

$primaryColor = getSetting('primary_color', '#1a4d8f');
$secondaryColor = getSetting('secondary_color', '#e8b923');
$accentColor = getSetting('accent_color', '#28a745');

// Programs data
$programs = array(
    array('icon' => 'bi-emoji-smile', 'title' => 'Pre-Primary', 'desc' => 'Playful learning environment for Nursery to UKG with activity-based curriculum.'),
    array('icon' => 'bi-journal-text', 'title' => 'Primary (I-V)', 'desc' => 'Strong foundation in core subjects with focus on creativity and curiosity.'),
    array('icon' => 'bi-lightbulb', 'title' => 'Middle (VI-VIII)', 'desc' => 'Integrated approach to learning with emphasis on critical thinking.'),
    array('icon' => 'bi-mortarboard', 'title' => 'Secondary (IX-X)', 'desc' => 'CBSE curriculum with career counseling and board exam preparation.'),
    array('icon' => 'bi-graph-up-arrow', 'title' => 'Senior Sec (XI-XII)', 'desc' => 'Science, Commerce & Humanities streams with competitive exam coaching.'),
    array('icon' => 'bi-palette', 'title' => 'Co-Curricular', 'desc' => 'Art, Music, Dance, Sports & Clubs for all-round personality development.'),
);

// Facilities data
$facilities = array(
    array('icon' => 'bi-pc-display', 'title' => 'Smart Classrooms', 'desc' => 'Interactive boards & digital learning tools'),
    array('icon' => 'bi-flask', 'title' => 'Science Labs', 'desc' => 'Fully equipped Physics, Chemistry & Biology labs'),
    array('icon' => 'bi-book', 'title' => 'Library', 'desc' => '50,000+ books, journals & digital resources'),
    array('icon' => 'bi-dribbble', 'title' => 'Sports Complex', 'desc' => 'Indoor & outdoor sports with professional coaching'),
    array('icon' => 'bi-music-note-beamed', 'title' => 'Music & Arts', 'desc' => 'Dedicated studios for creative expression'),
    array('icon' => 'bi-bus-front', 'title' => 'Transport', 'desc' => 'GPS-enabled buses covering all major routes'),
    array('icon' => 'bi-shield-check', 'title' => 'Security', 'desc' => 'CCTV surveillance & trained security staff'),
    array('icon' => 'bi-hospital', 'title' => 'Medical Room', 'desc' => 'Qualified nurse & first-aid facilities'),
);

// Marquee items
$marqueeItems = array(
    array('icon' => 'bi-trophy', 'text' => 'Best School Award 2024'),
    array('icon' => 'bi-star-fill', 'text' => '100% Board Results'),
    array('icon' => 'bi-people-fill', 'text' => '5000+ Happy Students'),
    array('icon' => 'bi-award', 'text' => 'Excellence in Education'),
    array('icon' => 'bi-lightning-fill', 'text' => 'Smart Classrooms'),
    array('icon' => 'bi-shield-check', 'text' => 'Safe Campus'),
);

// Theme icons for hero banners
$themeIcons = array(
    'admission' => 'bi-mortarboard',
    'festival' => 'bi-stars',
    'vacation' => 'bi-sun',
    'achievement' => 'bi-trophy',
    'welcome' => 'bi-book',
);
