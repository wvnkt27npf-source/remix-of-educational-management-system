<?php
/**
 * Minimal Clean Template - ACHIEVEMENT SHOWCASE
 * Ultra-minimal, elegant design focusing on excellence and achievements
 * Layout: Full-width sections, large typography, asymmetric grids, editorial style
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
  <meta name="description" content="<?= e($schoolName) ?> - Excellence in Education">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display:wght@400&display=swap" rel="stylesheet">
  <style>
    :root {
      --royal: #4f46e5;
      --gold: #f59e0b;
      --dark: #111827;
      --gray-900: #1f2937;
      --gray-700: #374151;
      --gray-500: #6b7280;
      --gray-300: #d1d5db;
      --gray-100: #f3f4f6;
      --white: #ffffff;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; scroll-padding-top: 80px; }
    
    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--white);
      color: var(--gray-700);
      line-height: 1.8;
      font-size: 17px;
    }
    
    h1, h2, h3 { font-family: 'DM Serif Display', serif; font-weight: 400; color: var(--dark); letter-spacing: -0.02em; }
    h4, h5, h6 { font-family: 'DM Sans', sans-serif; font-weight: 600; color: var(--dark); }
    
    /* Minimal Navbar */
    .navbar-minimal {
      background: var(--white);
      padding: 20px 0;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      transition: all 0.3s;
    }
    
    .navbar-minimal.scrolled {
      padding: 12px 0;
      box-shadow: 0 1px 0 var(--gray-300);
    }
    
    .nav-brand { display: flex; align-items: center; gap: 15px; text-decoration: none; }
    
    .brand-logo {
      width: 48px;
      height: 48px;
      background: var(--dark);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      color: var(--gold);
      overflow: hidden;
    }
    
    .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
    .brand-name { font-family: 'DM Serif Display', serif; font-size: 1.4rem; color: var(--dark); }
    
    .nav-links { display: flex; align-items: center; gap: 40px; list-style: none; margin: 0; padding: 0; }
    
    .nav-link {
      color: var(--gray-500) !important;
      font-weight: 500;
      font-size: 0.95rem;
      text-decoration: none;
      transition: color 0.3s;
      padding: 0 !important;
    }
    
    .nav-link:hover { color: var(--dark) !important; }
    
    .nav-cta {
      color: var(--white) !important;
      background: var(--dark);
      padding: 12px 28px !important;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .nav-cta:hover { background: var(--royal); }
    
    .mobile-toggle {
      display: none;
      background: transparent;
      border: none;
      color: var(--dark);
      font-size: 1.5rem;
      cursor: pointer;
    }
    
    /* HERO - Achievement Showcase Style */
    .hero-achievement {
      min-height: 100vh;
      display: flex;
      align-items: center;
      padding: 160px 0 100px;
      position: relative;
      overflow: hidden;
    }
    
    .hero-achievement::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 50%;
      height: 100%;
      background: var(--gray-100);
      z-index: -1;
    }
    
    .hero-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
    }
    
    .hero-content { max-width: 540px; }
    
    .achievement-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--gold);
      color: var(--dark);
      padding: 8px 20px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 30px;
    }
    
    .hero-title {
      font-size: clamp(3rem, 5vw, 4.5rem);
      line-height: 1.1;
      margin-bottom: 25px;
    }
    
    .hero-subtitle {
      font-size: 1.15rem;
      color: var(--gray-500);
      margin-bottom: 40px;
      line-height: 1.8;
    }
    
    .hero-buttons { display: flex; gap: 16px; flex-wrap: wrap; }
    
    .btn-minimal {
      padding: 16px 32px;
      font-weight: 600;
      font-size: 0.95rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.3s;
      border-radius: 8px;
    }
    
    .btn-dark {
      background: var(--dark);
      color: var(--white);
    }
    
    .btn-dark:hover { background: var(--royal); color: var(--white); transform: translateY(-2px); }
    
    .btn-outline {
      background: transparent;
      border: 2px solid var(--gray-300);
      color: var(--dark);
    }
    
    .btn-outline:hover { border-color: var(--dark); color: var(--dark); }
    
    .hero-visual { position: relative; }
    
    .hero-image-main {
      width: 100%;
      height: 550px;
      object-fit: cover;
      border-radius: 16px;
    }
    
    /* Achievement Cards floating */
    .achievement-float {
      position: absolute;
      background: var(--white);
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 20px 50px rgba(0,0,0,.1);
    }
    
    .achievement-float-1 { bottom: 60px; left: -60px; }
    .achievement-float-2 { top: 40px; right: -40px; }
    
    .achievement-float-icon {
      width: 50px;
      height: 50px;
      background: var(--gold);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 15px;
    }
    
    .achievement-float h4 { font-size: 2rem; margin-bottom: 5px; color: var(--dark); }
    .achievement-float p { font-size: 0.85rem; color: var(--gray-500); margin: 0; }
    
    /* Stats Section */
    .stats-section {
      background: var(--dark);
      padding: 80px 0;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 50px;
    }
    
    .stat-item { text-align: center; }
    
    .stat-number {
      font-family: 'DM Serif Display', serif;
      font-size: 4rem;
      color: var(--gold);
      line-height: 1;
      margin-bottom: 10px;
    }
    
    .stat-label { font-size: 0.95rem; color: rgba(255,255,255,.7); font-weight: 500; }
    
    /* Section Styles */
    .section { padding: 120px 0; }
    .section-gray { background: var(--gray-100); }
    
    .section-header { margin-bottom: 80px; }
    .section-header.centered { text-align: center; }
    
    .section-label {
      display: inline-block;
      color: var(--royal);
      font-weight: 600;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 20px;
    }
    
    .section-title { font-size: 3rem; margin-bottom: 20px; }
    .section-subtitle { font-size: 1.1rem; color: var(--gray-500); max-width: 550px; }
    .section-header.centered .section-subtitle { margin: 0 auto; }
    
    /* About - Editorial Split */
    .about-split {
      display: grid;
      grid-template-columns: 1fr 1fr;
      min-height: 600px;
    }
    
    .about-image {
      position: relative;
      overflow: hidden;
    }
    
    .about-image img { width: 100%; height: 100%; object-fit: cover; }
    
    .about-content {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 80px;
      background: var(--gray-100);
    }
    
    .about-content h2 { font-size: 2.5rem; margin-bottom: 25px; }
    .about-content p { color: var(--gray-500); margin-bottom: 30px; line-height: 1.9; }
    
    /* Programs - List Style */
    .programs-list { border-top: 1px solid var(--gray-300); }
    
    .program-item {
      display: grid;
      grid-template-columns: 80px 1fr auto;
      gap: 40px;
      align-items: center;
      padding: 40px 0;
      border-bottom: 1px solid var(--gray-300);
      transition: all 0.3s;
    }
    
    .program-item:hover { padding-left: 20px; background: var(--gray-100); }
    
    .program-number {
      font-family: 'DM Serif Display', serif;
      font-size: 2.5rem;
      color: var(--gray-300);
    }
    
    .program-item:hover .program-number { color: var(--royal); }
    
    .program-info h4 { font-size: 1.4rem; margin-bottom: 8px; }
    .program-info p { color: var(--gray-500); margin: 0; }
    
    .program-arrow {
      width: 50px;
      height: 50px;
      border: 2px solid var(--gray-300);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gray-500);
      transition: all 0.3s;
    }
    
    .program-item:hover .program-arrow { background: var(--dark); border-color: var(--dark); color: var(--white); }
    
    /* Principal Section */
    .principal-section { background: var(--dark); color: var(--white); }
    
    .principal-grid {
      display: grid;
      grid-template-columns: 400px 1fr;
      gap: 80px;
      align-items: center;
    }
    
    .principal-image { border-radius: 16px; overflow: hidden; }
    .principal-image img { width: 100%; height: 500px; object-fit: cover; }
    
    .principal-content blockquote {
      font-family: 'DM Serif Display', serif;
      font-size: 1.8rem;
      color: var(--white);
      line-height: 1.6;
      margin: 0 0 40px;
    }
    
    .principal-info h4 { font-size: 1.2rem; color: var(--white); margin-bottom: 5px; }
    .principal-info p { color: var(--gold); margin: 0; font-weight: 600; }
    
    /* Facilities Grid */
    .facilities-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }
    
    .facility-card {
      background: var(--white);
      padding: 40px 30px;
      border-radius: 12px;
      transition: all 0.3s;
      border: 1px solid var(--gray-300);
    }
    
    .facility-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(0,0,0,.08);
      border-color: transparent;
    }
    
    .facility-icon {
      width: 60px;
      height: 60px;
      background: var(--gray-100);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      color: var(--royal);
      margin-bottom: 25px;
      transition: all 0.3s;
    }
    
    .facility-card:hover .facility-icon { background: var(--royal); color: var(--white); }
    
    .facility-card h4 { font-size: 1.1rem; margin-bottom: 10px; }
    .facility-card p { color: var(--gray-500); font-size: 0.9rem; margin: 0; }
    
    /* Gallery - Asymmetric Grid */
    .gallery-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      grid-template-rows: repeat(2, 250px);
      gap: 20px;
    }
    
    .gallery-item { border-radius: 12px; overflow: hidden; }
    .gallery-item:first-child { grid-row: span 2; }
    
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .gallery-item:hover img { transform: scale(1.05); }
    
    /* News Grid */
    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 40px;
    }
    
    .news-card { transition: all 0.3s; }
    .news-card:hover { transform: translateY(-5px); }
    
    .news-card-image { width: 100%; height: 220px; object-fit: cover; border-radius: 12px; margin-bottom: 25px; background: var(--gray-100); }
    .news-card-date { color: var(--royal); font-size: 0.85rem; font-weight: 600; margin-bottom: 12px; }
    .news-card h4 { font-size: 1.2rem; margin-bottom: 12px; }
    .news-card p { color: var(--gray-500); font-size: 0.95rem; margin: 0; }
    
    /* Testimonials - Large Quote Style */
    .testimonial-large {
      text-align: center;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .testimonial-quote {
      font-family: 'DM Serif Display', serif;
      font-size: 2rem;
      color: var(--dark);
      line-height: 1.6;
      margin-bottom: 40px;
    }
    
    .testimonial-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      margin: 0 auto 20px;
    }
    
    .testimonial-avatar-placeholder {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--royal);
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: 600;
      margin: 0 auto 20px;
    }
    
    .testimonial-name { font-weight: 600; font-size: 1.1rem; color: var(--dark); margin-bottom: 5px; }
    .testimonial-role { font-size: 0.9rem; color: var(--gray-500); }
    
    /* Admission Form */
    .admission-split {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    
    .admission-info {
      background: var(--dark);
      color: var(--white);
      padding: 100px 80px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    
    .admission-info h2 { font-size: 2.5rem; color: var(--white); margin-bottom: 25px; }
    .admission-info > p { color: rgba(255,255,255,.7); margin-bottom: 50px; }
    
    .admission-steps { list-style: none; padding: 0; }
    
    .admission-step {
      display: flex;
      gap: 25px;
      margin-bottom: 35px;
      padding-bottom: 35px;
      border-bottom: 1px solid rgba(255,255,255,.1);
    }
    
    .step-number {
      width: 45px;
      height: 45px;
      border: 2px solid var(--gold);
      color: var(--gold);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      flex-shrink: 0;
    }
    
    .admission-step h4 { color: var(--white); font-size: 1rem; margin-bottom: 5px; }
    .admission-step p { color: rgba(255,255,255,.6); margin: 0; font-size: 0.9rem; }
    
    .admission-form-wrapper {
      background: var(--white);
      padding: 100px 80px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    
    .admission-form h3 { font-size: 1.8rem; margin-bottom: 40px; }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    .form-group { margin-bottom: 25px; }
    .form-label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 10px; color: var(--dark); text-transform: uppercase; letter-spacing: 1px; }
    
    .form-control, .form-select {
      width: 100%;
      padding: 16px 0;
      border: none;
      border-bottom: 2px solid var(--gray-300);
      background: transparent;
      font-family: inherit;
      font-size: 1rem;
      color: var(--dark);
      transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
      outline: none;
      border-color: var(--dark);
    }
    
    .btn-submit {
      width: 100%;
      padding: 18px;
      background: var(--dark);
      border: none;
      color: var(--white);
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
      border-radius: 8px;
      margin-top: 20px;
    }
    
    .btn-submit:hover { background: var(--royal); }
    
    .alert-success {
      background: rgba(16, 185, 129, .1);
      border-left: 4px solid #10b981;
      padding: 20px;
      margin-bottom: 30px;
      color: #10b981;
    }
    
    .alert-danger {
      background: rgba(239, 68, 68, .1);
      border-left: 4px solid #ef4444;
      padding: 20px;
      margin-bottom: 30px;
      color: #ef4444;
    }
    
    /* Contact Section */
    .contact-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 50px;
    }
    
    .contact-item { text-align: center; }
    .contact-icon { font-size: 2rem; color: var(--royal); margin-bottom: 20px; }
    .contact-item h4 { font-size: 1rem; margin-bottom: 10px; }
    .contact-item p, .contact-item a { color: var(--gray-500); font-size: 0.95rem; margin: 0; text-decoration: none; display: block; }
    .contact-item a:hover { color: var(--dark); }
    
    /* Footer */
    .footer {
      background: var(--dark);
      color: var(--white);
      padding: 80px 0 40px;
    }
    
    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 60px;
      margin-bottom: 60px;
    }
    
    .footer-brand h3 { font-size: 1.4rem; margin-bottom: 20px; color: var(--white); }
    .footer-brand p { color: rgba(255,255,255,.7); font-size: 0.95rem; margin-bottom: 25px; }
    
    .social-links { display: flex; gap: 15px; }
    
    .social-link {
      width: 45px;
      height: 45px;
      border: 1px solid rgba(255,255,255,.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-size: 18px;
      transition: all 0.3s;
    }
    
    .social-link:hover { background: var(--white); color: var(--dark); }
    
    .footer-links h5 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px; color: var(--white); }
    .footer-links ul { list-style: none; padding: 0; }
    .footer-links a { color: rgba(255,255,255,.7); text-decoration: none; display: block; padding: 8px 0; font-size: 0.95rem; transition: color 0.3s; }
    .footer-links a:hover { color: var(--white); }
    
    .footer-bottom {
      text-align: center;
      padding-top: 40px;
      border-top: 1px solid rgba(255,255,255,.1);
      color: rgba(255,255,255,.5);
      font-size: 0.85rem;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
      .hero-grid, .principal-grid { grid-template-columns: 1fr; gap: 60px; }
      .about-split, .admission-split { grid-template-columns: 1fr; }
      .about-image { height: 400px; }
      .footer-grid { grid-template-columns: repeat(2, 1fr); }
      .achievement-float { display: none; }
    }
    
    @media (max-width: 992px) {
      .nav-links {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        height: 100vh;
        background: var(--white);
        flex-direction: column;
        padding: 100px 40px 40px;
        transition: left 0.3s;
        z-index: 999;
        gap: 30px;
        box-shadow: 5px 0 30px rgba(0,0,0,.05);
      }
      .nav-links.active { left: 0; }
      .mobile-toggle { display: block; }
      .stats-grid, .contact-grid, .facilities-grid { grid-template-columns: repeat(2, 1fr); }
      .news-grid { grid-template-columns: 1fr; }
      .gallery-grid { grid-template-columns: repeat(2, 1fr); grid-template-rows: auto; }
      .gallery-item:first-child { grid-row: auto; }
      .program-item { grid-template-columns: 60px 1fr; }
      .program-arrow { display: none; }
    }
    
    @media (max-width: 768px) {
      .section { padding: 80px 0; }
      .about-content, .admission-info, .admission-form-wrapper { padding: 50px 20px; }
      .stats-grid, .contact-grid, .facilities-grid, .footer-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
      .hero-achievement::before { display: none; }
      .gallery-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar-minimal" id="navbar">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <a href="<?= e(base_url('/')) ?>" class="nav-brand">
          <div class="brand-logo">
            <?php if (!empty($schoolLogo)): ?>
              <img src="<?= e($schoolLogo) ?>" alt="<?= e($schoolName) ?>">
            <?php else: ?>
              <i class="bi bi-trophy"></i>
            <?php endif; ?>
          </div>
          <div class="brand-name"><?= e($schoolName) ?></div>
        </a>
        
        <ul class="nav-links" id="navLinks">
          <li><a class="nav-link" href="#about">About</a></li>
          <li><a class="nav-link" href="#programs">Programs</a></li>
          <li><a class="nav-link" href="#facilities">Facilities</a></li>
          <li><a class="nav-link" href="#gallery">Gallery</a></li>
          <li><a class="nav-link" href="#contact">Contact</a></li>
          <li><a class="nav-link nav-cta" href="#admission">Apply Now</a></li>
        </ul>
        
        <button class="mobile-toggle" onclick="document.getElementById('navLinks').classList.toggle('active')">
          <i class="bi bi-list"></i>
        </button>
      </div>
    </div>
  </nav>
  
  <!-- Hero Section - Achievement Showcase -->
  <section class="hero-achievement">
    <div class="container">
      <div class="hero-grid">
        <div class="hero-content">
          <div class="achievement-badge">
            <i class="bi bi-trophy"></i>
            #1 Ranked School
          </div>
          
          <h1 class="hero-title"><?= e($heroTitle) ?></h1>
          <p class="hero-subtitle"><?= e($heroSubtitle) ?></p>
          
          <div class="hero-buttons">
            <a href="#admission" class="btn-minimal btn-dark">
              Apply for Admission
              <i class="bi bi-arrow-right"></i>
            </a>
            <a href="#about" class="btn-minimal btn-outline">
              Learn More
            </a>
          </div>
        </div>
        
        <div class="hero-visual">
          <img src="<?= e($heroImage) ?>" alt="Excellence" class="hero-image-main">
          
          <div class="achievement-float achievement-float-1">
            <div class="achievement-float-icon"><i class="bi bi-star-fill"></i></div>
            <h4><?= e($statResults) ?></h4>
            <p>Board Results</p>
          </div>
          
          <div class="achievement-float achievement-float-2">
            <div class="achievement-float-icon"><i class="bi bi-award"></i></div>
            <h4><?= e($statYears) ?></h4>
            <p>Years of Excellence</p>
          </div>
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
          <div class="stat-label">Students Enrolled</div>
        </div>
        <div class="stat-item">
          <div class="stat-number"><?= e($statTeachers) ?></div>
          <div class="stat-label">Expert Faculty</div>
        </div>
        <div class="stat-item">
          <div class="stat-number"><?= e($statResults) ?></div>
          <div class="stat-label">Board Results</div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- About Section -->
  <section id="about">
    <div class="about-split">
      <div class="about-image">
        <img src="<?= e($aboutImage) ?>" alt="About Us">
      </div>
      <div class="about-content">
        <div class="section-label">About Us</div>
        <h2><?= e($aboutTitle) ?></h2>
        <p><?= e($aboutText) ?></p>
        <a href="#programs" class="btn-minimal btn-dark">
          Explore Programs <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>
  
  <!-- Programs Section -->
  <section class="section section-gray" id="programs">
    <div class="container">
      <div class="section-header">
        <div class="section-label">Academic Excellence</div>
        <h2 class="section-title">Our Programs</h2>
      </div>
      
      <div class="programs-list">
        <?php $num = 1; foreach ($programs as $program): ?>
        <div class="program-item">
          <div class="program-number"><?= str_pad($num++, 2, '0', STR_PAD_LEFT) ?></div>
          <div class="program-info">
            <h4><?= e($program['title']) ?></h4>
            <p><?= e($program['desc']) ?></p>
          </div>
          <div class="program-arrow"><i class="bi bi-arrow-right"></i></div>
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
          <div class="section-label" style="color: var(--gold);">Principal's Message</div>
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
      <div class="section-header centered">
        <div class="section-label">Infrastructure</div>
        <h2 class="section-title">World-Class Facilities</h2>
        <p class="section-subtitle">State-of-the-art infrastructure designed to support every aspect of learning.</p>
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
  <section class="section section-gray" id="gallery">
    <div class="container">
      <div class="section-header centered">
        <div class="section-label">Gallery</div>
        <h2 class="section-title">Campus Life</h2>
      </div>
      
      <div class="gallery-grid">
        <?php foreach (array_slice($GALLERY_IMAGES, 0, 5) as $img): ?>
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
      <div class="section-header centered">
        <div class="section-label">Latest Updates</div>
        <h2 class="section-title">News & Events</h2>
      </div>
      
      <div class="news-grid">
        <?php foreach (array_slice($newsArticles, 0, 3) as $news): ?>
        <div class="news-card">
          <?php if (!empty($news['image'])): ?>
            <img src="<?= e($news['image']) ?>" alt="<?= e($news['title']) ?>" class="news-card-image">
          <?php else: ?>
            <div class="news-card-image"></div>
          <?php endif; ?>
          <div class="news-card-date"><?= e(!empty($news['date']) ? date('M d, Y', strtotime($news['date'])) : '') ?></div>
          <h4><?= e($news['title']) ?></h4>
          <p><?= e(substr(strip_tags($news['content']), 0, 100)) ?>...</p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  
  <!-- Testimonials Section -->
  <?php if (!empty($testimonials)): ?>
  <section class="section section-gray">
    <div class="container">
      <div class="section-header centered">
        <div class="section-label">Testimonials</div>
        <h2 class="section-title">What Parents Say</h2>
      </div>
      
      <?php $t = $testimonials[0]; ?>
      <div class="testimonial-large">
        <p class="testimonial-quote">"<?= e($t['content']) ?>"</p>
        <?php if (!empty($t['image'])): ?>
          <img src="<?= e($t['image']) ?>" alt="<?= e($t['name']) ?>" class="testimonial-avatar">
        <?php else: ?>
          <div class="testimonial-avatar-placeholder"><?= e(strtoupper(substr($t['name'], 0, 1))) ?></div>
        <?php endif; ?>
        <div class="testimonial-name"><?= e($t['name']) ?></div>
        <div class="testimonial-role"><?= e($t['role'] ?? 'Parent') ?></div>
      </div>
    </div>
  </section>
  <?php endif; ?>
  
  <!-- Admission Form Section -->
  <section id="admission">
    <div class="admission-split">
      <div class="admission-info">
        <div class="section-label" style="color: var(--gold);">Admissions</div>
        <h2>Join Our Legacy of Excellence</h2>
        <p>Take the first step towards a brighter future. Apply now and become part of our distinguished community.</p>
        
        <ul class="admission-steps">
          <li class="admission-step">
            <div class="step-number">1</div>
            <div>
              <h4>Submit Application</h4>
              <p>Complete the online application form</p>
            </div>
          </li>
          <li class="admission-step">
            <div class="step-number">2</div>
            <div>
              <h4>Document Review</h4>
              <p>Submit required documents for verification</p>
            </div>
          </li>
          <li class="admission-step">
            <div class="step-number">3</div>
            <div>
              <h4>Assessment</h4>
              <p>Appear for evaluation and interaction</p>
            </div>
          </li>
          <li class="admission-step">
            <div class="step-number">4</div>
            <div>
              <h4>Enrollment</h4>
              <p>Complete formalities and join us</p>
            </div>
          </li>
        </ul>
      </div>
      
      <div class="admission-form-wrapper">
        <form class="admission-form" method="post">
          <h3>Application Form</h3>
          
          <?php if ($success): ?>
            <div class="alert-success">
              <i class="bi bi-check-circle me-2"></i>
              Your application has been submitted successfully.
            </div>
          <?php elseif ($error): ?>
            <div class="alert-danger"><?= e($error) ?></div>
          <?php endif; ?>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Student Name *</label>
              <input type="text" class="form-control" name="student_name" required>
            </div>
            <div class="form-group">
              <label class="form-label">Parent Name *</label>
              <input type="text" class="form-control" name="parent_name" required>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Email *</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
              <label class="form-label">Phone *</label>
              <input type="tel" class="form-control" name="phone" required>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Date of Birth</label>
              <input type="date" class="form-control" name="dob">
            </div>
            <div class="form-group">
              <label class="form-label">Class *</label>
              <select class="form-select" name="class_applying" required>
                <option value="">Select</option>
                <option value="Nursery">Nursery</option>
                <option value="LKG">LKG</option>
                <option value="UKG">UKG</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                  <option value="Class <?= $i ?>">Class <?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">Previous School</label>
            <input type="text" class="form-control" name="previous_school">
          </div>
          
          <input type="hidden" name="gender" value="">
          <input type="hidden" name="address" value="">
          
          <button type="submit" class="btn-submit">
            Submit Application
          </button>
        </form>
      </div>
    </div>
  </section>
  
  <!-- Contact Section -->
  <section class="section" id="contact">
    <div class="container">
      <div class="section-header centered">
        <div class="section-label">Contact</div>
        <h2 class="section-title">Get in Touch</h2>
      </div>
      
      <div class="contact-grid">
        <div class="contact-item">
          <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
          <h4>Address</h4>
          <p><?= e($address) ?></p>
        </div>
        <div class="contact-item">
          <div class="contact-icon"><i class="bi bi-telephone"></i></div>
          <h4>Phone</h4>
          <a href="tel:<?= e($phone1) ?>"><?= e($phone1) ?></a>
          <a href="tel:<?= e($phone2) ?>"><?= e($phone2) ?></a>
        </div>
        <div class="contact-item">
          <div class="contact-icon"><i class="bi bi-envelope"></i></div>
          <h4>Email</h4>
          <a href="mailto:<?= e($emailContact) ?>"><?= e($emailContact) ?></a>
        </div>
        <div class="contact-item">
          <div class="contact-icon"><i class="bi bi-clock"></i></div>
          <h4>Hours</h4>
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
          <p><?= e($schoolTagline) ?> - Excellence since <?= e(getSetting('school_established', '1998')) ?>.</p>
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
          <h5>Access</h5>
          <ul>
            <li><a href="#contact">Contact Us</a></li>
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
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.getElementById('navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
    
    // Close mobile menu on link click
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        document.getElementById('navLinks').classList.remove('active');
      });
    });
  </script>
</body>
</html>
