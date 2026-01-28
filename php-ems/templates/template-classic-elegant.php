<?php
/**
 * Classic Elegant Template - FESTIVAL CELEBRATION
 * Warm, festive design with cultural celebrations theme
 * Layout: Centered hero with decorative borders, vertical timeline events, card-based content
 */

// Load shared data
require_once __DIR__ . '/partials/data-loader.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($schoolName) ?> - <?= e($schoolTagline) ?></title>
  <meta name="description" content="<?= e($schoolName) ?> - Celebrating Education & Culture">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --saffron: #ff6b35;
      --gold: #ffc107;
      --maroon: #7c1034;
      --cream: #fff9f0;
      --ivory: #fffef9;
      --dark: #2d1810;
      --text: #3d2914;
      --text-muted: #6d5a47;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; scroll-padding-top: 80px; }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: var(--cream);
      color: var(--text);
      line-height: 1.8;
    }
    
    h1, h2, h3, h4, h5 { font-family: 'Cormorant Garamond', serif; font-weight: 700; color: var(--maroon); }
    
    /* Festive Decorative Elements */
    .festive-border {
      height: 8px;
      background: repeating-linear-gradient(
        90deg,
        var(--saffron) 0px, var(--saffron) 20px,
        var(--gold) 20px, var(--gold) 40px,
        var(--maroon) 40px, var(--maroon) 60px
      );
    }
    
    .rangoli-pattern {
      position: absolute;
      width: 100px;
      height: 100px;
      background: radial-gradient(circle, var(--gold) 10%, transparent 10%),
                  radial-gradient(circle, var(--saffron) 10%, transparent 10%);
      background-size: 20px 20px;
      background-position: 0 0, 10px 10px;
      opacity: 0.1;
      animation: rotate 60s linear infinite;
    }
    
    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    /* Navbar */
    .navbar-festive {
      background: var(--ivory);
      padding: 12px 0;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      box-shadow: 0 4px 20px rgba(0,0,0,.08);
      border-bottom: 3px solid var(--gold);
    }
    
    .nav-brand { display: flex; align-items: center; gap: 15px; text-decoration: none; }
    
    .brand-logo {
      width: 55px;
      height: 55px;
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      color: white;
      border: 3px solid var(--maroon);
      overflow: hidden;
    }
    
    .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
    .brand-name { font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; font-weight: 700; color: var(--maroon); }
    .brand-tagline { font-size: 0.72rem; color: var(--saffron); letter-spacing: 1px; text-transform: uppercase; }
    
    .nav-links { display: flex; align-items: center; gap: 8px; list-style: none; margin: 0; padding: 0; }
    
    .nav-link {
      color: var(--text) !important;
      font-weight: 500;
      padding: 10px 18px !important;
      border-radius: 25px;
      transition: all 0.3s;
      text-decoration: none;
      font-size: 0.9rem;
    }
    
    .nav-link:hover { background: rgba(255, 107, 53, .1); color: var(--saffron) !important; }
    
    .nav-cta {
      background: linear-gradient(135deg, var(--saffron), var(--gold)) !important;
      color: white !important;
      padding: 12px 28px !important;
      box-shadow: 0 4px 15px rgba(255, 107, 53, .3);
    }
    
    .nav-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 107, 53, .4); }
    
    .mobile-toggle {
      display: none;
      background: var(--saffron);
      border: none;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      font-size: 1.2rem;
      cursor: pointer;
    }
    
    /* HERO - Festival Celebration Style */
    .hero-festival {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      background: 
        radial-gradient(circle at 20% 80%, rgba(255, 107, 53, .15), transparent 40%),
        radial-gradient(circle at 80% 20%, rgba(255, 193, 7, .15), transparent 40%),
        var(--cream);
      padding: 140px 20px 100px;
      position: relative;
      overflow: hidden;
    }
    
    .hero-festival::before,
    .hero-festival::after {
      content: '✿';
      position: absolute;
      font-size: 120px;
      color: var(--gold);
      opacity: 0.1;
      animation: float 8s ease-in-out infinite;
    }
    
    .hero-festival::before { top: 15%; left: 5%; }
    .hero-festival::after { bottom: 15%; right: 5%; animation-delay: 4s; }
    
    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(10deg); }
    }
    
    .hero-content { max-width: 850px; position: relative; z-index: 5; }
    
    .celebration-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(135deg, var(--maroon), #a01845);
      color: var(--gold);
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 30px;
      animation: shimmer 3s ease-in-out infinite;
    }
    
    @keyframes shimmer {
      0%, 100% { box-shadow: 0 0 20px rgba(255, 193, 7, .3); }
      50% { box-shadow: 0 0 40px rgba(255, 193, 7, .6); }
    }
    
    .hero-title {
      font-size: clamp(3rem, 6vw, 5rem);
      line-height: 1.1;
      margin-bottom: 25px;
      color: var(--maroon);
    }
    
    .hero-title span {
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero-subtitle {
      font-size: 1.2rem;
      color: var(--text-muted);
      margin-bottom: 40px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .hero-buttons { display: flex; gap: 18px; justify-content: center; flex-wrap: wrap; }
    
    .btn-festival {
      padding: 16px 38px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.4s;
      border: none;
    }
    
    .btn-saffron {
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      color: white;
      box-shadow: 0 8px 25px rgba(255, 107, 53, .35);
    }
    
    .btn-saffron:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(255, 107, 53, .5); color: white; }
    
    .btn-outline-maroon {
      background: transparent;
      border: 2px solid var(--maroon);
      color: var(--maroon);
    }
    
    .btn-outline-maroon:hover { background: var(--maroon); color: white; }
    
    /* Decorative Divider */
    .decorative-divider {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      padding: 50px 0;
    }
    
    .divider-line { width: 100px; height: 2px; background: linear-gradient(90deg, transparent, var(--gold), transparent); }
    .divider-icon { color: var(--saffron); font-size: 2rem; }
    
    /* Stats Section */
    .stats-section {
      background: linear-gradient(135deg, var(--maroon), #5a0c26);
      padding: 60px 0;
      position: relative;
      overflow: hidden;
    }
    
    .stats-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffc107' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      opacity: 0.5;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
      position: relative;
      z-index: 2;
    }
    
    .stat-item { text-align: center; color: white; }
    
    .stat-number {
      font-family: 'Cormorant Garamond', serif;
      font-size: 3.5rem;
      font-weight: 700;
      color: var(--gold);
      line-height: 1;
      margin-bottom: 10px;
    }
    
    .stat-label { font-size: 0.95rem; opacity: 0.9; }
    
    /* Section Styles */
    .section { padding: 100px 0; }
    .section-alt { background: var(--ivory); }
    
    .section-header { text-align: center; margin-bottom: 60px; }
    
    .section-badge {
      display: inline-block;
      background: rgba(124, 16, 52, .1);
      color: var(--maroon);
      padding: 10px 25px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.85rem;
      margin-bottom: 18px;
      border: 1px solid rgba(124, 16, 52, .2);
    }
    
    .section-title { font-size: 2.8rem; margin-bottom: 15px; }
    .section-subtitle { font-size: 1.1rem; color: var(--text-muted); max-width: 600px; margin: 0 auto; }
    
    /* About - Split Layout */
    .about-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
    }
    
    .about-image {
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      border: 5px solid var(--ivory);
      box-shadow: 0 20px 50px rgba(0,0,0,.15);
    }
    
    .about-image::before {
      content: '';
      position: absolute;
      top: -10px;
      left: -10px;
      right: 10px;
      bottom: 10px;
      border: 3px solid var(--gold);
      border-radius: 20px;
      z-index: -1;
    }
    
    .about-image img { width: 100%; height: 450px; object-fit: cover; }
    
    .about-content h2 { font-size: 2.2rem; margin-bottom: 20px; }
    .about-content p { color: var(--text-muted); margin-bottom: 25px; line-height: 1.9; }
    
    /* Programs - Horizontal Scroll Cards */
    .programs-scroll {
      display: flex;
      gap: 30px;
      overflow-x: auto;
      padding: 20px 0;
      scroll-snap-type: x mandatory;
    }
    
    .programs-scroll::-webkit-scrollbar { height: 8px; }
    .programs-scroll::-webkit-scrollbar-track { background: var(--cream); border-radius: 10px; }
    .programs-scroll::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 10px; }
    
    .program-card {
      min-width: 320px;
      background: var(--ivory);
      border-radius: 20px;
      padding: 35px;
      scroll-snap-align: start;
      transition: all 0.4s;
      border: 2px solid transparent;
      position: relative;
      overflow: hidden;
    }
    
    .program-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--saffron), var(--gold), var(--maroon));
    }
    
    .program-card:hover {
      transform: translateY(-10px);
      border-color: var(--gold);
      box-shadow: 0 20px 50px rgba(0,0,0,.12);
    }
    
    .program-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      color: white;
      margin-bottom: 22px;
    }
    
    .program-card h4 { font-size: 1.4rem; margin-bottom: 12px; }
    .program-card p { color: var(--text-muted); font-size: 0.95rem; margin: 0; line-height: 1.7; }
    
    /* Principal Section */
    .principal-section {
      background: linear-gradient(135deg, var(--maroon), #5a0c26);
      color: white;
      position: relative;
      overflow: hidden;
    }
    
    .principal-section::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 50%;
      height: 100%;
      background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M50 50m-40 0a40,40 0 1,0 80,0a40,40 0 1,0 -80,0' fill='none' stroke='%23ffc107' stroke-width='0.5' opacity='0.2'/%3E%3C/svg%3E");
      opacity: 0.3;
    }
    
    .principal-grid {
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 60px;
      align-items: center;
      position: relative;
      z-index: 2;
    }
    
    .principal-image {
      border-radius: 20px;
      overflow: hidden;
      border: 4px solid var(--gold);
    }
    
    .principal-image img { width: 100%; height: 400px; object-fit: cover; }
    
    .principal-content h3 { color: var(--gold); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; }
    
    .principal-content blockquote {
      font-size: 1.4rem;
      font-style: italic;
      opacity: 0.95;
      margin: 0 0 30px;
      line-height: 1.8;
      border-left: 4px solid var(--gold);
      padding-left: 25px;
    }
    
    .principal-info h4 { font-size: 1.3rem; color: white; margin-bottom: 5px; }
    .principal-info p { color: var(--gold); margin: 0; font-weight: 600; }
    
    /* Facilities - Masonry Grid */
    .facilities-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 25px;
    }
    
    .facility-card {
      background: var(--ivory);
      border-radius: 16px;
      padding: 30px 22px;
      text-align: center;
      transition: all 0.3s;
      border: 2px solid transparent;
    }
    
    .facility-card:hover {
      transform: translateY(-8px);
      border-color: var(--saffron);
      box-shadow: 0 15px 40px rgba(0,0,0,.1);
    }
    
    .facility-icon {
      width: 65px;
      height: 65px;
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 28px;
      color: white;
    }
    
    .facility-card h4 { font-size: 1.1rem; margin-bottom: 10px; }
    .facility-card p { color: var(--text-muted); font-size: 0.88rem; margin: 0; }
    
    /* Gallery - Polaroid Style */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }
    
    .gallery-item {
      background: white;
      padding: 12px 12px 40px;
      border-radius: 5px;
      box-shadow: 0 8px 25px rgba(0,0,0,.1);
      transform: rotate(<?= rand(-4, 4) ?>deg);
      transition: all 0.4s;
    }
    
    .gallery-item:hover { transform: rotate(0deg) scale(1.05); z-index: 5; }
    
    .gallery-item img { width: 100%; height: 180px; object-fit: cover; border-radius: 3px; }
    
    /* News Grid */
    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .news-card {
      background: var(--ivory);
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.3s;
      border: 2px solid transparent;
    }
    
    .news-card:hover {
      transform: translateY(-8px);
      border-color: var(--gold);
      box-shadow: 0 20px 50px rgba(0,0,0,.12);
    }
    
    .news-card-image { width: 100%; height: 180px; object-fit: cover; background: linear-gradient(135deg, var(--saffron), var(--gold)); }
    .news-card-body { padding: 25px; }
    .news-card-date { color: var(--saffron); font-size: 0.85rem; font-weight: 600; margin-bottom: 10px; }
    .news-card h4 { font-size: 1.15rem; margin-bottom: 10px; }
    .news-card p { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
    
    /* Testimonials */
    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .testimonial-card {
      background: var(--ivory);
      border-radius: 20px;
      padding: 35px;
      position: relative;
      border: 2px solid transparent;
      transition: all 0.3s;
    }
    
    .testimonial-card:hover { border-color: var(--gold); }
    
    .testimonial-card::before {
      content: '"';
      position: absolute;
      top: 20px;
      right: 25px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 70px;
      color: var(--gold);
      opacity: 0.4;
      line-height: 1;
    }
    
    .testimonial-stars { color: var(--saffron); margin-bottom: 18px; font-size: 1rem; }
    .testimonial-content { color: var(--text-muted); margin-bottom: 25px; line-height: 1.8; min-height: 80px; font-style: italic; }
    
    .testimonial-author { display: flex; align-items: center; gap: 15px; }
    
    .testimonial-avatar {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--gold);
    }
    
    .testimonial-avatar-placeholder {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 20px;
      color: white;
    }
    
    .testimonial-name { font-weight: 700; font-size: 1rem; color: var(--maroon); }
    .testimonial-role { font-size: 0.85rem; color: var(--text-muted); }
    
    /* Admission Form */
    .admission-section {
      background: linear-gradient(135deg, rgba(255, 107, 53, .08), rgba(255, 193, 7, .05));
    }
    
    .admission-grid {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      gap: 60px;
      align-items: start;
    }
    
    .admission-info h2 { font-size: 2.2rem; margin-bottom: 20px; }
    .admission-info > p { color: var(--text-muted); margin-bottom: 35px; }
    
    .admission-steps { list-style: none; padding: 0; }
    
    .admission-step {
      display: flex;
      gap: 20px;
      margin-bottom: 25px;
      padding-bottom: 25px;
      border-bottom: 1px solid rgba(0,0,0,.1);
    }
    
    .step-number {
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.2rem;
      flex-shrink: 0;
    }
    
    .admission-step h4 { font-size: 1.1rem; margin-bottom: 5px; }
    .admission-step p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
    
    .admission-form {
      background: var(--ivory);
      border-radius: 24px;
      padding: 45px;
      border: 2px solid var(--gold);
      box-shadow: 0 20px 60px rgba(0,0,0,.08);
    }
    
    .admission-form h3 { text-align: center; margin-bottom: 30px; font-size: 1.6rem; }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 10px; color: var(--maroon); }
    
    .form-control, .form-select {
      width: 100%;
      padding: 15px 18px;
      background: white;
      border: 2px solid rgba(0,0,0,.1);
      border-radius: 12px;
      font-family: inherit;
      font-size: 0.95rem;
      color: var(--text);
      transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
      outline: none;
      border-color: var(--saffron);
      box-shadow: 0 0 0 3px rgba(255, 107, 53, .1);
    }
    
    .btn-submit {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 10px;
    }
    
    .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(255, 107, 53, .4); }
    
    .alert-success {
      background: rgba(40, 167, 69, .1);
      border: 2px solid rgba(40, 167, 69, .3);
      color: #28a745;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 25px;
      text-align: center;
    }
    
    .alert-danger {
      background: rgba(220, 53, 69, .1);
      border: 2px solid rgba(220, 53, 69, .3);
      color: #dc3545;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 25px;
    }
    
    /* Contact Section */
    .contact-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }
    
    .contact-card {
      background: var(--ivory);
      border-radius: 16px;
      padding: 30px;
      text-align: center;
      transition: all 0.3s;
      border: 2px solid transparent;
    }
    
    .contact-card:hover { border-color: var(--gold); transform: translateY(-5px); }
    
    .contact-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--saffron), var(--gold));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 18px;
      font-size: 24px;
      color: white;
    }
    
    .contact-card h4 { font-size: 1rem; margin-bottom: 10px; }
    .contact-card p, .contact-card a { color: var(--text-muted); font-size: 0.9rem; margin: 0; text-decoration: none; display: block; }
    .contact-card a:hover { color: var(--saffron); }
    
    /* Footer */
    .footer {
      background: linear-gradient(135deg, var(--maroon), #5a0c26);
      color: white;
      padding: 80px 0 30px;
    }
    
    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 50px;
      margin-bottom: 50px;
    }
    
    .footer-brand h3 { font-size: 1.5rem; margin-bottom: 18px; color: var(--gold); }
    .footer-brand p { opacity: 0.85; font-size: 0.95rem; margin-bottom: 25px; }
    
    .social-links { display: flex; gap: 12px; }
    
    .social-link {
      width: 44px;
      height: 44px;
      background: rgba(255,255,255,.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      font-size: 18px;
      transition: all 0.3s;
    }
    
    .social-link:hover { background: var(--gold); color: var(--maroon); transform: translateY(-3px); }
    
    .footer-links h5 { font-size: 1rem; margin-bottom: 22px; color: var(--gold); }
    .footer-links ul { list-style: none; padding: 0; }
    .footer-links a { color: rgba(255,255,255,.8); text-decoration: none; display: block; padding: 8px 0; font-size: 0.9rem; transition: color 0.3s; }
    .footer-links a:hover { color: var(--gold); }
    
    .footer-bottom {
      text-align: center;
      padding-top: 30px;
      border-top: 1px solid rgba(255,255,255,.15);
      opacity: 0.8;
      font-size: 0.85rem;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
      .about-grid, .admission-grid, .principal-grid { grid-template-columns: 1fr; }
      .principal-image { max-width: 400px; margin: 0 auto; }
      .footer-grid { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 992px) {
      .nav-links {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        height: 100vh;
        background: var(--ivory);
        flex-direction: column;
        padding: 100px 40px 40px;
        transition: left 0.3s;
        z-index: 999;
        gap: 20px;
        box-shadow: 5px 0 30px rgba(0,0,0,.1);
      }
      .nav-links.active { left: 0; }
      .mobile-toggle { display: block; }
      .stats-grid, .contact-grid, .facilities-grid { grid-template-columns: repeat(2, 1fr); }
      .news-grid, .testimonials-grid { grid-template-columns: 1fr; }
      .gallery-grid { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 768px) {
      .section { padding: 70px 0; }
      .stats-grid, .contact-grid, .facilities-grid, .footer-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
      .admission-form { padding: 30px 20px; }
      .gallery-grid { grid-template-columns: 1fr; }
      .gallery-item { transform: none; }
    }
  </style>
</head>
<body>
  <div class="festive-border"></div>
  
  <!-- Navbar -->
  <nav class="navbar-festive" id="navbar">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <a href="<?= e(base_url('/')) ?>" class="nav-brand">
          <div class="brand-logo">
            <?php if (!empty($schoolLogo)): ?>
              <img src="<?= e($schoolLogo) ?>" alt="<?= e($schoolName) ?>">
            <?php else: ?>
              <i class="bi bi-mortarboard"></i>
            <?php endif; ?>
          </div>
          <div class="brand-text">
            <div class="brand-name"><?= e($schoolName) ?></div>
            <div class="brand-tagline"><?= e($schoolTagline) ?></div>
          </div>
        </a>
        
        <ul class="nav-links" id="navLinks">
          <li><a class="nav-link" href="#about">About</a></li>
          <li><a class="nav-link" href="#programs">Programs</a></li>
          <li><a class="nav-link" href="#facilities">Facilities</a></li>
          <li><a class="nav-link" href="#gallery">Gallery</a></li>
          <li><a class="nav-link" href="#contact">Contact</a></li>
          <li><a class="nav-link nav-cta" href="#admission"><i class="bi bi-pencil-square me-1"></i>Apply Now</a></li>
        </ul>
        
        <button class="mobile-toggle" onclick="document.getElementById('navLinks').classList.toggle('active')">
          <i class="bi bi-list"></i>
        </button>
      </div>
    </div>
  </nav>
  
  <!-- Hero Banner Slider (if enabled) -->
  <?php include __DIR__ . '/partials/hero-slider.php'; ?>
  
  <!-- Hero Section - Festival Celebration -->
  <section class="hero-festival">
    <div class="container">
      <div class="hero-content">
        <div class="celebration-badge">
          <i class="bi bi-stars"></i>
          Celebrating Excellence in Education
        </div>
        
        <h1 class="hero-title"><?= e($heroTitle) ?></h1>
        <p class="hero-subtitle"><?= e($heroSubtitle) ?></p>
        
        <div class="hero-buttons">
          <a href="#admission" class="btn-festival btn-saffron">
            <i class="bi bi-pencil-square"></i>
            Start Your Journey
          </a>
          <a href="#about" class="btn-festival btn-outline-maroon">
            <i class="bi bi-info-circle"></i>
            Learn More
          </a>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Stats Section -->
  <section class="stats-section">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-item">
          <div class="stat-number"><?= e($statYears) ?></div>
          <div class="stat-label">Years of Excellence</div>
        </div>
        <div class="stat-item">
          <div class="stat-number"><?= e($statStudents) ?></div>
          <div class="stat-label">Happy Students</div>
        </div>
        <div class="stat-item">
          <div class="stat-number"><?= e($statTeachers) ?></div>
          <div class="stat-label">Expert Teachers</div>
        </div>
        <div class="stat-item">
          <div class="stat-number"><?= e($statResults) ?></div>
          <div class="stat-label">Board Results</div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- About Section -->
  <section class="section" id="about">
    <div class="container">
      <div class="about-grid">
        <div class="about-image">
          <img src="<?= e($aboutImage) ?>" alt="About Us">
        </div>
        <div class="about-content">
          <div class="section-badge">About Us</div>
          <h2><?= e($aboutTitle) ?></h2>
          <p><?= e($aboutText) ?></p>
          <a href="#programs" class="btn-festival btn-saffron">
            <i class="bi bi-arrow-right"></i>
            Explore Programs
          </a>
        </div>
      </div>
    </div>
  </section>
  
  <div class="decorative-divider">
    <div class="divider-line"></div>
    <div class="divider-icon">✿</div>
    <div class="divider-line"></div>
  </div>
  
  <!-- Programs Section -->
  <section class="section section-alt" id="programs">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">Academic Programs</div>
        <h2 class="section-title">Nurturing Future Leaders</h2>
        <p class="section-subtitle">Comprehensive education from early years to senior secondary.</p>
      </div>
      
      <div class="programs-scroll">
        <?php foreach ($programs as $program): ?>
        <div class="program-card">
          <div class="program-icon"><i class="bi <?= e($program['icon']) ?>"></i></div>
          <h4><?= e($program['title']) ?></h4>
          <p><?= e($program['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  
  <!-- Principal Section -->
  <section class="section principal-section">
    <div class="container">
      <div class="principal-grid">
        <div class="principal-image">
          <img src="<?= e($principalImage) ?>" alt="<?= e($principalName) ?>">
        </div>
        <div class="principal-content">
          <h3>Principal's Message</h3>
          <blockquote>"<?= e($principalMessage) ?>"</blockquote>
          <div class="principal-info">
            <h4><?= e($principalName) ?></h4>
            <p><?= e($principalTitle) ?></p>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Facilities Section -->
  <section class="section" id="facilities">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">Our Facilities</div>
        <h2 class="section-title">World-Class Infrastructure</h2>
        <p class="section-subtitle">State-of-the-art facilities for holistic development.</p>
      </div>
      
      <div class="facilities-grid">
        <?php foreach ($facilities as $facility): ?>
        <div class="facility-card">
          <div class="facility-icon"><i class="bi <?= e($facility['icon']) ?>"></i></div>
          <h4><?= e($facility['title']) ?></h4>
          <p><?= e($facility['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  
  <!-- Gallery Section -->
  <section class="section section-alt" id="gallery">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">Gallery</div>
        <h2 class="section-title">Campus Life</h2>
      </div>
      
      <div class="gallery-grid">
        <?php foreach ($GALLERY_IMAGES as $img): ?>
        <div class="gallery-item">
          <img src="<?= e($img['url']) ?>" alt="<?= e($img['title']) ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  
  <!-- News Section -->
  <?php if (!empty($newsArticles)): ?>
  <section class="section">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">Latest Updates</div>
        <h2 class="section-title">News & Announcements</h2>
      </div>
      
      <div class="news-grid">
        <?php foreach (array_slice($newsArticles, 0, 3) as $news): ?>
        <div class="news-card">
          <?php if (!empty($news['image'])): ?>
            <img src="<?= e($news['image']) ?>" alt="<?= e($news['title']) ?>" class="news-card-image">
          <?php else: ?>
            <div class="news-card-image"></div>
          <?php endif; ?>
          <div class="news-card-body">
            <div class="news-card-date"><?= e(!empty($news['date']) ? date('M d, Y', strtotime($news['date'])) : '') ?></div>
            <h4><?= e($news['title']) ?></h4>
            <p><?= e(substr(strip_tags($news['content']), 0, 100)) ?>...</p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  
  <!-- Testimonials Section -->
  <?php if (!empty($testimonials)): ?>
  <section class="section section-alt">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">Testimonials</div>
        <h2 class="section-title">What Parents Say</h2>
      </div>
      
      <div class="testimonials-grid">
        <?php foreach (array_slice($testimonials, 0, 3) as $t): ?>
        <div class="testimonial-card">
          <div class="testimonial-stars">
            <?php for ($i = 0; $i < 5; $i++): ?>
              <i class="bi <?= $i < (int)($t['rating'] ?? 5) ? 'bi-star-fill' : 'bi-star' ?>"></i>
            <?php endfor; ?>
          </div>
          <p class="testimonial-content">"<?= e(substr($t['content'], 0, 150)) ?>"</p>
          <div class="testimonial-author">
            <?php if (!empty($t['image'])): ?>
              <img src="<?= e($t['image']) ?>" alt="<?= e($t['name']) ?>" class="testimonial-avatar">
            <?php else: ?>
              <div class="testimonial-avatar-placeholder"><?= e(strtoupper(substr($t['name'], 0, 1))) ?></div>
            <?php endif; ?>
            <div>
              <div class="testimonial-name"><?= e($t['name']) ?></div>
              <div class="testimonial-role"><?= e($t['role'] ?? 'Parent') ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  
  <!-- Admission Form Section -->
  <section class="section admission-section" id="admission">
    <div class="container">
      <div class="admission-grid">
        <div class="admission-info">
          <div class="section-badge">Enroll Now</div>
          <h2>Begin Your Child's Success Story</h2>
          <p>Join our family of learners and give your child the foundation for a bright future.</p>
          
          <ul class="admission-steps">
            <li class="admission-step">
              <div class="step-number">1</div>
              <div>
                <h4>Fill Application</h4>
                <p>Complete the admission form with required details</p>
              </div>
            </li>
            <li class="admission-step">
              <div class="step-number">2</div>
              <div>
                <h4>Document Verification</h4>
                <p>Submit necessary documents for verification</p>
              </div>
            </li>
            <li class="admission-step">
              <div class="step-number">3</div>
              <div>
                <h4>Interaction Round</h4>
                <p>Brief interaction with student and parents</p>
              </div>
            </li>
            <li class="admission-step">
              <div class="step-number">4</div>
              <div>
                <h4>Admission Confirmation</h4>
                <p>Complete fee payment and secure admission</p>
              </div>
            </li>
          </ul>
        </div>
        
        <div class="admission-form">
          <h3>Admission Application</h3>
          
          <?php if ($success): ?>
            <div class="alert-success">
              <i class="bi bi-check-circle me-2"></i>
              Thank you! Your application has been submitted successfully.
            </div>
          <?php elseif ($error): ?>
            <div class="alert-danger"><?= e($error) ?></div>
          <?php endif; ?>
          
          <form method="post">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Student's Name *</label>
                <input type="text" class="form-control" name="student_name" required>
              </div>
              <div class="form-group">
                <label class="form-label">Parent's Name *</label>
                <input type="text" class="form-control" name="parent_name" required>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input type="tel" class="form-control" name="phone" required>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="dob">
              </div>
              <div class="form-group">
                <label class="form-label">Gender</label>
                <select class="form-select" name="gender">
                  <option value="">Select</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Class Applying For *</label>
                <select class="form-select" name="class_applying" required>
                  <option value="">Select Class</option>
                  <option value="Nursery">Nursery</option>
                  <option value="LKG">LKG</option>
                  <option value="UKG">UKG</option>
                  <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="Class <?= $i ?>">Class <?= $i ?></option>
                  <?php endfor; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Previous School</label>
                <input type="text" class="form-control" name="previous_school">
              </div>
            </div>
            
            <div class="form-group">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="2"></textarea>
            </div>
            
            <button type="submit" class="btn-submit">
              <i class="bi bi-send me-2"></i>Submit Application
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Contact Section -->
  <section class="section" id="contact">
    <div class="container">
      <div class="section-header">
        <div class="section-badge">Get in Touch</div>
        <h2 class="section-title">Contact Us</h2>
      </div>
      
      <div class="contact-grid">
        <div class="contact-card">
          <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
          <h4>Address</h4>
          <p><?= e($address) ?></p>
        </div>
        <div class="contact-card">
          <div class="contact-icon"><i class="bi bi-telephone"></i></div>
          <h4>Phone</h4>
          <a href="tel:<?= e($phone1) ?>"><?= e($phone1) ?></a>
          <a href="tel:<?= e($phone2) ?>"><?= e($phone2) ?></a>
        </div>
        <div class="contact-card">
          <div class="contact-icon"><i class="bi bi-envelope"></i></div>
          <h4>Email</h4>
          <a href="mailto:<?= e($emailContact) ?>"><?= e($emailContact) ?></a>
          <a href="mailto:<?= e($admissionEmail) ?>"><?= e($admissionEmail) ?></a>
        </div>
        <div class="contact-card">
          <div class="contact-icon"><i class="bi bi-clock"></i></div>
          <h4>Office Hours</h4>
          <p><?= e($officeHours) ?></p>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <h3><?= e($schoolName) ?></h3>
          <p><?= e($schoolTagline) ?> - Building character, shaping futures since <?= e(getSetting('school_established', '1998')) ?>.</p>
          <div class="social-links">
            <a href="<?= e($socialFacebook) ?>" class="social-link" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="<?= e($socialInstagram) ?>" class="social-link" target="_blank"><i class="bi bi-instagram"></i></a>
            <a href="<?= e($socialTwitter) ?>" class="social-link" target="_blank"><i class="bi bi-twitter-x"></i></a>
            <a href="<?= e($socialYoutube) ?>" class="social-link" target="_blank"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        
        <div class="footer-links">
          <h5>Quick Links</h5>
          <ul>
            <li><a href="#about">About Us</a></li>
            <li><a href="#programs">Programs</a></li>
            <li><a href="#facilities">Facilities</a></li>
            <li><a href="#gallery">Gallery</a></li>
          </ul>
        </div>
        
        <div class="footer-links">
          <h5>Academics</h5>
          <ul>
            <li><a href="#">Curriculum</a></li>
            <li><a href="#">Faculty</a></li>
            <li><a href="#">Calendar</a></li>
            <li><a href="#">Results</a></li>
          </ul>
        </div>
        
        <div class="footer-links">
          <h5>Contact</h5>
          <ul>
            <li><a href="#contact">Get in Touch</a></li>
            <li><a href="#admission">Admissions</a></li>
            <li><a href="<?= e(base_url('login')) ?>">Staff Login</a></li>
            <li><a href="<?= e(base_url('student_login')) ?>">Student Portal</a></li>
          </ul>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</p>
      </div>
    </div>
  </footer>
  
  <div class="festive-border"></div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Close mobile menu on link click
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        document.getElementById('navLinks').classList.remove('active');
      });
    });
  </script>
</body>
</html>
