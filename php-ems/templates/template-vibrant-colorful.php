<?php
/**
 * Vibrant Colorful Template - SUMMER VACATION / KIDS FOCUS
 * Playful, energetic design with bright colors and fun animations
 * Layout: Wavy sections, floating elements, cartoon-style cards, playful typography
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
  <meta name="description" content="<?= e($schoolName) ?> - Fun Learning Environment">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --sky: #00b4d8;
      --sun: #ffb703;
      --grass: #06d6a0;
      --coral: #ff6b6b;
      --purple: #9b5de5;
      --pink: #ff69b4;
      --sand: #fff3e0;
      --white: #ffffff;
      --dark: #2d3436;
      --text: #2d3436;
      --text-muted: #636e72;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; scroll-padding-top: 80px; overflow-x: hidden; }
    
    body {
      font-family: 'Quicksand', sans-serif;
      background: var(--sand);
      color: var(--text);
      line-height: 1.8;
      overflow-x: hidden;
      max-width: 100vw;
    }
    
    /* Slider adjustment for hero section */
    .hero-slider-wrapper + .hero-fun {
      padding-top: 80px;
    }
    
    h1, h2, h3, h4, h5 { font-family: 'Baloo 2', cursive; font-weight: 700; color: var(--dark); }
    
    /* Floating Fun Elements */
    .floating-decorations {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      overflow: hidden;
    }
    
    .float-item {
      position: absolute;
      font-size: 40px;
      opacity: 0.15;
      animation: floatBounce 10s ease-in-out infinite;
    }
    
    .float-item:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; }
    .float-item:nth-child(2) { top: 30%; right: 8%; animation-delay: 2s; }
    .float-item:nth-child(3) { bottom: 20%; left: 10%; animation-delay: 4s; }
    .float-item:nth-child(4) { bottom: 35%; right: 5%; animation-delay: 6s; }
    .float-item:nth-child(5) { top: 50%; left: 3%; animation-delay: 1s; }
    
    @keyframes floatBounce {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      25% { transform: translateY(-15px) rotate(5deg); }
      50% { transform: translateY(0) rotate(0deg); }
      75% { transform: translateY(-10px) rotate(-5deg); }
    }
    
    .content-wrapper { position: relative; z-index: 1; }
    
    /* Rainbow Navbar */
    .navbar-rainbow {
      background: var(--white);
      padding: 10px 0;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      box-shadow: 0 4px 20px rgba(0,0,0,.08);
    }
    
    .navbar-rainbow::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--coral), var(--sun), var(--grass), var(--sky), var(--purple), var(--pink));
    }
    
    .nav-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
    
    .brand-logo {
      width: 55px;
      height: 55px;
      background: linear-gradient(135deg, var(--sky), var(--grass));
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      color: white;
      transform: rotate(-8deg);
      transition: transform 0.4s;
      overflow: hidden;
    }
    
    .brand-logo:hover { transform: rotate(0deg) scale(1.1); }
    .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
    
    .brand-name { font-family: 'Baloo 2', cursive; font-size: 1.5rem; font-weight: 700; color: var(--sky); }
    .brand-tagline { font-size: 0.72rem; color: var(--sun); font-weight: 600; }
    
    .nav-links { display: flex; align-items: center; gap: 8px; list-style: none; margin: 0; padding: 0; }
    
    .nav-link {
      color: var(--text) !important;
      font-weight: 600;
      padding: 10px 18px !important;
      border-radius: 30px;
      transition: all 0.3s;
      text-decoration: none;
      font-size: 0.95rem;
    }
    
    .nav-link:hover { background: var(--sand); color: var(--sky) !important; }
    
    .nav-cta {
      background: linear-gradient(135deg, var(--sun), var(--coral)) !important;
      color: white !important;
      padding: 12px 28px !important;
      box-shadow: 0 5px 20px rgba(255, 107, 107, .3);
    }
    
    .nav-cta:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 8px 30px rgba(255, 107, 107, .4); }
    
    .mobile-toggle {
      display: none;
      background: linear-gradient(135deg, var(--sky), var(--grass));
      border: none;
      color: white;
      padding: 10px 15px;
      border-radius: 12px;
      font-size: 1.2rem;
      cursor: pointer;
    }
    
    /* HERO - Summer Vacation Style */
    .hero-summer {
      min-height: 100vh;
      display: flex;
      align-items: center;
      background: linear-gradient(180deg, var(--sky) 0%, #87ceeb 100%);
      padding: 140px 0 0;
      position: relative;
      overflow: hidden;
    }
    
    /* Sun Animation */
    .sun-decoration {
      position: absolute;
      top: 80px;
      right: 10%;
      width: 150px;
      height: 150px;
      background: var(--sun);
      border-radius: 50%;
      box-shadow: 0 0 80px var(--sun), 0 0 120px rgba(255, 183, 3, .5);
      animation: sunPulse 4s ease-in-out infinite;
    }
    
    @keyframes sunPulse {
      0%, 100% { transform: scale(1); box-shadow: 0 0 80px var(--sun); }
      50% { transform: scale(1.05); box-shadow: 0 0 120px var(--sun); }
    }
    
    /* Cloud Decorations */
    .cloud {
      position: absolute;
      background: rgba(255,255,255,.9);
      border-radius: 100px;
      animation: cloudFloat 20s linear infinite;
    }
    
    .cloud::before, .cloud::after {
      content: '';
      position: absolute;
      background: inherit;
      border-radius: 50%;
    }
    
    .cloud-1 { width: 150px; height: 50px; top: 25%; left: -150px; animation-delay: 0s; }
    .cloud-1::before { width: 60px; height: 60px; top: -30px; left: 20px; }
    .cloud-1::after { width: 80px; height: 80px; top: -40px; left: 60px; }
    
    .cloud-2 { width: 120px; height: 40px; top: 15%; left: -120px; animation-delay: 8s; }
    .cloud-2::before { width: 50px; height: 50px; top: -25px; left: 15px; }
    .cloud-2::after { width: 65px; height: 65px; top: -30px; left: 50px; }
    
    @keyframes cloudFloat {
      0% { left: -200px; }
      100% { left: 110%; }
    }
    
    .hero-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px;
      align-items: center;
      position: relative;
      z-index: 5;
    }
    
    .hero-content { color: white; }
    
    .summer-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--sun);
      color: var(--dark);
      padding: 12px 28px;
      border-radius: 50px;
      font-weight: 700;
      font-size: 0.9rem;
      margin-bottom: 25px;
      animation: wiggle 3s ease-in-out infinite;
    }
    
    @keyframes wiggle {
      0%, 100% { transform: rotate(-2deg); }
      50% { transform: rotate(2deg); }
    }
    
    .hero-title {
      font-size: clamp(2.8rem, 5.5vw, 4.5rem);
      line-height: 1.1;
      margin-bottom: 20px;
      color: white;
      text-shadow: 3px 3px 0 rgba(0,0,0,.1);
    }
    
    .hero-subtitle {
      font-size: 1.2rem;
      opacity: 0.95;
      margin-bottom: 35px;
      max-width: 500px;
    }
    
    .hero-buttons { display: flex; gap: 18px; flex-wrap: wrap; }
    
    .btn-fun {
      padding: 16px 36px;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.4s;
      border: none;
    }
    
    .btn-sun {
      background: var(--sun);
      color: var(--dark);
      box-shadow: 0 8px 25px rgba(255, 183, 3, .4);
    }
    
    .btn-sun:hover { transform: translateY(-5px) rotate(-2deg); box-shadow: 0 15px 40px rgba(255, 183, 3, .5); color: var(--dark); }
    
    .btn-white {
      background: white;
      color: var(--sky);
    }
    
    .btn-white:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(255,255,255,.4); color: var(--sky); }
    
    .hero-visual {
      position: relative;
      display: flex;
      justify-content: center;
    }
    
    .hero-image {
      width: 100%;
      max-width: 450px;
      border-radius: 30px;
      box-shadow: 0 30px 60px rgba(0,0,0,.2);
      transform: rotate(3deg);
      transition: transform 0.5s;
      border: 8px solid white;
    }
    
    .hero-image:hover { transform: rotate(-1deg) scale(1.02); }
    
    /* Floating Cards around hero image */
    .float-card {
      position: absolute;
      background: white;
      border-radius: 20px;
      padding: 15px 20px;
      box-shadow: 0 15px 40px rgba(0,0,0,.15);
      display: flex;
      align-items: center;
      gap: 12px;
      animation: floatBounce 5s ease-in-out infinite;
    }
    
    .float-card-1 { top: 10%; left: -10%; animation-delay: 0s; }
    .float-card-2 { bottom: 20%; right: -5%; animation-delay: 2s; }
    .float-card-3 { top: 50%; left: -15%; animation-delay: 1s; }
    
    .float-card-icon {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      color: white;
    }
    
    .float-card-icon.blue { background: var(--sky); }
    .float-card-icon.green { background: var(--grass); }
    .float-card-icon.coral { background: var(--coral); }
    
    .float-card-text { font-weight: 700; font-size: 0.9rem; color: var(--dark); }
    
    /* Wave Divider */
    .wave-divider {
      height: 100px;
      position: relative;
      background: var(--sky);
      margin-top: -1px;
    }
    
    .wave-divider svg { position: absolute; bottom: 0; width: 100%; height: 100px; }
    
    /* Stats Section */
    .stats-section {
      background: var(--sand);
      padding: 60px 0;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }
    
    .stat-card {
      background: white;
      border-radius: 25px;
      padding: 35px 25px;
      text-align: center;
      transition: all 0.4s;
      border: 4px solid transparent;
    }
    
    .stat-card:nth-child(1) { border-color: var(--coral); }
    .stat-card:nth-child(2) { border-color: var(--sun); }
    .stat-card:nth-child(3) { border-color: var(--grass); }
    .stat-card:nth-child(4) { border-color: var(--sky); }
    
    .stat-card:hover { transform: translateY(-10px) rotate(-2deg); box-shadow: 0 20px 50px rgba(0,0,0,.1); }
    
    .stat-number {
      font-family: 'Baloo 2', cursive;
      font-size: 3.5rem;
      font-weight: 800;
      line-height: 1;
      margin-bottom: 10px;
    }
    
    .stat-card:nth-child(1) .stat-number { color: var(--coral); }
    .stat-card:nth-child(2) .stat-number { color: var(--sun); }
    .stat-card:nth-child(3) .stat-number { color: var(--grass); }
    .stat-card:nth-child(4) .stat-number { color: var(--sky); }
    
    .stat-label { font-size: 1rem; color: var(--text-muted); font-weight: 600; }
    
    /* Section Styles */
    .section { padding: 100px 0; }
    .section-white { background: white; }
    .section-sand { background: var(--sand); }
    
    .section-header { text-align: center; margin-bottom: 60px; }
    
    .section-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(135deg, var(--sky), var(--grass));
      color: white;
      padding: 10px 28px;
      border-radius: 50px;
      font-weight: 700;
      font-size: 0.85rem;
      margin-bottom: 18px;
    }
    
    .section-title { font-size: 2.8rem; margin-bottom: 15px; }
    .section-subtitle { font-size: 1.1rem; color: var(--text-muted); max-width: 600px; margin: 0 auto; }
    
    /* About Section */
    .about-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
    }
    
    .about-image {
      position: relative;
      border-radius: 30px;
      overflow: hidden;
      transform: rotate(-3deg);
      transition: transform 0.5s;
      border: 8px solid white;
      box-shadow: 0 25px 60px rgba(0,0,0,.15);
    }
    
    .about-image:hover { transform: rotate(0deg); }
    
    .about-image img { width: 100%; height: 400px; object-fit: cover; }
    
    .about-content h2 { font-size: 2.2rem; margin-bottom: 20px; }
    .about-content p { color: var(--text-muted); margin-bottom: 25px; line-height: 1.9; }
    
    /* Programs Grid */
    .programs-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .program-card {
      background: white;
      border-radius: 30px;
      padding: 35px;
      text-align: center;
      transition: all 0.4s;
      position: relative;
      overflow: hidden;
    }
    
    .program-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, var(--coral), var(--sun), var(--grass), var(--sky));
    }
    
    .program-card:hover { transform: translateY(-12px) rotate(-1deg); box-shadow: 0 25px 60px rgba(0,0,0,.12); }
    
    .program-icon {
      width: 85px;
      height: 85px;
      background: linear-gradient(135deg, var(--sky), var(--grass));
      border-radius: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 38px;
      color: white;
      margin: 0 auto 25px;
      transform: rotate(-8deg);
      transition: transform 0.4s;
    }
    
    .program-card:hover .program-icon { transform: rotate(0deg) scale(1.1); }
    
    .program-card h4 { font-size: 1.4rem; margin-bottom: 12px; }
    .program-card p { color: var(--text-muted); font-size: 0.95rem; margin: 0; line-height: 1.7; }
    
    /* Principal Section */
    .principal-section {
      background: linear-gradient(135deg, var(--purple), var(--pink));
      color: white;
      position: relative;
      overflow: hidden;
    }
    
    .principal-grid {
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 50px;
      align-items: center;
      position: relative;
      z-index: 2;
    }
    
    .principal-image {
      border-radius: 25px;
      overflow: hidden;
      border: 6px solid white;
      box-shadow: 0 20px 50px rgba(0,0,0,.2);
      transform: rotate(-5deg);
    }
    
    .principal-image img { width: 100%; height: 380px; object-fit: cover; }
    
    .principal-content h3 { color: var(--sun); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; }
    
    .principal-content blockquote {
      font-size: 1.35rem;
      font-style: italic;
      opacity: 0.95;
      margin: 0 0 30px;
      line-height: 1.8;
    }
    
    .principal-info h4 { font-size: 1.3rem; color: white; margin-bottom: 5px; }
    .principal-info p { color: var(--sun); margin: 0; font-weight: 700; }
    
    /* Facilities Grid */
    .facilities-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 25px;
    }
    
    .facility-card {
      background: white;
      border-radius: 25px;
      padding: 30px 22px;
      text-align: center;
      transition: all 0.4s;
    }
    
    .facility-card:hover { transform: translateY(-10px) rotate(2deg); box-shadow: 0 20px 50px rgba(0,0,0,.1); }
    
    .facility-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--coral), var(--sun));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 30px;
      color: white;
    }
    
    .facility-card h4 { font-size: 1.1rem; margin-bottom: 10px; }
    .facility-card p { color: var(--text-muted); font-size: 0.88rem; margin: 0; }
    
    /* Gallery - Fun Grid */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 25px;
    }
    
    .gallery-item {
      border-radius: 20px;
      overflow: hidden;
      aspect-ratio: 1;
      border: 5px solid white;
      box-shadow: 0 10px 30px rgba(0,0,0,.1);
      transition: all 0.4s;
    }
    
    .gallery-item:nth-child(odd) { transform: rotate(-3deg); }
    .gallery-item:nth-child(even) { transform: rotate(3deg); }
    
    .gallery-item:hover { transform: rotate(0deg) scale(1.05); z-index: 5; }
    
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .gallery-item:hover img { transform: scale(1.1); }
    
    /* News Grid */
    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .news-card {
      background: white;
      border-radius: 25px;
      overflow: hidden;
      transition: all 0.4s;
    }
    
    .news-card:hover { transform: translateY(-10px) rotate(-1deg); box-shadow: 0 25px 60px rgba(0,0,0,.12); }
    
    .news-card-image { width: 100%; height: 180px; object-fit: cover; background: linear-gradient(135deg, var(--sky), var(--grass)); }
    .news-card-body { padding: 25px; }
    .news-card-date { color: var(--coral); font-size: 0.85rem; font-weight: 700; margin-bottom: 10px; }
    .news-card h4 { font-size: 1.15rem; margin-bottom: 10px; }
    .news-card p { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
    
    /* Testimonials */
    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .testimonial-card {
      background: white;
      border-radius: 25px;
      padding: 35px;
      position: relative;
      transition: all 0.3s;
    }
    
    .testimonial-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,.1); }
    
    .testimonial-card::before {
      content: '💬';
      position: absolute;
      top: 20px;
      right: 20px;
      font-size: 35px;
      opacity: 0.2;
    }
    
    .testimonial-stars { color: var(--sun); margin-bottom: 18px; font-size: 1rem; }
    .testimonial-content { color: var(--text-muted); margin-bottom: 25px; line-height: 1.8; min-height: 80px; }
    
    .testimonial-author { display: flex; align-items: center; gap: 15px; }
    
    .testimonial-avatar {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid var(--sun);
    }
    
    .testimonial-avatar-placeholder {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--sky), var(--grass));
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 22px;
      color: white;
    }
    
    .testimonial-name { font-weight: 700; font-size: 1rem; color: var(--sky); }
    .testimonial-role { font-size: 0.85rem; color: var(--text-muted); }
    
    /* Admission Form */
    .admission-section {
      background: linear-gradient(135deg, rgba(0, 180, 216, .1), rgba(6, 214, 160, .1));
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
      border-bottom: 2px dashed rgba(0,0,0,.1);
    }
    
    .step-number {
      width: 55px;
      height: 55px;
      background: linear-gradient(135deg, var(--sun), var(--coral));
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.3rem;
      flex-shrink: 0;
    }
    
    .admission-step h4 { font-size: 1.1rem; margin-bottom: 5px; }
    .admission-step p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
    
    .admission-form {
      background: white;
      border-radius: 30px;
      padding: 45px;
      border: 5px solid var(--sky);
      box-shadow: 0 20px 60px rgba(0,0,0,.08);
    }
    
    .admission-form h3 { text-align: center; margin-bottom: 30px; font-size: 1.6rem; color: var(--sky); }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-weight: 700; font-size: 0.9rem; margin-bottom: 10px; color: var(--sky); }
    
    .form-control, .form-select {
      width: 100%;
      padding: 15px 20px;
      background: var(--sand);
      border: 3px solid transparent;
      border-radius: 15px;
      font-family: inherit;
      font-size: 0.95rem;
      color: var(--text);
      transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
      outline: none;
      border-color: var(--sky);
      background: white;
    }
    
    .btn-submit {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, var(--sky), var(--grass));
      color: white;
      border: none;
      border-radius: 15px;
      font-weight: 800;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.4s;
      margin-top: 10px;
    }
    
    .btn-submit:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 15px 40px rgba(0, 180, 216, .4); }
    
    .alert-success {
      background: rgba(6, 214, 160, .15);
      border: 3px solid var(--grass);
      color: var(--grass);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 25px;
      text-align: center;
      font-weight: 600;
    }
    
    .alert-danger {
      background: rgba(255, 107, 107, .15);
      border: 3px solid var(--coral);
      color: var(--coral);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 25px;
    }
    
    /* Contact Section */
    .contact-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }
    
    .contact-card {
      background: white;
      border-radius: 25px;
      padding: 35px;
      text-align: center;
      transition: all 0.4s;
    }
    
    .contact-card:hover { transform: translateY(-8px) rotate(-2deg); box-shadow: 0 20px 50px rgba(0,0,0,.1); }
    
    .contact-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--purple), var(--pink));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 28px;
      color: white;
    }
    
    .contact-card h4 { font-size: 1.1rem; margin-bottom: 10px; }
    .contact-card p, .contact-card a { color: var(--text-muted); font-size: 0.9rem; margin: 0; text-decoration: none; display: block; }
    .contact-card a:hover { color: var(--sky); }
    
    /* Footer */
    .footer {
      background: linear-gradient(135deg, var(--dark), #1a1a2e);
      color: white;
      padding: 80px 0 30px;
    }
    
    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 50px;
      margin-bottom: 50px;
    }
    
    .footer-brand h3 { font-size: 1.5rem; margin-bottom: 18px; color: var(--sun); }
    .footer-brand p { opacity: 0.85; font-size: 0.95rem; margin-bottom: 25px; }
    
    .social-links { display: flex; gap: 15px; }
    
    .social-link {
      width: 48px;
      height: 48px;
      background: rgba(255,255,255,.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 20px;
      transition: all 0.4s;
    }
    
    .social-link:hover { background: linear-gradient(135deg, var(--sky), var(--grass)); transform: translateY(-5px) rotate(-10deg); }
    
    .footer-links h5 { font-size: 1rem; margin-bottom: 22px; color: var(--sun); }
    .footer-links ul { list-style: none; padding: 0; }
    .footer-links a { color: rgba(255,255,255,.8); text-decoration: none; display: block; padding: 8px 0; font-size: 0.9rem; transition: all 0.3s; }
    .footer-links a:hover { color: var(--sun); padding-left: 8px; }
    
    .footer-bottom {
      text-align: center;
      padding-top: 30px;
      border-top: 1px solid rgba(255,255,255,.1);
      opacity: 0.8;
      font-size: 0.85rem;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
      .hero-grid, .about-grid, .admission-grid, .principal-grid { grid-template-columns: 1fr; }
      .hero-visual { order: -1; }
      .principal-image { max-width: 350px; margin: 0 auto; }
      .footer-grid { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 992px) {
      .nav-links {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        height: 100vh;
        background: white;
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
      .programs-grid, .news-grid, .testimonials-grid { grid-template-columns: 1fr; }
      .gallery-grid { grid-template-columns: repeat(2, 1fr); }
      .float-card { display: none; }
    }
    
    @media (max-width: 768px) {
      .section { padding: 70px 0; }
      .stats-grid, .contact-grid, .facilities-grid, .footer-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
      .admission-form { padding: 30px 20px; }
      .gallery-grid { grid-template-columns: 1fr; }
      .gallery-item { transform: none !important; }
      .sun-decoration { display: none; }
      .cloud { display: none; }
    }
  </style>
</head>
<body>
  <div class="floating-decorations">
    <div class="float-item">🎈</div>
    <div class="float-item">⭐</div>
    <div class="float-item">🌟</div>
    <div class="float-item">🎨</div>
    <div class="float-item">📚</div>
  </div>
  
  <div class="content-wrapper">
    <!-- Navbar -->
    <nav class="navbar-rainbow" id="navbar">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center">
          <a href="<?= e(base_url('/')) ?>" class="nav-brand">
            <div class="brand-logo">
              <?php if (!empty($schoolLogo)): ?>
                <img src="<?= e($schoolLogo) ?>" alt="<?= e($schoolName) ?>">
              <?php else: ?>
                <i class="bi bi-emoji-smile"></i>
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
            <li><a class="nav-link nav-cta" href="#admission">🎉 Join Us!</a></li>
          </ul>
          
          <button class="mobile-toggle" onclick="document.getElementById('navLinks').classList.toggle('active')">
            <i class="bi bi-list"></i>
          </button>
        </div>
      </div>
    </nav>
    
    <!-- Hero Banner Slider (if enabled) -->
    <?php include __DIR__ . '/partials/hero-slider.php'; ?>
    
    <!-- Hero Section - Summer Vacation Style -->
    <section class="hero-summer">
      <div class="sun-decoration"></div>
      <div class="cloud cloud-1"></div>
      <div class="cloud cloud-2"></div>
      
      <div class="container">
        <div class="hero-grid">
          <div class="hero-content">
            <div class="summer-badge">
              <span>☀️</span>
              Where Learning Meets Fun!
            </div>
            
            <h1 class="hero-title"><?= e($heroTitle) ?></h1>
            <p class="hero-subtitle"><?= e($heroSubtitle) ?></p>
            
            <div class="hero-buttons">
              <a href="#admission" class="btn-fun btn-sun">
                <span>🚀</span>
                Start Adventure
              </a>
              <a href="#about" class="btn-fun btn-white">
                <i class="bi bi-play-circle"></i>
                Explore
              </a>
            </div>
          </div>
          
          <div class="hero-visual">
            <img src="<?= e($heroImage) ?>" alt="Happy Students" class="hero-image">
            
            <div class="float-card float-card-1">
              <div class="float-card-icon blue"><i class="bi bi-trophy"></i></div>
              <div class="float-card-text"><?= e($statResults) ?> Results</div>
            </div>
            
            <div class="float-card float-card-2">
              <div class="float-card-icon green"><i class="bi bi-people"></i></div>
              <div class="float-card-text"><?= e($statStudents) ?> Students</div>
            </div>
            
            <div class="float-card float-card-3">
              <div class="float-card-icon coral"><i class="bi bi-star"></i></div>
              <div class="float-card-text"><?= e($statYears) ?> Years</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Wave Divider -->
    <div class="wave-divider">
      <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path fill="<?= e($secondaryColor ?? '#fff3e0') ?>" fill-opacity="1" d="M0,40L48,45C96,50,192,60,288,55C384,50,480,30,576,25C672,20,768,30,864,40C960,50,1056,60,1152,60C1248,60,1344,50,1392,45L1440,40L1440,100L1392,100C1344,100,1248,100,1152,100C1056,100,960,100,864,100C768,100,672,100,576,100C480,100,384,100,288,100C192,100,96,100,48,100L0,100Z"></path>
      </svg>
    </div>
    
    <!-- Stats Section -->
    <section class="stats-section">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-number"><?= e($statYears) ?></div>
            <div class="stat-label">Years of Fun Learning</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= e($statStudents) ?></div>
            <div class="stat-label">Happy Students</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= e($statTeachers) ?></div>
            <div class="stat-label">Amazing Teachers</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= e($statResults) ?></div>
            <div class="stat-label">Excellent Results</div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- About Section -->
    <section class="section section-white" id="about">
      <div class="container">
        <div class="about-grid">
          <div class="about-image">
            <img src="<?= e($aboutImage) ?>" alt="About Us">
          </div>
          <div class="about-content">
            <div class="section-badge">🌈 About Us</div>
            <h2><?= e($aboutTitle) ?></h2>
            <p><?= e($aboutText) ?></p>
            <a href="#programs" class="btn-fun btn-sun">
              <span>🎓</span>
              Explore Programs
            </a>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Programs Section -->
    <section class="section section-sand" id="programs">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📚 Our Programs</div>
          <h2 class="section-title">Learning Made Fun!</h2>
          <p class="section-subtitle">Age-appropriate programs designed to spark curiosity and creativity.</p>
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
            <h3>✨ Principal's Message</h3>
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
    <section class="section section-sand" id="facilities">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">🏫 Our Facilities</div>
          <h2 class="section-title">Everything Kids Love!</h2>
          <p class="section-subtitle">Modern facilities designed for safe and fun learning.</p>
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
    <section class="section section-white" id="gallery">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📸 Gallery</div>
          <h2 class="section-title">Happy Moments</h2>
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
    <section class="section section-sand">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📰 Latest News</div>
          <h2 class="section-title">What's Happening</h2>
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
                <i class="bi bi-newspaper" style="font-size: 48px; color: white; opacity: 0.5;"></i>
              </div>
            <?php endif; ?>
            <div class="news-card-body">
              <div class="news-card-date">📅 <?= e(!empty($news['date']) ? date('M d, Y', strtotime($news['date'])) : '') ?></div>
              <h4><?= e($news['title']) ?></h4>
              <p><?= e(substr(strip_tags($news['content']), 0, 150)) ?>...</p>
              <span class="news-read-more" style="color: var(--sky); font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px; margin-top: 10px;">
                Read More 🚀
              </span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    
    <!-- News Modal -->
    <div class="news-modal-overlay" id="newsModalOverlay" onclick="closeNewsModal()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,.85); z-index: 9999; padding: 20px; overflow-y: auto;">
      <div class="news-modal" onclick="event.stopPropagation()" style="max-width: 800px; margin: 40px auto; background: white; border-radius: 30px; overflow: hidden; animation: modalFadeIn 0.3s ease; border: 5px solid var(--sky);">
        <button onclick="closeNewsModal()" style="position: absolute; top: 15px; right: 15px; background: var(--coral); color: white; border: none; width: 45px; height: 45px; border-radius: 50%; font-size: 1.3rem; cursor: pointer; z-index: 10;">
          <i class="bi bi-x-lg"></i>
        </button>
        <div id="newsModalImage" style="width: 100%; height: 280px; object-fit: cover; background: linear-gradient(135deg, var(--sky), var(--grass));"></div>
        <div style="padding: 35px;">
          <div id="newsModalDate" style="color: var(--coral); font-weight: 700; font-size: 0.95rem; margin-bottom: 12px;"></div>
          <h3 id="newsModalTitle" style="font-size: 1.8rem; margin-bottom: 20px; color: var(--dark);"></h3>
          <div id="newsModalContent" style="color: var(--text-muted); line-height: 1.9; font-size: 1rem; white-space: pre-line;"></div>
        </div>
      </div>
    </div>
    
    <style>
      @keyframes modalFadeIn { from { opacity: 0; transform: translateY(30px) rotate(-2deg); } to { opacity: 1; transform: translateY(0) rotate(0); } }
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
          imgDiv.innerHTML = '<div style="width:100%;height:280px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#00b4d8,#06d6a0);"><span style="font-size:80px;">📰</span></div>';
        }
        
        document.getElementById('newsModalDate').innerHTML = '📅 ' + (news.date ? new Date(news.date).toLocaleDateString('en-US', {year:'numeric',month:'long',day:'numeric'}) : '');
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
    <section class="section section-white">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">💬 Testimonials</div>
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
            <div class="section-badge">🎉 Join Us!</div>
            <h2>Start Your Child's Fun Journey</h2>
            <p>Give your little one the gift of joyful learning in a nurturing environment.</p>
            
            <ul class="admission-steps">
              <li class="admission-step">
                <div class="step-number">1</div>
                <div>
                  <h4>📝 Fill Form</h4>
                  <p>Complete the simple admission form</p>
                </div>
              </li>
              <li class="admission-step">
                <div class="step-number">2</div>
                <div>
                  <h4>📄 Documents</h4>
                  <p>Submit required documents</p>
                </div>
              </li>
              <li class="admission-step">
                <div class="step-number">3</div>
                <div>
                  <h4>👋 Meet Us</h4>
                  <p>Visit campus and meet our team</p>
                </div>
              </li>
              <li class="admission-step">
                <div class="step-number">4</div>
                <div>
                  <h4>🎊 Welcome!</h4>
                  <p>Complete enrollment and celebrate</p>
                </div>
              </li>
            </ul>
          </div>
          
          <div class="admission-form">
            <h3>🌟 Admission Form</h3>
            
            <?php if ($success): ?>
              <div class="alert-success">
                🎉 Yay! Your application has been submitted successfully!
              </div>
            <?php elseif ($error): ?>
              <div class="alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            
            <form method="post">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Child's Name *</label>
                  <input type="text" class="form-control" name="student_name" placeholder="Enter name" required>
                </div>
                <div class="form-group">
                  <label class="form-label">Parent's Name *</label>
                  <input type="text" class="form-control" name="parent_name" placeholder="Enter name" required>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Email *</label>
                  <input type="email" class="form-control" name="email" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                  <label class="form-label">Phone *</label>
                  <input type="tel" class="form-control" name="phone" placeholder="+91 XXXXX XXXXX" required>
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
                    <option value="Male">Boy</option>
                    <option value="Female">Girl</option>
                  </select>
                </div>
              </div>
              
              <div class="form-row">
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
                <div class="form-group">
                  <label class="form-label">Previous School</label>
                  <input type="text" class="form-control" name="previous_school" placeholder="School name">
                </div>
              </div>
              
              <div class="form-group">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="2" placeholder="Your address"></textarea>
              </div>
              
              <button type="submit" class="btn-submit">
                🚀 Submit Application
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Contact Section -->
    <section class="section section-sand" id="contact">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📞 Contact Us</div>
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
            <a href="mailto:<?= e($admissionEmail) ?>"><?= e($admissionEmail) ?></a>
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
            <p><?= e($schoolTagline) ?> - Making learning fun since <?= e(getSetting('school_established', '1998')) ?>! 🌟</p>
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
          <p>&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved. 💖</p>
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
