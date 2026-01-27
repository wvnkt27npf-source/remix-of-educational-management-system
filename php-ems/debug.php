<?php
/**
 * Debug/Health Check Script
 * Visit /debug in browser to diagnose hosting issues
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== PHP Health Check ===\n\n";

// 1. PHP Version
echo "1. PHP Version: " . PHP_VERSION . "\n";
echo "   PHP SAPI: " . php_sapi_name() . "\n\n";

// 2. Current Directory
echo "2. Current Directory:\n";
echo "   __DIR__: " . __DIR__ . "\n";
echo "   __FILE__: " . __FILE__ . "\n";
echo "   getcwd(): " . getcwd() . "\n\n";

// 3. Check critical files exist
echo "3. Critical Files Check:\n";
$files = [
    'bootstrap.php',
    'config.php',
    'functions.php',
    'data/users.csv',
    'data/site_settings.csv',
    'data/hero_banners.csv',
    'templates/template-modern-dark.php',
    'templates/partials/data-loader.php',
];
foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $readable = is_readable($path);
    echo "   $file: " . ($exists ? 'EXISTS' : 'MISSING') . ($exists && $readable ? ' (readable)' : ($exists ? ' (NOT readable!)' : '')) . "\n";
}

// 4. Directory permissions
echo "\n4. Directory Permissions:\n";
$dirs = ['data', 'uploads', 'templates'];
foreach ($dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        echo "   $dir/: " . substr(sprintf('%o', fileperms($path)), -4) . " (writable: " . (is_writable($path) ? 'yes' : 'NO!') . ")\n";
    } else {
        echo "   $dir/: MISSING\n";
    }
}

// 5. Try to load bootstrap
echo "\n5. Bootstrap Load Test:\n";
try {
    require_once __DIR__ . '/bootstrap.php';
    echo "   Bootstrap loaded successfully.\n";
    echo "   DATA_PATH defined: " . (defined('DATA_PATH') ? DATA_PATH : 'NO') . "\n";
    echo "   Session started: " . (session_status() === PHP_SESSION_ACTIVE ? 'yes' : 'no') . "\n";
} catch (Throwable $e) {
    echo "   ERROR loading bootstrap: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

// 6. Try to load template data
echo "\n6. Template Data Load Test:\n";
try {
    if (function_exists('csv_read_all')) {
        $banners = csv_read_all(DATA_PATH . '/hero_banners.csv');
        echo "   Hero banners count: " . count($banners) . "\n";
        $settings = csv_read_all(DATA_PATH . '/site_settings.csv');
        echo "   Site settings count: " . count($settings) . "\n";
    } else {
        echo "   csv_read_all function not available!\n";
    }
} catch (Throwable $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

// 7. Error reporting
echo "\n7. Error Reporting:\n";
echo "   display_errors: " . ini_get('display_errors') . "\n";
echo "   error_reporting: " . error_reporting() . "\n";
echo "   log_errors: " . ini_get('log_errors') . "\n";
echo "   error_log: " . ini_get('error_log') . "\n";

echo "\n=== End Health Check ===\n";
