<?php
/**
 * Template Router
 * Loads the selected landing page template based on site settings
 */
require_once __DIR__ . '/bootstrap.php';

// If logged in, go to dashboard
if (is_logged_in()) {
    redirect('dashboard');
}

// Load site settings to get selected template
$settingsFile = DATA_PATH . '/site_settings.csv';
$selectedTemplate = 'modern-dark'; // Default

if (file_exists($settingsFile)) {
    $allSettings = csv_read_all($settingsFile);
    foreach ($allSettings as $s) {
        if ($s['key'] === 'site_template') {
            $selectedTemplate = $s['value'];
            break;
        }
    }
}

// Validate template name (security)
$validTemplates = ['modern-dark', 'classic-elegant', 'vibrant-colorful', 'minimal-clean', 'bold-geometric'];
if (!in_array($selectedTemplate, $validTemplates, true)) {
    $selectedTemplate = 'modern-dark';
}

// Load the template
$templatePath = __DIR__ . '/templates/template-' . $selectedTemplate . '.php';
if (file_exists($templatePath)) {
    require_once $templatePath;
} else {
    // Fallback to modern dark
    require_once __DIR__ . '/templates/template-modern-dark.php';
}
