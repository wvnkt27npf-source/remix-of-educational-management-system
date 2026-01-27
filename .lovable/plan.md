

# 5 Full Landing Page Templates with Admin Management

## Overview
Create 5 completely separate, fully-designed landing page templates with unique visual themes and layouts. Each template will be a standalone PHP file with its own design system, and admins will be able to switch between them from the Site Settings panel.

---

## Template Designs

### Template 1: Modern Dark (Current)
**File:** `templates/template-modern-dark.php`
- Already exists as current index.php
- Premium glassmorphism with dark background
- Mesh gradients, particle effects
- Multi-banner hero slider with 5 themes
- Floating cards, glowing buttons
- Best For: Modern tech-savvy institutions

### Template 2: Classic Elegant
**File:** `templates/template-classic-elegant.php`
- **Style:** Traditional, sophisticated, timeless
- **Colors:** Navy Blue (#1e3a5f), Gold (#c9a227), Cream (#f8f5f0)
- **Typography:** Serif headings (Playfair Display), elegant spacing
- **Layout:**
  - Full-width hero with centered text over large school photo
  - Decorative gold line dividers between sections
  - 2-column about section with ornamental frames
  - Programs as elegant numbered list with icons
  - Principal message in quote card with decorative borders
  - Classic grid gallery with rounded corners
  - Testimonials as elegant quote blocks
  - Contact in 4-column layout with gold icons
  - Dark navy footer with elegant links
- Best For: Established schools, traditional institutions

### Template 3: Vibrant Colorful
**File:** `templates/template-vibrant-colorful.php`
- **Style:** Playful, energetic, child-friendly
- **Colors:** Bright Blue (#4361ee), Orange (#ff6b35), Yellow (#ffd600), Green (#06d6a0)
- **Typography:** Rounded sans-serif (Nunito), bouncy feel
- **Layout:**
  - Hero with wave-shaped bottom divider and floating shapes
  - Animated bouncing elements (circles, stars)
  - Programs as colorful icon cards with gradient backgrounds
  - Stats as large animated number counters with icons
  - Gallery as polaroid-style frames
  - Testimonials as colorful speech bubbles
  - Fun wave dividers between sections
  - Playful footer with rounded elements
- **Animations:** Bounce on scroll, wave motion, floating shapes
- Best For: Primary schools, playschools, kindergartens

### Template 4: Minimal Clean
**File:** `templates/template-minimal-clean.php`
- **Style:** Ultra-clean, whitespace-focused, sophisticated
- **Colors:** Pure White (#ffffff), Charcoal (#333333), Single Accent (school's primary color)
- **Typography:** Clean sans-serif (Inter), generous line-height
- **Layout:**
  - Full-screen hero with minimal text, single CTA
  - Maximum whitespace (100px+ section padding)
  - Single-column centered content
  - Programs as text-focused horizontal list
  - Full-bleed alternating image/text sections
  - Minimal line borders, no box shadows
  - Large typography for headlines
  - Single testimonial display (one at a time)
  - Ultra-clean footer with minimal links
- Best For: International schools, premium academies

### Template 5: Bold Geometric
**File:** `templates/template-bold-geometric.php`
- **Style:** Modern, edgy, tech-forward
- **Colors:** Deep Purple (#6b21a8), Electric Blue (#0ea5e9), Neon accents
- **Typography:** Bold condensed headings, sharp lines
- **Layout:**
  - Hero with diagonal split design (45-degree angle)
  - Geometric pattern backgrounds (triangles, hexagons)
  - Programs as hexagonal/diamond-shaped cards
  - Stats in large angular blocks with 3D effect
  - Asymmetric grid layouts
  - Tilted image frames with overlap effects
  - Sharp-edged buttons and cards
  - Angular section dividers
  - Bold geometric footer pattern
- **Animations:** Slide-in from angles, 3D hover effects
- Best For: STEM schools, modern high schools, tech academies

---

## Technical Implementation

### New Directory Structure
```text
php-ems/
  templates/
    template-modern-dark.php      (refactored from index.php)
    template-classic-elegant.php  (new)
    template-vibrant-colorful.php (new)
    template-minimal-clean.php    (new)
    template-bold-geometric.php   (new)
    partials/
      data-loader.php             (shared data loading logic)
```

### Index.php Refactor
The index.php will become a template router:
```php
// Load shared data (settings, banners, news, etc.)
require_once __DIR__ . '/templates/partials/data-loader.php';

// Get selected template from site_settings
$selectedTemplate = getSetting('site_template', 'modern-dark');

// Load the template
$templatePath = __DIR__ . '/templates/template-' . $selectedTemplate . '.php';
if (file_exists($templatePath)) {
    require_once $templatePath;
} else {
    require_once __DIR__ . '/templates/template-modern-dark.php';
}
```

### Site Settings Addition
Add new setting to site_settings.php:
```php
['key' => 'site_template', 'value' => 'modern-dark', 'type' => 'template_select', 'label' => 'Website Template', 'group' => 'theme']
```

### Template Selection UI
In site_settings.php, add a visual template selector with:
- Preview cards for each template with thumbnail
- Template name and description
- "Active" badge on selected template
- Click to select
- Optional "Preview" button to see template before activating

---

## Each Template Includes

All templates will have these sections (styled differently):
1. **Navigation Bar** - Fixed/sticky with responsive mobile menu
2. **Hero Section** - With banner slider support (5 themes)
3. **Marquee/Announcement Bar** - Scrolling highlights
4. **About Section** - School introduction with image
5. **Programs Section** - Academic offerings (6 cards)
6. **Principal Section** - Message with photo
7. **Facilities Section** - 8 facility cards with icons
8. **Gallery Section** - Image showcase
9. **News Section** - Latest news articles
10. **Testimonials Section** - Parent/student reviews
11. **Admission Section** - Inquiry form
12. **Contact Section** - 4 contact cards
13. **Footer** - Links, social, copyright

---

## Shared Data (partials/data-loader.php)
All templates will use the same data loaded from CSVs:
- Site settings (school name, colors, contact, etc.)
- Hero banners from hero_banners.csv
- News articles from news.csv
- Testimonials from testimonials.csv
- Form handling for admissions

---

## Responsive Design
Each template will be fully responsive:
- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: < 768px

---

## File Changes Summary

### New Files
| File | Description |
|------|-------------|
| `templates/partials/data-loader.php` | Shared data loading logic |
| `templates/template-modern-dark.php` | Current design (refactored) |
| `templates/template-classic-elegant.php` | Traditional elegant theme |
| `templates/template-vibrant-colorful.php` | Playful colorful theme |
| `templates/template-minimal-clean.php` | Minimalist clean theme |
| `templates/template-bold-geometric.php` | Modern geometric theme |

### Modified Files
| File | Changes |
|------|---------|
| `index.php` | Convert to template router |
| `site_settings.php` | Add template selector UI |

---

## Template Preview Cards (for Admin UI)

```text
+------------------------------------------------+
|  Choose Website Template                        |
+------------------------------------------------+
|  +--------+  +--------+  +--------+            |
|  |  Dark  |  |Classic |  |Vibrant |            |
|  |  BG    |  | Cream  |  |Colorful|            |
|  +--------+  +--------+  +--------+            |
|  Modern Dark Classic     Vibrant               |
|  ★ Active    Elegant     Colorful              |
|                                                |
|  +--------+  +--------+                        |
|  | Clean  |  |Geometric|                       |
|  | White  |  | Purple |                        |
|  +--------+  +--------+                        |
|  Minimal     Bold                              |
|  Clean       Geometric                         |
+------------------------------------------------+
```

---

## Implementation Order

1. Create `templates/` directory structure
2. Create `templates/partials/data-loader.php` with shared data logic
3. Refactor `index.php` to become template router
4. Move current design to `templates/template-modern-dark.php`
5. Add `site_template` setting to site_settings.php
6. Create Template 2: Classic Elegant (full implementation)
7. Create Template 3: Vibrant Colorful (full implementation)
8. Create Template 4: Minimal Clean (full implementation)
9. Create Template 5: Bold Geometric (full implementation)
10. Add visual template selector UI to site_settings.php
11. Test all templates with responsive design
12. Add preview functionality

---

## Technical Notes

### Hero Banner Compatibility
All 5 templates will support the multi-banner hero slider with the 5 themes (admission, festival, vacation, achievement, welcome). Each template will style the banners according to its own design language while preserving theme-specific colors.

### CSS Isolation
Each template will have its own complete CSS within the file to prevent style conflicts and ensure true visual separation.

### Performance
- Each template is self-contained (~3000-4000 lines)
- Only the selected template's CSS/JS is loaded
- Lazy loading for images
- Critical CSS inline for fast LCP

### Fallback
If template file is missing, automatically fall back to Modern Dark theme.

