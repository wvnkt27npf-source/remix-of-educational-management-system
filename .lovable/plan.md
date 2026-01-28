

# Fix Template Selection Display in Site Settings

## Problem Identified
When you go to Site Settings, the Website Template selector always shows the first template ("Admission Focus" / `modern-dark`) as active, even when a different template is actually selected and being used on the website.

## Root Cause Analysis
After reviewing the code, the issue is in how the template value is being compared:
1. The saved template value in CSV may contain leading/trailing whitespace
2. The comparison `$setting['value'] === $key` uses strict comparison which will fail if there's any whitespace difference
3. There's no trimming of the value before comparison

## Solution

### 1. Trim Template Value Before Display Comparison
In `php-ems/site_settings.php`, ensure the template value is trimmed when building the settings group and when comparing in the template selector.

**Changes to make:**

```php
// Line ~207: Trim value when reading from CSV
$value = isset($settingsByKey[$key]) ? trim($settingsByKey[$key]['value']) : $ds['value'];
```

### 2. Add Explicit Trim in Template Comparison
Update the template rendering section (around line 411) to also trim the value during comparison:

**Before:**
```php
<label class="template-card <?= $setting['value'] === $key ? 'active' : '' ?>"
```

**After:**
```php
<?php $currentTemplate = trim($setting['value']); ?>
<label class="template-card <?= $currentTemplate === $key ? 'active' : '' ?>"
```

### 3. Fix All Template Value Comparisons
Update all places where `$setting['value']` is compared with template keys:
- Line 411 (card active class)
- Line 411 (border color inline style)  
- Line 412 (radio checked attribute)
- Line 419 (Active badge display)

## Technical Implementation

### Files to Modify
- `php-ems/site_settings.php`

### Code Changes

**1. Add trimmed variable before template loop (~line 408):**
```php
<?php 
$templates = [...]; // existing templates array
$currentTemplate = trim($setting['value']); // Add this line
?>
```

**2. Update all comparisons to use `$currentTemplate`:**
- Replace `$setting['value'] === $key` with `$currentTemplate === $key` in 4 places

## Expected Result
After this fix:
- The template that is actually active in the CSV will show as selected
- Selecting a new template and saving will properly update the display
- The active badge will appear on the correct template card

