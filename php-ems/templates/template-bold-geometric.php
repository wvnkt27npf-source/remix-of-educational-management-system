<?php
/**
 * Bold Geometric Template - NEW SESSION WELCOME
 * Modern, tech-forward design with geometric shapes and gradients
 * Layout: Diagonal sections, hexagonal elements, bold typography, futuristic feel
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
  <meta name="description" content="<?= e($schoolName) ?> - Welcome to a New Era of Learning">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #10b981;
      --primary-dark: #059669;
      --secondary: #06b6d4;
      --accent: #8b5cf6;
      --dark: #0f172a;
      --darker: #020617;
      --gray-800: #1e293b;
      --gray-600: #475569;
      --gray-400: #94a3b8;
      --white: #ffffff;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; scroll-padding-top: 80px; overflow-x: hidden; }
    
    body {
      font-family: 'Sora', sans-serif;
      background: var(--dark);
      color: var(--white);
      line-height: 1.8;
      overflow-x: hidden;
      max-width: 100vw;
    }
    
    /* Slider adjustment for hero section */
    .hero-slider-wrapper + .hero-tech {
      padding-top: 80px;
    }
    
    h1, h2, h3 { font-weight: 700; letter-spacing: -0.02em; }
    
    /* Geometric Background */
    .geo-bg {
      position: fixed;
      inset: 0;
      z-index: 0;
      overflow: hidden;
    }
    
    .geo-shape {
      position: absolute;
      opacity: 0.1;
    }
    
    .geo-shape-1 {
      width: 600px;
      height: 600px;
      top: -200px;
      left: -200px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      animation: geoRotate 60s linear infinite;
    }
    
    .geo-shape-2 {
      width: 400px;
      height: 400px;
      bottom: 10%;
      right: -100px;
      background: linear-gradient(135deg, var(--accent), var(--primary));
      clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
      animation: geoFloat 20s ease-in-out infinite;
    }
    
    .geo-shape-3 {
      width: 200px;
      height: 200px;
      top: 40%;
      left: 5%;
      background: var(--secondary);
      clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
      animation: geoRotate 40s linear infinite reverse;
    }
    
    @keyframes geoRotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @keyframes geoFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-30px); } }
    
    .content-wrapper { position: relative; z-index: 1; }
    
    /* Tech Navbar */
    .navbar-tech {
      background: rgba(15, 23, 42, 0.9);
      backdrop-filter: blur(20px);
      padding: 15px 0;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(255,255,255,.05);
    }
    
    .nav-brand { display: flex; align-items: center; gap: 15px; text-decoration: none; }
    
    .brand-logo {
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      color: white;
      overflow: hidden;
    }
    
    .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
    .brand-name { font-weight: 700; font-size: 1.3rem; color: var(--white); }
    .brand-tagline { font-family: 'Space Mono', monospace; font-size: 0.7rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }
    
    .nav-links { display: flex; align-items: center; gap: 10px; list-style: none; margin: 0; padding: 0; }
    
    .nav-link {
      color: var(--gray-400) !important;
      font-weight: 500;
      padding: 10px 18px !important;
      border-radius: 8px;
      transition: all 0.3s;
      text-decoration: none;
      font-size: 0.9rem;
    }
    
    .nav-link:hover { color: var(--white) !important; background: rgba(255,255,255,.05); }
    
    .nav-cta {
      background: linear-gradient(135deg, var(--primary), var(--secondary)) !important;
      color: var(--white) !important;
      padding: 12px 28px !important;
      font-weight: 600;
    }
    
    .nav-cta:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(16, 185, 129, .3); }
    
    .mobile-toggle {
      display: none;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      font-size: 1.2rem;
      cursor: pointer;
    }
    
    /* HERO - New Session Welcome */
    .hero-welcome {
      min-height: 100vh;
      display: flex;
      align-items: center;
      padding: 140px 0 100px;
      position: relative;
      overflow: hidden;
    }
    
    .hero-welcome::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 60%;
      height: 100%;
      background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
      clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%);
      opacity: 0.1;
    }
    
    .hero-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
    }
    
    .hero-content { position: relative; z-index: 5; }
    
    .welcome-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: rgba(16, 185, 129, .15);
      color: var(--primary);
      padding: 10px 25px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.85rem;
      margin-bottom: 30px;
      border: 1px solid rgba(16, 185, 129, .3);
      font-family: 'Space Mono', monospace;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    
    .hero-title {
      font-size: clamp(3rem, 5.5vw, 4.5rem);
      line-height: 1.1;
      margin-bottom: 25px;
      background: linear-gradient(135deg, var(--white), var(--gray-400));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero-title span {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero-subtitle {
      font-size: 1.15rem;
      color: var(--gray-400);
      margin-bottom: 40px;
      max-width: 500px;
      line-height: 1.8;
    }
    
    .hero-buttons { display: flex; gap: 18px; flex-wrap: wrap; }
    
    .btn-tech {
      padding: 16px 36px;
      font-weight: 600;
      font-size: 0.95rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.4s;
      border: none;
      border-radius: 12px;
    }
    
    .btn-gradient {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: var(--white);
      box-shadow: 0 10px 30px rgba(16, 185, 129, .3);
    }
    
    .btn-gradient:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(16, 185, 129, .4); color: var(--white); }
    
    .btn-outline-tech {
      background: transparent;
      border: 2px solid rgba(255,255,255,.2);
      color: var(--white);
    }
    
    .btn-outline-tech:hover { border-color: var(--primary); color: var(--primary); }
    
    .hero-visual { position: relative; }
    
    .hero-image-container {
      position: relative;
      padding: 20px;
    }
    
    .hero-image-container::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 30px;
      transform: rotate(3deg);
      opacity: 0.2;
    }
    
    .hero-image {
      width: 100%;
      height: 500px;
      object-fit: cover;
      border-radius: 24px;
      position: relative;
      z-index: 2;
    }
    
    /* Floating Tech Cards */
    .tech-float {
      position: absolute;
      background: var(--gray-800);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 16px;
      padding: 20px 25px;
      backdrop-filter: blur(10px);
      z-index: 5;
    }
    
    .tech-float-1 { bottom: 40px; left: -40px; }
    .tech-float-2 { top: 60px; right: -30px; }
    
    .tech-float-icon {
      width: 45px;
      height: 45px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      margin-bottom: 12px;
    }
    
    .tech-float h4 { font-size: 1.8rem; margin-bottom: 5px; }
    .tech-float p { font-size: 0.8rem; color: var(--gray-400); margin: 0; font-family: 'Space Mono', monospace; }
    
    /* Stats Section */
    .stats-section {
      background: linear-gradient(135deg, var(--gray-800), var(--dark));
      padding: 60px 0;
      border-top: 1px solid rgba(255,255,255,.05);
      border-bottom: 1px solid rgba(255,255,255,.05);
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 40px;
    }
    
    .stat-item {
      text-align: center;
      padding: 30px;
      border-radius: 16px;
      background: rgba(255,255,255,.02);
      border: 1px solid rgba(255,255,255,.05);
      transition: all 0.3s;
    }
    
    .stat-item:hover {
      background: rgba(16, 185, 129, .1);
      border-color: rgba(16, 185, 129, .2);
    }
    
    .stat-number {
      font-size: 3rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1;
      margin-bottom: 10px;
    }
    
    .stat-label { font-size: 0.9rem; color: var(--gray-400); }
    
    /* Section Styles */
    .section { padding: 100px 0; }
    .section-darker { background: var(--darker); }
    
    .section-header { margin-bottom: 60px; }
    .section-header.centered { text-align: center; }
    
    .section-label {
      display: inline-block;
      color: var(--primary);
      font-family: 'Space Mono', monospace;
      font-weight: 700;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 15px;
    }
    
    .section-title { font-size: 2.8rem; margin-bottom: 15px; }
    .section-subtitle { font-size: 1.1rem; color: var(--gray-400); max-width: 550px; }
    .section-header.centered .section-subtitle { margin: 0 auto; }
    
    /* About Grid */
    .about-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
    }
    
    .about-image {
      position: relative;
      border-radius: 24px;
      overflow: hidden;
    }
    
    .about-image::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(16, 185, 129, .2), transparent);
      z-index: 2;
    }
    
    .about-image img { width: 100%; height: 450px; object-fit: cover; }
    
    .about-content h2 { font-size: 2.2rem; margin-bottom: 20px; }
    .about-content p { color: var(--gray-400); margin-bottom: 25px; line-height: 1.9; }
    
    /* Programs Grid - Hexagonal Cards */
    .programs-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .program-card {
      background: var(--gray-800);
      border: 1px solid rgba(255,255,255,.05);
      border-radius: 20px;
      padding: 40px 30px;
      transition: all 0.4s;
      position: relative;
      overflow: hidden;
    }
    
    .program-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 0;
      background: linear-gradient(180deg, var(--primary), var(--secondary));
      transition: height 0.4s;
    }
    
    .program-card:hover {
      transform: translateY(-10px);
      border-color: rgba(16, 185, 129, .3);
      box-shadow: 0 25px 60px rgba(0,0,0,.3);
    }
    
    .program-card:hover::before { height: 100%; }
    
    .program-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      margin-bottom: 25px;
    }
    
    .program-card h4 { font-size: 1.3rem; margin-bottom: 12px; }
    .program-card p { color: var(--gray-400); font-size: 0.95rem; margin: 0; line-height: 1.7; }
    
    /* Principal Section */
    .principal-section {
      background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
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
      background: rgba(255,255,255,.05);
      clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%);
    }
    
    .principal-grid {
      display: grid;
      grid-template-columns: 350px 1fr;
      gap: 60px;
      align-items: center;
      position: relative;
      z-index: 2;
    }
    
    .principal-image {
      border-radius: 20px;
      overflow: hidden;
      border: 4px solid rgba(255,255,255,.2);
    }
    
    .principal-image img { width: 100%; height: 420px; object-fit: cover; }
    
    .principal-content blockquote {
      font-size: 1.5rem;
      font-style: italic;
      color: rgba(255,255,255,.95);
      margin: 0 0 35px;
      line-height: 1.7;
    }
    
    .principal-info h4 { font-size: 1.3rem; color: var(--white); margin-bottom: 5px; }
    .principal-info p { color: rgba(255,255,255,.7); margin: 0; font-weight: 500; }
    
    /* Facilities Grid */
    .facilities-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 25px;
    }
    
    .facility-card {
      background: var(--gray-800);
      border: 1px solid rgba(255,255,255,.05);
      border-radius: 16px;
      padding: 35px 25px;
      text-align: center;
      transition: all 0.3s;
    }
    
    .facility-card:hover {
      transform: translateY(-8px);
      border-color: rgba(16, 185, 129, .3);
    }
    
    .facility-icon {
      width: 65px;
      height: 65px;
      background: rgba(16, 185, 129, .1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 28px;
      color: var(--primary);
      transition: all 0.3s;
    }
    
    .facility-card:hover .facility-icon { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: var(--white); }
    
    .facility-card h4 { font-size: 1.05rem; margin-bottom: 10px; }
    .facility-card p { color: var(--gray-400); font-size: 0.85rem; margin: 0; }
    
    /* Gallery Grid */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }
    
    .gallery-item {
      border-radius: 16px;
      overflow: hidden;
      aspect-ratio: 1;
      position: relative;
    }
    
    .gallery-item::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(16, 185, 129, .4), rgba(6, 182, 212, .4));
      opacity: 0;
      transition: opacity 0.3s;
      z-index: 2;
    }
    
    .gallery-item:hover::before { opacity: 1; }
    
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .gallery-item:hover img { transform: scale(1.1); }
    
    /* News Grid */
    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .news-card {
      background: var(--gray-800);
      border: 1px solid rgba(255,255,255,.05);
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.3s;
    }
    
    .news-card:hover {
      transform: translateY(-8px);
      border-color: rgba(16, 185, 129, .2);
    }
    
    .news-card-image { width: 100%; height: 180px; object-fit: cover; background: linear-gradient(135deg, var(--primary), var(--secondary)); }
    .news-card-body { padding: 25px; }
    .news-card-date { color: var(--primary); font-family: 'Space Mono', monospace; font-size: 0.8rem; margin-bottom: 10px; }
    .news-card h4 { font-size: 1.1rem; margin-bottom: 10px; }
    .news-card p { color: var(--gray-400); font-size: 0.9rem; margin: 0; }
    
    /* Testimonials */
    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .testimonial-card {
      background: var(--gray-800);
      border: 1px solid rgba(255,255,255,.05);
      border-radius: 20px;
      padding: 35px;
      position: relative;
    }
    
    .testimonial-card::before {
      content: '"';
      position: absolute;
      top: 20px;
      right: 25px;
      font-size: 60px;
      color: var(--primary);
      opacity: 0.2;
      line-height: 1;
    }
    
    .testimonial-stars { color: var(--primary); margin-bottom: 18px; font-size: 1rem; }
    .testimonial-content { color: var(--gray-400); margin-bottom: 25px; line-height: 1.8; min-height: 80px; }
    
    .testimonial-author { display: flex; align-items: center; gap: 15px; }
    
    .testimonial-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--primary);
    }
    
    .testimonial-avatar-placeholder {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 18px;
    }
    
    .testimonial-name { font-weight: 600; font-size: 0.95rem; }
    .testimonial-role { font-size: 0.8rem; color: var(--gray-400); }
    
    /* Admission Form */
    .admission-section {
      background: linear-gradient(135deg, rgba(16, 185, 129, .05), rgba(6, 182, 212, .05));
    }
    
    .admission-grid {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      gap: 80px;
      align-items: start;
    }
    
    .admission-info h2 { font-size: 2.2rem; margin-bottom: 20px; }
    .admission-info > p { color: var(--gray-400); margin-bottom: 40px; }
    
    .admission-steps { list-style: none; padding: 0; }
    
    .admission-step {
      display: flex;
      gap: 25px;
      margin-bottom: 30px;
      padding-bottom: 30px;
      border-bottom: 1px solid rgba(255,255,255,.05);
    }
    
    .step-number {
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.1rem;
      flex-shrink: 0;
    }
    
    .admission-step h4 { font-size: 1.05rem; margin-bottom: 5px; }
    .admission-step p { color: var(--gray-400); margin: 0; font-size: 0.9rem; }
    
    .admission-form {
      background: var(--gray-800);
      border: 1px solid rgba(255,255,255,.05);
      border-radius: 24px;
      padding: 50px;
    }
    
    .admission-form h3 { text-align: center; margin-bottom: 35px; font-size: 1.5rem; }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    .form-group { margin-bottom: 25px; }
    .form-label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 12px; color: var(--primary); font-family: 'Space Mono', monospace; text-transform: uppercase; letter-spacing: 1px; }
    
    .form-control, .form-select {
      width: 100%;
      padding: 16px 20px;
      background: var(--dark);
      border: 2px solid rgba(255,255,255,.1);
      border-radius: 12px;
      font-family: inherit;
      font-size: 0.95rem;
      color: var(--white);
      transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
      outline: none;
      border-color: var(--primary);
    }
    
    .form-select option { background: var(--dark); }
    
    .btn-submit {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      color: var(--white);
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.4s;
      border-radius: 12px;
      margin-top: 15px;
    }
    
    .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(16, 185, 129, .4); }
    
    .alert-success {
      background: rgba(16, 185, 129, .15);
      border: 2px solid rgba(16, 185, 129, .3);
      color: var(--primary);
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      text-align: center;
    }
    
    .alert-danger {
      background: rgba(239, 68, 68, .15);
      border: 2px solid rgba(239, 68, 68, .3);
      color: #ef4444;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
    }
    
    /* Contact Section */
    .contact-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }
    
    .contact-card {
      background: var(--gray-800);
      border: 1px solid rgba(255,255,255,.05);
      border-radius: 16px;
      padding: 35px;
      text-align: center;
      transition: all 0.3s;
    }
    
    .contact-card:hover { border-color: rgba(16, 185, 129, .2); transform: translateY(-5px); }
    
    .contact-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 24px;
    }
    
    .contact-card h4 { font-size: 1rem; margin-bottom: 10px; }
    .contact-card p, .contact-card a { color: var(--gray-400); font-size: 0.9rem; margin: 0; text-decoration: none; display: block; }
    .contact-card a:hover { color: var(--primary); }
    
    /* Footer */
    .footer {
      background: var(--darker);
      padding: 80px 0 40px;
      border-top: 1px solid rgba(255,255,255,.05);
    }
    
    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 60px;
      margin-bottom: 60px;
    }
    
    .footer-brand h3 { font-size: 1.4rem; margin-bottom: 20px; }
    .footer-brand p { color: var(--gray-400); font-size: 0.95rem; margin-bottom: 25px; }
    
    .social-links { display: flex; gap: 12px; }
    
    .social-link {
      width: 45px;
      height: 45px;
      background: var(--gray-800);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-size: 18px;
      transition: all 0.3s;
    }
    
    .social-link:hover { background: linear-gradient(135deg, var(--primary), var(--secondary)); border-color: transparent; transform: translateY(-3px); }
    
    .footer-links h5 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px; color: var(--primary); font-family: 'Space Mono', monospace; }
    .footer-links ul { list-style: none; padding: 0; }
    .footer-links a { color: var(--gray-400); text-decoration: none; display: block; padding: 8px 0; font-size: 0.9rem; transition: all 0.3s; }
    .footer-links a:hover { color: var(--white); padding-left: 8px; }
    
    .footer-bottom {
      text-align: center;
      padding-top: 40px;
      border-top: 1px solid rgba(255,255,255,.05);
      color: var(--gray-600);
      font-size: 0.85rem;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
      .hero-grid, .about-grid, .admission-grid, .principal-grid { grid-template-columns: 1fr; gap: 60px; }
      .footer-grid { grid-template-columns: repeat(2, 1fr); }
      .tech-float { display: none; }
    }
    
    @media (max-width: 992px) {
      .nav-links {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        height: 100vh;
        background: var(--dark);
        flex-direction: column;
        padding: 100px 40px 40px;
        transition: left 0.3s;
        z-index: 999;
        gap: 20px;
        border-right: 1px solid rgba(255,255,255,.1);
      }
      .nav-links.active { left: 0; }
      .mobile-toggle { display: block; }
      .stats-grid, .contact-grid, .facilities-grid { grid-template-columns: repeat(2, 1fr); }
      .programs-grid, .news-grid, .testimonials-grid { grid-template-columns: 1fr; }
      .gallery-grid { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 768px) {
      .section { padding: 70px 0; }
      .stats-grid, .contact-grid, .facilities-grid, .footer-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
      .admission-form { padding: 30px 20px; }
      .gallery-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="geo-bg">
    <div class="geo-shape geo-shape-1"></div>
    <div class="geo-shape geo-shape-2"></div>
    <div class="geo-shape geo-shape-3"></div>
  </div>
  
  <div class="content-wrapper">
    <!-- Navbar -->
    <nav class="navbar-tech" id="navbar">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center">
          <a href="<?= e(base_url('/')) ?>" class="nav-brand">
            <div class="brand-logo">
              <?php if (!empty($schoolLogo)): ?>
                <img src="<?= e($schoolLogo) ?>" alt="<?= e($schoolName) ?>">
              <?php else: ?>
                <i class="bi bi-rocket"></i>
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
            <li><a class="nav-link nav-cta" href="#admission">Get Started</a></li>
          </ul>
          
          <button class="mobile-toggle" onclick="document.getElementById('navLinks').classList.toggle('active')">
            <i class="bi bi-list"></i>
          </button>
        </div>
      </div>
    </nav>
    
    <!-- Hero Banner Slider (if enabled) -->
    <?php include __DIR__ . '/partials/hero-slider.php'; ?>
    
    <!-- Hero Section - New Session Welcome -->
    <section class="hero-welcome">
      <div class="container">
        <div class="hero-grid">
          <div class="hero-content">
            <div class="welcome-badge">
              <i class="bi bi-rocket-takeoff"></i>
              New Session <?= e($admissionYear) ?>
            </div>
            
            <h1 class="hero-title">
              Welcome to a <span>New Era</span> of Learning
            </h1>
            <p class="hero-subtitle"><?= e($heroSubtitle) ?></p>
            
            <div class="hero-buttons">
              <a href="#admission" class="btn-tech btn-gradient">
                <i class="bi bi-arrow-right-circle"></i>
                Join Now
              </a>
              <a href="#about" class="btn-tech btn-outline-tech">
                <i class="bi bi-play-circle"></i>
                Learn More
              </a>
            </div>
          </div>
          
          <div class="hero-visual">
            <div class="hero-image-container">
              <img src="<?= e($heroImage) ?>" alt="Welcome" class="hero-image">
            </div>
            
            <div class="tech-float tech-float-1">
              <div class="tech-float-icon"><i class="bi bi-trophy"></i></div>
              <h4><?= e($statResults) ?></h4>
              <p>Board Results</p>
            </div>
            
            <div class="tech-float tech-float-2">
              <div class="tech-float-icon"><i class="bi bi-people"></i></div>
              <h4><?= e($statStudents) ?></h4>
              <p>Students</p>
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
            <div class="stat-label">Success Rate</div>
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
            <div class="section-label">// About Us</div>
            <h2><?= e($aboutTitle) ?></h2>
            <p><?= e($aboutText) ?></p>
            <a href="#programs" class="btn-tech btn-gradient">
              Explore Programs <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Programs Section -->
    <section class="section section-darker" id="programs">
      <div class="container">
        <div class="section-header centered">
          <div class="section-label">// Academic Programs</div>
          <h2 class="section-title">Future-Ready Education</h2>
          <p class="section-subtitle">Comprehensive programs designed to prepare students for tomorrow's challenges.</p>
        </div>
        
        <div class="programs-grid">
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
            <div class="section-label" style="color: rgba(255,255,255,.7);">// Principal's Message</div>
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
          <div class="section-label">// Infrastructure</div>
          <h2 class="section-title">Modern Facilities</h2>
          <p class="section-subtitle">State-of-the-art infrastructure for holistic development.</p>
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
    <section class="section section-darker" id="gallery">
      <div class="container">
        <div class="section-header centered">
          <div class="section-label">// Gallery</div>
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
        <div class="section-header centered">
          <div class="section-label">// Latest Updates</div>
          <h2 class="section-title">News & Events</h2>
        </div>
        
        <div class="news-grid">
          <?php foreach ($newsArticles as $index => $news): 
            if ($news['status'] !== 'published') continue;
          ?>
          <div class="news-card" onclick="openNewsModal(<?= $index ?>)" style="cursor: pointer;">
            <?php if (!empty($news['image'])): ?>
              <img src="<?= e($news['image']) ?>" alt="<?= e($news['title']) ?>" class="news-card-image">
            <?php else: ?>
              <div class="news-card-image" style="display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-newspaper" style="font-size: 48px; color: var(--primary); opacity: 0.4;"></i>
              </div>
            <?php endif; ?>
            <div class="news-card-body">
              <div class="news-card-date"><i class="bi bi-calendar3 me-1"></i><?= e(!empty($news['date']) ? date('M d, Y', strtotime($news['date'])) : '') ?></div>
              <h4><?= e($news['title']) ?></h4>
              <p><?= e(substr(strip_tags($news['content']), 0, 150)) ?>...</p>
              <span class="news-read-more" style="color: var(--primary); font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; font-family: 'Space Mono', monospace;">
                Read More <i class="bi bi-arrow-right"></i>
              </span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    
    <!-- News Modal -->
    <div class="news-modal-overlay" id="newsModalOverlay" onclick="closeNewsModal()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,.9); z-index: 9999; padding: 20px; overflow-y: auto;">
      <div class="news-modal" onclick="event.stopPropagation()" style="max-width: 800px; margin: 40px auto; background: var(--gray-800); border-radius: 24px; overflow: hidden; animation: modalFadeIn 0.3s ease; border: 1px solid rgba(255,255,255,.1);">
        <button onclick="closeNewsModal()" style="position: absolute; top: 15px; right: 15px; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; border: none; width: 45px; height: 45px; border-radius: 12px; font-size: 1.2rem; cursor: pointer; z-index: 10;">
          <i class="bi bi-x-lg"></i>
        </button>
        <div id="newsModalImage" style="width: 100%; height: 280px; object-fit: cover; background: linear-gradient(135deg, var(--primary), var(--secondary));"></div>
        <div style="padding: 40px;">
          <div id="newsModalDate" style="color: var(--primary); font-weight: 600; font-size: 0.85rem; margin-bottom: 12px; font-family: 'Space Mono', monospace; text-transform: uppercase; letter-spacing: 1px;"></div>
          <h3 id="newsModalTitle" style="font-size: 1.8rem; margin-bottom: 25px; color: var(--white);"></h3>
          <div id="newsModalContent" style="color: var(--gray-400); line-height: 2; font-size: 1rem; white-space: pre-line;"></div>
        </div>
      </div>
    </div>
    
    <style>
      @keyframes modalFadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
      .news-modal { position: relative; }
    </style>
    
    <script>
      const newsData = <?= json_encode(array_values(array_filter($newsArticles, function($n) { return $n['status'] === 'published'; }))) ?>;
      
      function openNewsModal(index) {
        const news = newsData[index];
        if (!news) return;
        
        const overlay = document.getElementById('newsModalOverlay');
        const imgDiv = document.getElementById('newsModalImage');
        
        if (news.image) {
          imgDiv.innerHTML = '<img src="' + news.image + '" alt="" style="width:100%;height:280px;object-fit:cover;">';
        } else {
          imgDiv.innerHTML = '<div style="width:100%;height:280px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#10b981,#06b6d4);"><i class="bi bi-newspaper" style="font-size:80px;color:white;opacity:0.3;"></i></div>';
        }
        
        document.getElementById('newsModalDate').innerHTML = '<i class="bi bi-calendar3 me-1"></i>' + (news.date ? new Date(news.date).toLocaleDateString('en-US', {year:'numeric',month:'long',day:'numeric'}) : '');
        document.getElementById('newsModalTitle').textContent = news.title;
        document.getElementById('newsModalContent').textContent = news.content;
        
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
      }
      
      function closeNewsModal() {
        document.getElementById('newsModalOverlay').style.display = 'none';
        document.body.style.overflow = '';
      }
      
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeNewsModal();
      });
    </script>
    <?php endif; ?>
    
    <!-- Testimonials Section -->
    <?php if (!empty($testimonials)): ?>
    <section class="section section-darker">
      <div class="container">
        <div class="section-header centered">
          <div class="section-label">// Testimonials</div>
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
            <div class="section-label">// Admissions Open</div>
            <h2>Begin Your Journey</h2>
            <p>Take the first step towards a brighter future. Join our community of learners and innovators.</p>
            
            <ul class="admission-steps">
              <li class="admission-step">
                <div class="step-number">01</div>
                <div>
                  <h4>Apply Online</h4>
                  <p>Complete the digital application form</p>
                </div>
              </li>
              <li class="admission-step">
                <div class="step-number">02</div>
                <div>
                  <h4>Document Submission</h4>
                  <p>Upload required documents</p>
                </div>
              </li>
              <li class="admission-step">
                <div class="step-number">03</div>
                <div>
                  <h4>Assessment</h4>
                  <p>Attend evaluation session</p>
                </div>
              </li>
              <li class="admission-step">
                <div class="step-number">04</div>
                <div>
                  <h4>Enrollment</h4>
                  <p>Complete registration</p>
                </div>
              </li>
            </ul>
          </div>
          
          <div class="admission-form">
            <h3>Application Form</h3>
            
            <?php if ($success): ?>
              <div class="alert-success">
                <i class="bi bi-check-circle me-2"></i>
                Application submitted successfully!
              </div>
            <?php elseif ($error): ?>
              <div class="alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            
            <form method="post">
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
                    <option value="">Select Class</option>
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
                <i class="bi bi-rocket me-2"></i>Submit Application
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Contact Section -->
    <section class="section section-darker" id="contact">
      <div class="container">
        <div class="section-header centered">
          <div class="section-label">// Contact</div>
          <h2 class="section-title">Get in Touch</h2>
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
          </div>
          <div class="contact-card">
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
            <p><?= e($schoolTagline) ?> - Shaping the future since <?= e(getSetting('school_established', '1998')) ?>.</p>
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
  </div>
  
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
