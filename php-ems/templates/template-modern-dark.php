<?php
/**
 * Admission Focus Template - PREMIUM LIGHT THEME
 * Ultra-modern, animated, super responsive with motion effects
 * Features: Scroll animations, 3D effects, parallax, magnetic buttons
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
  <meta name="description" content="<?= e($schoolName) ?> - <?= e($heroSubtitle) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0052cc;
      --primary-rgb: 0, 82, 204;
      --primary-light: #e6f0ff;
      --primary-dark: #003d99;
      --secondary: #ff6b35;
      --secondary-rgb: 255, 107, 53;
      --accent: #00c853;
      --accent-rgb: 0, 200, 83;
      --gold: #ffc107;
      --light: #f8fafc;
      --lighter: #ffffff;
      --dark: #0f172a;
      --text: #334155;
      --text-muted: #64748b;
      --text-light: #94a3b8;
      --border: #e2e8f0;
      --border-light: #f1f5f9;
      --shadow-sm: 0 2px 8px rgba(0,0,0,.04);
      --shadow: 0 8px 30px rgba(0,0,0,.08);
      --shadow-lg: 0 25px 60px rgba(0,0,0,.12);
      --shadow-xl: 0 40px 80px rgba(0,0,0,.15);
      --shadow-primary: 0 15px 40px rgba(0, 82, 204, .25);
      --shadow-secondary: 0 15px 40px rgba(255, 107, 53, .25);
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; scroll-padding-top: 80px; overflow-x: hidden; }
    
    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background: var(--lighter);
      color: var(--text);
      line-height: 1.7;
      overflow-x: hidden;
      max-width: 100vw;
    }
    
    h1, h2, h3, .display-font { font-family: 'Playfair Display', serif; color: var(--dark); }
    
    /* ======================
       ANIMATED BACKGROUND
    ====================== */
    .animated-bg {
      position: fixed;
      inset: 0;
      z-index: 0;
      overflow: hidden;
      background: linear-gradient(135deg, var(--lighter) 0%, var(--light) 50%, #fff5f0 100%);
    }
    
    .animated-bg::before {
      content: '';
      position: absolute;
      width: 800px;
      height: 800px;
      background: radial-gradient(circle, rgba(var(--primary-rgb), .06) 0%, transparent 70%);
      border-radius: 50%;
      top: -20%;
      right: -10%;
      animation: float-slow 20s ease-in-out infinite;
    }
    
    .animated-bg::after {
      content: '';
      position: absolute;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(var(--secondary-rgb), .05) 0%, transparent 70%);
      border-radius: 50%;
      bottom: -15%;
      left: -5%;
      animation: float-slow 25s ease-in-out infinite reverse;
    }
    
    @keyframes float-slow {
      0%, 100% { transform: translate(0, 0) scale(1); }
      25% { transform: translate(30px, -40px) scale(1.05); }
      50% { transform: translate(-20px, 30px) scale(0.95); }
      75% { transform: translate(40px, 20px) scale(1.02); }
    }
    
    .content-wrapper { position: relative; z-index: 1; }
    
    /* ======================
       SCROLL ANIMATIONS
    ====================== */
    [data-scroll] {
      opacity: 0;
      transform: translateY(60px);
      transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    [data-scroll].visible {
      opacity: 1;
      transform: translateY(0);
    }
    
    [data-scroll="fade-left"] { transform: translateX(-80px); }
    [data-scroll="fade-left"].visible { transform: translateX(0); }
    
    [data-scroll="fade-right"] { transform: translateX(80px); }
    [data-scroll="fade-right"].visible { transform: translateX(0); }
    
    [data-scroll="zoom-in"] { transform: scale(0.85); }
    [data-scroll="zoom-in"].visible { transform: scale(1); }
    
    [data-scroll="rotate-in"] { transform: perspective(800px) rotateY(-15deg) translateY(40px); }
    [data-scroll="rotate-in"].visible { transform: perspective(800px) rotateY(0) translateY(0); }
    
    .delay-1 { transition-delay: 0.1s !important; }
    .delay-2 { transition-delay: 0.2s !important; }
    .delay-3 { transition-delay: 0.3s !important; }
    .delay-4 { transition-delay: 0.4s !important; }
    .delay-5 { transition-delay: 0.5s !important; }
    
    /* ======================
       PREMIUM NAVBAR
    ====================== */
    .navbar-premium {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(226, 232, 240, 0.6);
      padding: 12px 0;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .navbar-premium.scrolled {
      background: rgba(255, 255, 255, 0.98);
      box-shadow: 0 4px 40px rgba(0,0,0,.08);
      padding: 8px 0;
    }
    
    .nav-brand {
      display: flex;
      align-items: center;
      gap: 14px;
      text-decoration: none;
    }
    
    .brand-logo {
      width: 54px;
      height: 54px;
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: white;
      box-shadow: var(--shadow-primary);
      overflow: hidden;
      transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .brand-logo:hover { transform: scale(1.08) rotate(-3deg); }
    .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
    
    .brand-name { 
      font-family: 'Playfair Display', serif; 
      font-size: 1.35rem; 
      font-weight: 700; 
      color: var(--dark);
      letter-spacing: -0.5px;
    }
    .brand-tagline { 
      font-size: 0.7rem; 
      color: var(--primary); 
      letter-spacing: 2.5px; 
      text-transform: uppercase; 
      font-weight: 600;
    }
    
    .nav-links { display: flex; align-items: center; gap: 6px; list-style: none; margin: 0; padding: 0; }
    
    .nav-link {
      color: var(--text) !important;
      font-weight: 500;
      padding: 12px 20px !important;
      border-radius: 12px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      font-size: 0.95rem;
      position: relative;
    }
    
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: 6px;
      left: 50%;
      width: 0;
      height: 2px;
      background: var(--primary);
      transition: all 0.3s;
      transform: translateX(-50%);
      border-radius: 2px;
    }
    
    .nav-link:hover { color: var(--primary) !important; background: var(--primary-light); }
    .nav-link:hover::after { width: 20px; }
    
    .nav-cta {
      background: linear-gradient(145deg, var(--secondary), #ff8f65) !important;
      color: white !important;
      font-weight: 600;
      padding: 14px 30px !important;
      border-radius: 100px;
      box-shadow: var(--shadow-secondary);
      position: relative;
      overflow: hidden;
    }
    
    .nav-cta::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(145deg, transparent, rgba(255,255,255,.2));
      opacity: 0;
      transition: opacity 0.3s;
    }
    
    .nav-cta:hover { 
      transform: translateY(-3px) scale(1.02); 
      box-shadow: 0 20px 50px rgba(255, 107, 53, .35);
      color: white !important;
    }
    
    .nav-cta:hover::before { opacity: 1; }
    .nav-cta::after { display: none; }
    
    .mobile-toggle {
      display: none;
      align-items: center;
      justify-content: center;
      background: var(--primary);
      border: none;
      color: white;
      width: 44px;
      height: 44px;
      min-width: 44px;
      padding: 0;
      border-radius: 12px;
      font-size: 1.4rem;
      cursor: pointer;
      transition: all 0.3s;
      z-index: 1001;
      flex-shrink: 0;
      box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);
    }
    
    .mobile-toggle:hover { background: var(--primary-dark); transform: scale(1.05); }
    .mobile-toggle.active { background: var(--secondary); }
    
    /* ======================
       HERO SECTION - PREMIUM WITH PARALLAX & VIDEO
    ====================== */
    .hero-premium {
      min-height: 100vh;
      display: flex;
      align-items: center;
      padding: 140px 0 100px;
      position: relative;
      overflow: hidden;
    }
    
    /* When slider is present, adjust hero padding */
    .hero-slider-wrapper + .hero-premium {
      padding-top: 100px;
      min-height: auto;
    }

    /* Video Background Container */
    .hero-video-bg {
      position: absolute;
      inset: 0;
      z-index: 0;
      overflow: hidden;
    }
    
    .hero-video-bg video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transform: scale(1.1);
      will-change: transform;
    }
    
    .hero-video-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.92) 0%, 
        rgba(248, 250, 252, 0.88) 30%,
        rgba(230, 240, 255, 0.85) 70%,
        rgba(255, 245, 240, 0.9) 100%);
      z-index: 1;
    }
    
    /* Parallax Layers */
    .parallax-container {
      position: absolute;
      inset: 0;
      z-index: 1;
      overflow: hidden;
      pointer-events: none;
    }
    
    .parallax-layer {
      position: absolute;
      inset: -20%;
      will-change: transform;
      transition: transform 0.1s ease-out;
    }
    
    .parallax-layer-1 {
      z-index: 1;
    }
    
    .parallax-layer-2 {
      z-index: 2;
    }
    
    .parallax-layer-3 {
      z-index: 3;
    }
    
    /* Parallax Shapes */
    .parallax-shape {
      position: absolute;
      border-radius: 50%;
      opacity: 0.5;
      filter: blur(1px);
    }
    
    .shape-circle-1 {
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(var(--primary-rgb), 0.12) 0%, transparent 70%);
      top: 5%;
      right: -5%;
    }
    
    .shape-circle-2 {
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(var(--secondary-rgb), 0.1) 0%, transparent 70%);
      bottom: 10%;
      left: -3%;
    }
    
    .shape-circle-3 {
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(var(--accent-rgb), 0.08) 0%, transparent 70%);
      top: 40%;
      left: 30%;
    }
    
    .shape-square {
      width: 150px;
      height: 150px;
      background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.06), transparent);
      border-radius: 30px;
      transform: rotate(45deg);
      top: 15%;
      left: 10%;
    }
    
    .shape-diamond {
      width: 120px;
      height: 120px;
      background: linear-gradient(180deg, rgba(var(--gold), 0.15), transparent);
      transform: rotate(45deg);
      bottom: 20%;
      right: 15%;
      border-radius: 15px;
    }
    
    /* Floating Icons for Parallax */
    .parallax-icon {
      position: absolute;
      font-size: 2.5rem;
      opacity: 0.08;
      color: var(--primary);
    }
    
    .icon-book { top: 25%; left: 8%; animation: float-rotate 12s ease-in-out infinite; }
    .icon-graduation { top: 15%; right: 20%; animation: float-rotate 15s ease-in-out infinite 2s; }
    .icon-trophy { bottom: 30%; left: 15%; animation: float-rotate 10s ease-in-out infinite 1s; }
    .icon-star { top: 50%; right: 8%; animation: float-rotate 14s ease-in-out infinite 3s; }
    .icon-pencil { bottom: 15%; right: 25%; animation: float-rotate 11s ease-in-out infinite 4s; }
    
    @keyframes float-rotate {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      25% { transform: translateY(-20px) rotate(5deg); }
      50% { transform: translateY(10px) rotate(-3deg); }
      75% { transform: translateY(-15px) rotate(3deg); }
    }
    
    .hero-particles {
      position: absolute;
      inset: 0;
      z-index: 2;
      pointer-events: none;
    }
    
    .particle {
      position: absolute;
      width: 6px;
      height: 6px;
      background: var(--primary);
      border-radius: 50%;
      opacity: 0.15;
    }
    
    .particle:nth-child(1) { top: 20%; left: 10%; animation: particle-float 8s ease-in-out infinite; }
    .particle:nth-child(2) { top: 40%; left: 85%; animation: particle-float 10s ease-in-out infinite 1s; background: var(--secondary); }
    .particle:nth-child(3) { top: 70%; left: 20%; animation: particle-float 9s ease-in-out infinite 2s; }
    .particle:nth-child(4) { top: 30%; left: 70%; animation: particle-float 7s ease-in-out infinite 0.5s; background: var(--gold); }
    .particle:nth-child(5) { top: 80%; left: 75%; animation: particle-float 11s ease-in-out infinite 1.5s; }
    .particle:nth-child(6) { top: 15%; left: 45%; animation: particle-float 8s ease-in-out infinite 3s; background: var(--accent); }
    .particle:nth-child(7) { top: 55%; left: 5%; animation: particle-float 9s ease-in-out infinite 2.5s; background: var(--secondary); }
    .particle:nth-child(8) { top: 10%; left: 60%; animation: particle-float 12s ease-in-out infinite 1.5s; }
    
    @keyframes particle-float {
      0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.15; }
      25% { transform: translate(30px, -30px) scale(1.5); opacity: 0.3; }
      50% { transform: translate(-20px, 20px) scale(0.8); opacity: 0.1; }
      75% { transform: translate(20px, 10px) scale(1.2); opacity: 0.25; }
    }
    
    /* Scroll Indicator */
    .scroll-indicator {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 10;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      animation: fade-in 1s 1.5s forwards;
      opacity: 0;
    }
    
    .scroll-indicator span {
      font-size: 0.75rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 3px;
      font-weight: 600;
    }
    
    .scroll-mouse {
      width: 28px;
      height: 45px;
      border: 2px solid var(--text-light);
      border-radius: 20px;
      position: relative;
    }
    
    .scroll-mouse::before {
      content: '';
      position: absolute;
      top: 8px;
      left: 50%;
      transform: translateX(-50%);
      width: 4px;
      height: 10px;
      background: var(--primary);
      border-radius: 2px;
      animation: scroll-anim 2s ease-in-out infinite;
    }
    
    @keyframes scroll-anim {
      0%, 100% { top: 8px; opacity: 1; }
      50% { top: 20px; opacity: 0.3; }
    }
    
    .hero-grid {
      display: grid;
      grid-template-columns: 1.3fr 1fr;
      gap: 80px;
      align-items: center;
      position: relative;
      z-index: 5;
    }
    
    .hero-content { position: relative; }
    
    /* Animated Admission Badge */
    .admission-badge-animated {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 14px 32px;
      border-radius: 100px;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 32px;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-primary);
      animation: badge-glow 3s ease-in-out infinite;
    }
    
    .admission-badge-animated::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.3), transparent);
      animation: shimmer 3s infinite;
    }
    
    @keyframes shimmer {
      0% { left: -100%; }
      100% { left: 100%; }
    }
    
    @keyframes badge-glow {
      0%, 100% { box-shadow: 0 10px 35px rgba(0, 82, 204, .25); }
      50% { box-shadow: 0 15px 45px rgba(0, 82, 204, .4); }
    }
    
    .badge-dot {
      width: 10px;
      height: 10px;
      background: var(--accent);
      border-radius: 50%;
      animation: dot-pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes dot-pulse {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.5); opacity: 0.5; }
    }
    
    .hero-title {
      font-size: clamp(2.8rem, 5.5vw, 4.5rem);
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: 28px;
      color: var(--dark);
      letter-spacing: -2px;
    }
    
    .hero-title .line { display: block; overflow: hidden; }
    .hero-title .line span { 
      display: inline-block; 
      animation: slide-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      opacity: 0;
      transform: translateY(100%);
    }
    .hero-title .line:nth-child(2) span { animation-delay: 0.15s; }
    .hero-title .line:nth-child(3) span { animation-delay: 0.3s; }
    
    @keyframes slide-up {
      to { transform: translateY(0); opacity: 1; }
    }
    
    .hero-title .gradient-text {
      background: linear-gradient(145deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero-subtitle {
      font-size: 1.2rem;
      color: var(--text-muted);
      margin-bottom: 40px;
      max-width: 560px;
      line-height: 1.9;
      animation: fade-in 1s 0.5s forwards;
      opacity: 0;
    }
    
    @keyframes fade-in {
      to { opacity: 1; }
    }
    
    .hero-buttons { 
      display: flex; 
      gap: 20px; 
      flex-wrap: wrap;
      animation: fade-in 1s 0.7s forwards;
      opacity: 0;
    }
    
    /* Magnetic Buttons */
    .btn-magnetic {
      padding: 18px 40px;
      border-radius: 100px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      border: none;
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }
    
    .btn-magnetic::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255,255,255,.2) 0%, transparent 60%);
      opacity: 0;
      transition: opacity 0.3s;
    }
    
    .btn-magnetic:hover::before { opacity: 1; }
    
    .btn-primary-magnetic {
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      color: white;
      box-shadow: var(--shadow-primary);
    }
    
    .btn-primary-magnetic:hover {
      transform: translateY(-5px) scale(1.03);
      box-shadow: 0 25px 60px rgba(0, 82, 204, .35);
      color: white;
    }
    
    .btn-secondary-magnetic {
      background: white;
      border: 2px solid var(--border);
      color: var(--dark);
      box-shadow: var(--shadow);
    }
    
    .btn-secondary-magnetic:hover {
      border-color: var(--primary);
      color: var(--primary);
      box-shadow: var(--shadow-lg);
      transform: translateY(-5px);
    }
    
    /* Hero Stats Mini */
    .hero-stats {
      display: flex;
      gap: 40px;
      margin-top: 50px;
      animation: fade-in 1s 0.9s forwards;
      opacity: 0;
    }
    
    .hero-stat {
      text-align: left;
    }
    
    .hero-stat-number {
      font-family: 'Playfair Display', serif;
      font-size: 2.2rem;
      font-weight: 800;
      color: var(--primary);
      line-height: 1;
    }
    
    .hero-stat-label {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-top: 4px;
    }
    
    /* ======================
       FLOATING ADMISSION CARD
    ====================== */
    .admission-card-3d {
      background: white;
      border-radius: 28px;
      padding: 45px;
      box-shadow: var(--shadow-xl);
      position: relative;
      overflow: hidden;
      animation: card-appear 1s 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      opacity: 0;
      transform: perspective(1000px) rotateY(-10deg) translateX(50px);
    }
    
    @keyframes card-appear {
      to { opacity: 1; transform: perspective(1000px) rotateY(0) translateX(0); }
    }
    
    .card-glow {
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(var(--primary-rgb), .08) 0%, transparent 50%);
      animation: glow-rotate 15s linear infinite;
    }
    
    @keyframes glow-rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    .card-accent-bar {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
      background-size: 200% 100%;
      animation: gradient-flow 3s ease infinite;
    }
    
    @keyframes gradient-flow {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }
    
    .card-content { position: relative; z-index: 2; }
    
    .card-badge-premium {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(145deg, var(--primary-light), #d6e6ff);
      color: var(--primary);
      padding: 10px 20px;
      border-radius: 100px;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 22px;
    }
    
    .admission-card-3d h3 {
      font-size: 1.8rem;
      margin-bottom: 10px;
      color: var(--dark);
      letter-spacing: -0.5px;
    }
    
    .admission-card-3d > p {
      color: var(--text-muted);
      margin-bottom: 28px;
      font-size: 0.95rem;
    }
    
    /* Premium Form */
    .form-premium { position: relative; z-index: 2; }
    .form-group-premium { margin-bottom: 18px; position: relative; }
    
    .form-group-premium input,
    .form-group-premium select {
      width: 100%;
      padding: 16px 20px;
      padding-left: 52px;
      background: var(--light);
      border: 2px solid transparent;
      border-radius: 16px;
      color: var(--text);
      font-family: inherit;
      font-size: 0.95rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .form-group-premium .form-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 1.1rem;
      transition: color 0.3s;
      z-index: 2;
    }
    
    .form-group-premium input:focus,
    .form-group-premium select:focus {
      outline: none;
      border-color: var(--primary);
      background: white;
      box-shadow: 0 0 0 4px rgba(var(--primary-rgb), .12);
    }
    
    .form-group-premium input:focus + .form-icon,
    .form-group-premium select:focus + .form-icon {
      color: var(--primary);
    }
    
    .form-group-premium input::placeholder { color: var(--text-light); }
    
    .btn-submit-premium {
      width: 100%;
      padding: 18px;
      background: linear-gradient(145deg, var(--secondary), #ff8f65);
      color: white;
      border: none;
      border-radius: 16px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
      overflow: hidden;
      margin-top: 8px;
    }
    
    .btn-submit-premium:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: var(--shadow-secondary);
    }
    
    .btn-submit-premium i { transition: transform 0.3s; }
    .btn-submit-premium:hover i { transform: translateX(5px); }
    
    .card-footer-info {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid var(--border-light);
      color: var(--text-light);
      font-size: 0.85rem;
    }
    
    .card-footer-info i { color: var(--accent); }
    
    /* ======================
       SCROLLING MARQUEE
    ====================== */
    .marquee-section {
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      padding: 18px 0;
      overflow: hidden;
      position: relative;
      z-index: 10;
    }
    
    .marquee-track {
      display: flex;
      animation: marquee 40s linear infinite;
      white-space: nowrap;
    }
    
    .marquee-item {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      color: white;
      font-weight: 500;
      font-size: 0.95rem;
      padding: 0 50px;
    }
    
    .marquee-item i { 
      color: var(--gold);
      font-size: 1.1rem;
    }
    
    @keyframes marquee {
      from { transform: translateX(0); }
      to { transform: translateX(-50%); }
    }
    
    /* ======================
       STATS SECTION
    ====================== */
    .stats-section {
      padding: 80px 0 0;
      margin-top: 0;
      position: relative;
      z-index: 15;
    }
    
    .stats-container {
      background: white;
      border-radius: 28px;
      padding: 50px 60px;
      box-shadow: var(--shadow-xl);
      border: 1px solid var(--border-light);
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 40px;
    }
    
    .stat-card {
      text-align: center;
      padding: 25px;
      border-radius: 20px;
      background: linear-gradient(145deg, var(--light), white);
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .stat-card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: var(--shadow-lg);
    }
    
    .stat-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(145deg, var(--primary-light), #d6e6ff);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 18px;
      font-size: 28px;
      color: var(--primary);
      transition: all 0.4s;
    }
    
    .stat-card:hover .stat-icon {
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      color: white;
      transform: rotateY(180deg);
    }
    
    .stat-number {
      font-family: 'Playfair Display', serif;
      font-size: 3rem;
      font-weight: 800;
      background: linear-gradient(145deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1;
      margin-bottom: 8px;
    }
    
    .stat-label { 
      font-size: 0.95rem; 
      color: var(--text-muted); 
      font-weight: 500; 
    }
    
    /* ======================
       SECTION STYLES
    ====================== */
    .section { padding: 120px 0; position: relative; }
    .section-alt { background: var(--light); }
    
    .section-header { text-align: center; margin-bottom: 70px; }
    
    .section-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(145deg, var(--primary-light), #d6e6ff);
      color: var(--primary);
      padding: 12px 28px;
      border-radius: 100px;
      font-weight: 700;
      font-size: 0.8rem;
      margin-bottom: 22px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }
    
    .section-title {
      font-size: clamp(2rem, 4vw, 3rem);
      margin-bottom: 18px;
      color: var(--dark);
      letter-spacing: -1px;
    }
    
    .section-subtitle {
      font-size: 1.15rem;
      color: var(--text-muted);
      max-width: 650px;
      margin: 0 auto;
      line-height: 1.8;
    }
    
    /* ======================
       ABOUT SECTION
    ====================== */
    .about-grid {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      gap: 80px;
      align-items: center;
    }
    
    .about-image-wrapper {
      position: relative;
    }
    
    .about-image {
      position: relative;
      border-radius: 28px;
      overflow: hidden;
      box-shadow: var(--shadow-xl);
    }
    
    .about-image img {
      width: 100%;
      height: 500px;
      object-fit: cover;
      transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .about-image:hover img { transform: scale(1.08); }
    
    .about-image-badge {
      position: absolute;
      bottom: -25px;
      right: -25px;
      background: white;
      padding: 25px 35px;
      border-radius: 20px;
      box-shadow: var(--shadow-lg);
      text-align: center;
    }
    
    .about-image-badge .number {
      font-family: 'Playfair Display', serif;
      font-size: 2.8rem;
      font-weight: 800;
      color: var(--secondary);
      line-height: 1;
    }
    
    .about-image-badge .label {
      font-size: 0.85rem;
      color: var(--text-muted);
      font-weight: 500;
    }
    
    .about-content .section-badge { margin-bottom: 18px; }
    
    .about-content h2 {
      font-size: 2.5rem;
      margin-bottom: 22px;
      color: var(--dark);
      letter-spacing: -1px;
    }
    
    .about-content > p {
      color: var(--text-muted);
      margin-bottom: 30px;
      line-height: 1.9;
      font-size: 1.05rem;
    }
    
    .about-features {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 35px;
    }
    
    .about-feature {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      padding: 18px;
      background: var(--light);
      border-radius: 16px;
      transition: all 0.3s;
    }
    
    .about-feature:hover {
      background: white;
      box-shadow: var(--shadow);
      transform: translateY(-3px);
    }
    
    .about-feature-icon {
      width: 45px;
      height: 45px;
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.1rem;
      flex-shrink: 0;
    }
    
    .about-feature h5 {
      font-size: 1rem;
      margin-bottom: 3px;
      color: var(--dark);
      font-weight: 600;
    }
    
    .about-feature p {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin: 0;
    }
    
    /* ======================
       WHY CHOOSE US
    ====================== */
    .why-section { 
      background: linear-gradient(145deg, var(--dark), #1e293b);
      color: white;
    }
    
    .why-section .section-badge { 
      background: rgba(255,255,255,.1); 
      color: white; 
    }
    .why-section .section-title { color: white; }
    .why-section .section-subtitle { color: rgba(255,255,255,.7); }
    
    .why-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .why-card {
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 24px;
      padding: 40px 30px;
      text-align: center;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
      overflow: hidden;
    }
    
    .why-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(145deg, rgba(var(--primary-rgb), .1), transparent);
      opacity: 0;
      transition: opacity 0.4s;
    }
    
    .why-card:hover {
      transform: translateY(-12px);
      background: rgba(255,255,255,.08);
      border-color: rgba(var(--primary-rgb), .5);
    }
    
    .why-card:hover::before { opacity: 1; }
    
    .why-icon {
      width: 85px;
      height: 85px;
      background: linear-gradient(145deg, var(--primary), var(--secondary));
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
      font-size: 36px;
      color: white;
      transition: transform 0.4s;
      position: relative;
      z-index: 2;
    }
    
    .why-card:hover .why-icon { transform: scale(1.1) rotateZ(-5deg); }
    
    .why-card h4 {
      font-size: 1.35rem;
      margin-bottom: 15px;
      color: white;
      font-weight: 700;
      position: relative;
      z-index: 2;
    }
    
    .why-card p {
      color: rgba(255,255,255,.65);
      font-size: 0.95rem;
      line-height: 1.7;
      position: relative;
      z-index: 2;
    }
    
    /* ======================
       PROGRAMS SECTION
    ====================== */
    .programs-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }
    
    .program-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 24px;
      padding: 40px 30px;
      transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
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
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      transform: scaleX(0);
      transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
      transform-origin: left;
    }
    
    .program-card:hover {
      transform: translateY(-15px);
      box-shadow: var(--shadow-xl);
      border-color: transparent;
    }
    
    .program-card:hover::before { transform: scaleX(1); }
    
    .program-icon-wrap {
      width: 80px;
      height: 80px;
      background: linear-gradient(145deg, var(--primary-light), #d6e6ff);
      border-radius: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 34px;
      color: var(--primary);
      margin-bottom: 25px;
      transition: all 0.4s;
    }
    
    .program-card:hover .program-icon-wrap {
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      color: white;
      transform: scale(1.1);
    }
    
    .program-card h4 { 
      font-size: 1.4rem; 
      margin-bottom: 14px; 
      color: var(--dark); 
      font-weight: 700;
    }
    
    .program-card p { 
      color: var(--text-muted); 
      font-size: 0.95rem; 
      margin: 0; 
      line-height: 1.8; 
    }
    
    .program-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 20px;
      color: var(--primary);
      font-weight: 600;
      font-size: 0.9rem;
      text-decoration: none;
      transition: gap 0.3s;
    }
    
    .program-link:hover { gap: 14px; color: var(--primary-dark); }
    
    /* ======================
       PRINCIPAL SECTION
    ====================== */
    .principal-card {
      background: white;
      border-radius: 32px;
      overflow: hidden;
      display: grid;
      grid-template-columns: 380px 1fr;
      box-shadow: var(--shadow-xl);
      position: relative;
    }
    
    .principal-image {
      position: relative;
      overflow: hidden;
    }
    
    .principal-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      min-height: 500px;
      transition: transform 0.8s;
    }
    
    .principal-card:hover .principal-image img { transform: scale(1.08); }
    
    .principal-content {
      padding: 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
    }
    
    .quote-icon {
      font-size: 80px;
      color: var(--primary);
      opacity: 0.1;
      position: absolute;
      top: 30px;
      right: 40px;
      font-family: serif;
      line-height: 1;
    }
    
    .principal-content blockquote {
      font-size: 1.35rem;
      font-style: italic;
      color: var(--text);
      margin: 0 0 35px;
      padding-left: 30px;
      border-left: 4px solid var(--secondary);
      line-height: 1.9;
      position: relative;
    }
    
    .principal-info { display: flex; align-items: center; gap: 18px; }
    
    .principal-info-text h4 { 
      font-size: 1.4rem; 
      margin-bottom: 5px; 
      color: var(--dark); 
    }
    .principal-info-text p { 
      color: var(--primary); 
      margin: 0; 
      font-weight: 600; 
      font-size: 1rem;
    }
    
    /* ======================
       FACILITIES SECTION
    ====================== */
    .facilities-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 25px;
    }
    
    .facility-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 22px;
      padding: 35px 25px;
      text-align: center;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
      overflow: hidden;
    }
    
    .facility-card::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(145deg, rgba(var(--primary-rgb), .03), transparent);
      opacity: 0;
      transition: opacity 0.4s;
    }
    
    .facility-card:hover {
      transform: translateY(-12px) scale(1.02);
      box-shadow: var(--shadow-xl);
      border-color: transparent;
    }
    
    .facility-card:hover::after { opacity: 1; }
    
    .facility-icon {
      width: 75px;
      height: 75px;
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 30px;
      color: white;
      transition: all 0.4s;
      position: relative;
      z-index: 2;
    }
    
    .facility-card:hover .facility-icon { transform: scale(1.15) rotateZ(10deg); }
    
    .facility-card h4 { 
      font-size: 1.1rem; 
      margin-bottom: 10px; 
      color: var(--dark); 
      font-weight: 700;
      position: relative;
      z-index: 2;
    }
    .facility-card p { 
      color: var(--text-muted); 
      font-size: 0.88rem; 
      margin: 0;
      position: relative;
      z-index: 2;
    }
    
    /* ======================
       GALLERY SECTION
    ====================== */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 22px;
    }
    
    .gallery-item {
      border-radius: 22px;
      overflow: hidden;
      aspect-ratio: 1;
      box-shadow: var(--shadow);
      position: relative;
      cursor: pointer;
    }
    
    .gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .gallery-item::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(0,0,0,.5), transparent);
      opacity: 0;
      transition: opacity 0.4s;
    }
    
    .gallery-item:hover img { transform: scale(1.15); }
    .gallery-item:hover::after { opacity: 1; }
    
    .gallery-item .gallery-icon {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      color: white;
      font-size: 2rem;
      z-index: 5;
      transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .gallery-item:hover .gallery-icon { transform: translate(-50%, -50%) scale(1); }
    
    /* ======================
       LIGHTBOX GALLERY
    ====================== */
    .lightbox-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.95);
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(10px);
    }
    
    .lightbox-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    
    .lightbox-container {
      position: relative;
      max-width: 90vw;
      max-height: 85vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .lightbox-image-wrapper {
      position: relative;
      overflow: hidden;
      border-radius: 16px;
      box-shadow: 0 30px 100px rgba(0,0,0,.5);
    }
    
    .lightbox-image {
      max-width: 90vw;
      max-height: 80vh;
      object-fit: contain;
      transform: scale(0.8);
      opacity: 0;
      transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
      border-radius: 16px;
    }
    
    .lightbox-overlay.active .lightbox-image {
      transform: scale(1);
      opacity: 1;
    }
    
    /* Zoom Controls */
    .lightbox-zoom-controls {
      position: absolute;
      bottom: -60px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 12px;
      background: rgba(255,255,255,.1);
      padding: 10px 20px;
      border-radius: 50px;
      backdrop-filter: blur(10px);
    }
    
    .zoom-btn {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: rgba(255,255,255,.15);
      border: 1px solid rgba(255,255,255,.2);
      color: white;
      font-size: 1.2rem;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .zoom-btn:hover {
      background: var(--primary);
      transform: scale(1.1);
    }
    
    .zoom-level {
      color: white;
      font-size: 0.9rem;
      font-weight: 600;
      min-width: 50px;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    /* Navigation Arrows */
    .lightbox-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 60px;
      height: 60px;
      background: rgba(255,255,255,.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,.2);
      border-radius: 50%;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10;
    }
    
    .lightbox-nav:hover {
      background: var(--primary);
      transform: translateY(-50%) scale(1.15);
      box-shadow: 0 10px 30px rgba(var(--primary-rgb), .4);
    }
    
    .lightbox-nav:active {
      transform: translateY(-50%) scale(0.95);
    }
    
    .lightbox-prev { left: 30px; }
    .lightbox-next { right: 30px; }
    
    /* Close Button */
    .lightbox-close {
      position: absolute;
      top: 30px;
      right: 30px;
      width: 55px;
      height: 55px;
      background: rgba(255,255,255,.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,.2);
      border-radius: 50%;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10;
    }
    
    .lightbox-close:hover {
      background: #dc3545;
      transform: scale(1.15) rotate(90deg);
    }
    
    /* Image Counter */
    .lightbox-counter {
      position: absolute;
      top: 35px;
      left: 35px;
      color: white;
      font-size: 1rem;
      font-weight: 600;
      background: rgba(255,255,255,.1);
      padding: 10px 20px;
      border-radius: 30px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,.2);
    }
    
    .lightbox-counter span {
      color: var(--secondary);
    }
    
    /* Image Title */
    .lightbox-title {
      position: absolute;
      bottom: 100px;
      left: 50%;
      transform: translateX(-50%);
      color: white;
      font-size: 1.2rem;
      font-weight: 600;
      background: rgba(0,0,0,.5);
      padding: 12px 28px;
      border-radius: 50px;
      backdrop-filter: blur(10px);
      white-space: nowrap;
    }
    
    /* Thumbnail Strip */
    .lightbox-thumbnails {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
      padding: 12px 18px;
      background: rgba(0,0,0,.5);
      border-radius: 16px;
      backdrop-filter: blur(10px);
      max-width: 80vw;
      overflow-x: auto;
    }
    
    .lightbox-thumb {
      width: 60px;
      height: 60px;
      border-radius: 10px;
      overflow: hidden;
      cursor: pointer;
      opacity: 0.5;
      transition: all 0.3s;
      flex-shrink: 0;
      border: 3px solid transparent;
    }
    
    .lightbox-thumb.active {
      opacity: 1;
      border-color: var(--primary);
      transform: scale(1.1);
    }
    
    .lightbox-thumb:hover {
      opacity: 1;
    }
    
    .lightbox-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    /* Lightbox Responsive */
    @media (max-width: 768px) {
      .lightbox-nav {
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
      }
      .lightbox-prev { left: 15px; }
      .lightbox-next { right: 15px; }
      .lightbox-close {
        top: 15px;
        right: 15px;
        width: 45px;
        height: 45px;
      }
      .lightbox-counter {
        top: 15px;
        left: 15px;
        font-size: 0.85rem;
        padding: 8px 15px;
      }
      .lightbox-title {
        bottom: 80px;
        font-size: 1rem;
        padding: 10px 20px;
      }
      .lightbox-thumbnails {
        bottom: 10px;
        padding: 8px 12px;
        gap: 8px;
      }
      .lightbox-thumb {
        width: 45px;
        height: 45px;
      }
      .lightbox-zoom-controls {
        bottom: -50px;
        padding: 8px 15px;
      }
      .zoom-btn {
        width: 38px;
        height: 38px;
      }
    }
    
    /* ======================
       FLOATING WHATSAPP BUTTON
    ====================== */
    .whatsapp-float {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 9998;
    }
    
    .whatsapp-button {
      width: 65px;
      height: 65px;
      background: linear-gradient(145deg, #25d366, #128c7e);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2rem;
      text-decoration: none;
      box-shadow: 0 8px 30px rgba(37, 211, 102, 0.4);
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
      overflow: hidden;
    }
    
    .whatsapp-button::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,.3), transparent);
      border-radius: 50%;
    }
    
    .whatsapp-button:hover {
      transform: scale(1.15) rotate(-5deg);
      box-shadow: 0 15px 50px rgba(37, 211, 102, 0.5);
      color: white;
    }
    
    .whatsapp-button:active {
      transform: scale(1.05);
    }
    
    /* Pulse Animation */
    .whatsapp-button::after {
      content: '';
      position: absolute;
      inset: -4px;
      border-radius: 50%;
      border: 3px solid #25d366;
      animation: whatsapp-pulse 2s ease-in-out infinite;
    }
    
    @keyframes whatsapp-pulse {
      0%, 100% { transform: scale(1); opacity: 0.8; }
      50% { transform: scale(1.2); opacity: 0; }
    }
    
    /* Tooltip */
    .whatsapp-tooltip {
      position: absolute;
      right: 80px;
      top: 50%;
      transform: translateY(-50%);
      background: white;
      color: var(--dark);
      padding: 12px 20px;
      border-radius: 12px;
      font-size: 0.9rem;
      font-weight: 600;
      white-space: nowrap;
      box-shadow: var(--shadow-lg);
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
      pointer-events: none;
    }
    
    .whatsapp-tooltip::after {
      content: '';
      position: absolute;
      right: -8px;
      top: 50%;
      transform: translateY(-50%);
      border: 8px solid transparent;
      border-left-color: white;
    }
    
    .whatsapp-float:hover .whatsapp-tooltip {
      opacity: 1;
      visibility: visible;
      right: 85px;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
      .whatsapp-float {
        bottom: 20px;
        right: 20px;
      }
      .whatsapp-button {
        width: 58px;
        height: 58px;
        font-size: 1.8rem;
      }
      .whatsapp-tooltip {
        display: none;
      }
    }
    
    /* ======================
       NEWS SECTION
    ====================== */
    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 35px;
    }
    
    .news-card {
      background: white;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
      border: 1px solid var(--border-light);
    }
    
    .news-card:hover {
      transform: translateY(-12px);
      box-shadow: var(--shadow-xl);
    }
    
    .news-card-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      background: linear-gradient(145deg, var(--primary-light), #fff5f0);
      position: relative;
      overflow: hidden;
    }
    
    .news-card-image::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(0,0,0,.1), transparent);
    }
    
    .news-card-body { padding: 30px; }
    
    .news-card-date { 
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--primary); 
      font-size: 0.85rem; 
      font-weight: 600; 
      margin-bottom: 12px; 
    }
    
    .news-card h4 { 
      font-size: 1.25rem; 
      margin-bottom: 12px; 
      color: var(--dark);
      line-height: 1.4;
    }
    .news-card p { 
      color: var(--text-muted); 
      font-size: 0.95rem; 
      margin: 0;
      line-height: 1.7;
    }
    
    /* ======================
       TESTIMONIALS SECTION
    ====================== */
    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 35px;
    }
    
    .testimonial-card {
      background: white;
      border-radius: 24px;
      padding: 40px;
      position: relative;
      box-shadow: var(--shadow);
      border: 1px solid var(--border-light);
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .testimonial-card:hover {
      transform: translateY(-10px);
      box-shadow: var(--shadow-xl);
    }
    
    .testimonial-quote-icon {
      position: absolute;
      top: 25px;
      right: 30px;
      font-size: 70px;
      color: var(--primary);
      opacity: 0.08;
      font-family: serif;
      line-height: 1;
    }
    
    .testimonial-stars { 
      display: flex;
      gap: 3px;
      color: var(--gold); 
      margin-bottom: 20px; 
      font-size: 1.1rem; 
    }
    
    .testimonial-content { 
      color: var(--text); 
      margin-bottom: 28px; 
      line-height: 1.8; 
      min-height: 100px;
      font-size: 1rem;
    }
    
    .testimonial-author { display: flex; align-items: center; gap: 16px; }
    
    .testimonial-avatar {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--primary-light);
    }
    
    .testimonial-avatar-placeholder {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      background: linear-gradient(145deg, var(--primary), var(--secondary));
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 20px;
      color: white;
    }
    
    .testimonial-name { font-weight: 700; font-size: 1.05rem; color: var(--dark); }
    .testimonial-role { font-size: 0.88rem; color: var(--text-muted); }
    
    /* ======================
       CONTACT SECTION
    ====================== */
    .contact-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }
    
    .contact-card {
      background: white;
      border-radius: 24px;
      padding: 40px 30px;
      text-align: center;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      box-shadow: var(--shadow);
      border: 1px solid var(--border-light);
    }
    
    .contact-card:hover { 
      transform: translateY(-10px) scale(1.02); 
      box-shadow: var(--shadow-xl);
    }
    
    .contact-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(145deg, var(--primary), var(--secondary));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 22px;
      font-size: 26px;
      color: white;
      transition: transform 0.4s;
    }
    
    .contact-card:hover .contact-icon { transform: scale(1.15) rotateZ(-10deg); }
    
    .contact-card h4 { 
      font-size: 1.1rem; 
      margin-bottom: 12px; 
      color: var(--dark);
      font-weight: 700;
    }
    .contact-card p, .contact-card a {
      color: var(--text-muted);
      font-size: 0.95rem;
      margin: 0;
      text-decoration: none;
      display: block;
      transition: color 0.3s;
      line-height: 1.6;
    }
    .contact-card a:hover { color: var(--primary); }
    
    /* ======================
       CTA SECTION
    ====================== */
    .cta-section {
      background: linear-gradient(145deg, var(--primary), var(--primary-dark));
      padding: 100px 0;
      position: relative;
      overflow: hidden;
    }
    
    .cta-section::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 60%);
      border-radius: 50%;
    }
    
    .cta-content {
      text-align: center;
      position: relative;
      z-index: 2;
    }
    
    .cta-content h2 {
      font-size: clamp(2rem, 4vw, 3rem);
      color: white;
      margin-bottom: 20px;
    }
    
    .cta-content p {
      color: rgba(255,255,255,.8);
      font-size: 1.15rem;
      max-width: 600px;
      margin: 0 auto 35px;
    }
    
    .cta-buttons { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
    
    .btn-cta-primary {
      background: white;
      color: var(--primary);
      padding: 18px 40px;
      border-radius: 100px;
      font-weight: 700;
      font-size: 1rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.4s;
      box-shadow: 0 15px 40px rgba(0,0,0,.2);
    }
    
    .btn-cta-primary:hover {
      transform: translateY(-5px);
      box-shadow: 0 25px 60px rgba(0,0,0,.3);
      color: var(--primary-dark);
    }
    
    .btn-cta-secondary {
      background: transparent;
      border: 2px solid rgba(255,255,255,.5);
      color: white;
      padding: 18px 40px;
      border-radius: 100px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.4s;
    }
    
    .btn-cta-secondary:hover {
      background: rgba(255,255,255,.1);
      border-color: white;
      color: white;
    }
    
    /* ======================
       FOOTER
    ====================== */
    .footer {
      background: var(--dark);
      padding: 100px 0 40px;
      color: rgba(255,255,255,.8);
    }
    
    .footer-grid {
      display: grid;
      grid-template-columns: 1.5fr 1fr 1fr 1fr;
      gap: 60px;
      margin-bottom: 60px;
    }
    
    .footer-brand h3 { 
      font-size: 1.5rem; 
      margin-bottom: 20px; 
      color: white; 
    }
    .footer-brand p { 
      color: rgba(255,255,255,.6); 
      font-size: 1rem; 
      margin-bottom: 28px;
      line-height: 1.8;
    }
    
    .social-links { display: flex; gap: 14px; }
    
    .social-link {
      width: 48px;
      height: 48px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 20px;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .social-link:hover {
      background: linear-gradient(145deg, var(--primary), var(--secondary));
      border-color: transparent;
      transform: translateY(-5px) scale(1.1);
    }
    
    .footer-links h5 { 
      font-size: 1.1rem; 
      margin-bottom: 25px; 
      color: white;
      font-weight: 700;
    }
    .footer-links ul { list-style: none; padding: 0; margin: 0; }
    .footer-links a {
      color: rgba(255,255,255,.6);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 0;
      font-size: 0.95rem;
      transition: all 0.3s;
    }
    .footer-links a:hover { 
      color: var(--secondary); 
      transform: translateX(8px);
    }
    
    .footer-bottom {
      text-align: center;
      padding-top: 40px;
      border-top: 1px solid rgba(255,255,255,.1);
      color: rgba(255,255,255,.5);
      font-size: 0.9rem;
    }
    
    /* ======================
       RESPONSIVE DESIGN
    ====================== */
    @media (max-width: 1400px) {
      .container { padding: 0 30px; }
      .hero-grid { gap: 60px; }
    }
    
    @media (max-width: 1200px) {
      .hero-grid { grid-template-columns: 1fr; text-align: center; }
      .hero-content { max-width: 700px; margin: 0 auto; }
      .hero-subtitle { margin-left: auto; margin-right: auto; }
      .hero-buttons { justify-content: center; }
      .hero-stats { justify-content: center; }
      .admission-card-3d { max-width: 500px; margin: 60px auto 0; }
      .about-grid { grid-template-columns: 1fr; gap: 50px; }
      .about-image-wrapper { order: -1; }
      .about-content { text-align: center; }
      .about-features { max-width: 600px; margin: 0 auto 35px; }
      .principal-card { grid-template-columns: 1fr; }
      .principal-image img { min-height: 350px; }
      .principal-content { padding: 50px; text-align: center; }
      .principal-content blockquote { text-align: left; }
      .principal-info { justify-content: center; }
      .footer-grid { grid-template-columns: repeat(2, 1fr); gap: 40px; }
    }
    
    @media (max-width: 992px) {
      /* Mobile Navigation - Full Screen Menu */
      .nav-links {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        height: 100dvh;
        background: #ffffff;
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-start;
        padding: 90px 25px 40px;
        z-index: 1001;
        gap: 10px;
        overflow-y: auto;
        overflow-x: hidden;
        transform: translateY(-100%);
        opacity: 0;
        pointer-events: none;
        transition: transform 0.35s ease, opacity 0.35s ease;
        box-sizing: border-box;
      }
      
      .nav-links.active { 
        transform: translateY(0); 
        opacity: 1;
        pointer-events: auto;
      }
      
      .nav-links li { 
        width: 100%; 
        list-style: none;
      }
      
      .nav-link { 
        display: flex !important;
        align-items: center;
        padding: 18px 22px !important; 
        width: 100%; 
        text-align: left; 
        font-size: 1.05rem;
        font-weight: 500;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0f172a !important;
        text-decoration: none;
        transition: all 0.2s ease;
      }
      
      .nav-link:hover, 
      .nav-link:focus { 
        background: #e6f0ff; 
        border-color: #0052cc;
        color: #0052cc !important;
      }
      
      .nav-link::after { display: none !important; }
      
      .nav-cta { 
        width: 100%; 
        justify-content: center; 
        margin-top: 15px;
        padding: 18px 24px !important;
        background: linear-gradient(145deg, #ff6b35, #ff8f65) !important;
        color: #ffffff !important;
        border: none !important;
        font-weight: 600;
      }
      
      .nav-cta:hover {
        background: linear-gradient(145deg, #e55a2b, #ff7d55) !important;
      }
      
      /* Mobile toggle always visible */
      .mobile-toggle { 
        display: flex !important; 
        position: relative;
        z-index: 1002;
      }
      
      /* Ensure brand doesn't push toggle off screen */
      .nav-brand {
        flex: 1;
        min-width: 0;
        overflow: hidden;
      }
      
      .brand-name {
        font-size: 1.1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      
      .brand-tagline {
        font-size: 0.6rem;
        letter-spacing: 1.5px;
      }
      
      .brand-logo {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 12px;
      }
      
      .stats-section { padding: 60px 0 0; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
      .programs-grid { grid-template-columns: repeat(2, 1fr); }
      .why-grid { grid-template-columns: 1fr; max-width: 500px; margin: 0 auto; }
      .facilities-grid { grid-template-columns: repeat(2, 1fr); }
      .gallery-grid { grid-template-columns: repeat(2, 1fr); }
      .news-grid { grid-template-columns: 1fr; max-width: 500px; margin: 0 auto; }
      .testimonials-grid { grid-template-columns: 1fr; max-width: 500px; margin: 0 auto; }
      .contact-grid { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 768px) {
      .container { padding: 0 20px; }
      .section { padding: 70px 0; }
      .section-header { margin-bottom: 50px; }
      .section-title { font-size: 1.8rem; }
      .section-subtitle { font-size: 1rem; }
      
      /* Hero Mobile */
      .hero-premium { padding: 100px 0 60px; min-height: auto; }
      .hero-title { font-size: 2rem; letter-spacing: -0.5px; line-height: 1.2; }
      .hero-subtitle { font-size: 0.95rem; margin-bottom: 30px; }
      .admission-badge-animated { padding: 10px 20px; font-size: 0.8rem; margin-bottom: 20px; }
      
      .hero-stats { 
        flex-direction: row; 
        flex-wrap: wrap; 
        gap: 15px; 
        justify-content: center;
        margin-top: 35px;
      }
      .hero-stat { text-align: center; min-width: 80px; }
      .hero-stat-number { font-size: 1.6rem; }
      .hero-stat-label { font-size: 0.75rem; }
      
      .hero-buttons { flex-direction: column; gap: 12px; }
      .btn-magnetic { 
        width: 100%; 
        max-width: 300px; 
        justify-content: center;
        padding: 16px 30px;
        font-size: 0.95rem;
      }
      
      /* Admission Card Mobile */
      .admission-card-3d { 
        margin-top: 40px; 
        padding: 30px 22px;
        border-radius: 22px;
      }
      .admission-card-3d h3 { font-size: 1.5rem; }
      
      /* Stats Mobile */
      .stats-section { padding: 40px 0 0; }
      .stats-container { padding: 25px 18px; border-radius: 18px; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
      .stat-card { padding: 18px 12px; border-radius: 16px; }
      .stat-number { font-size: 1.8rem; }
      .stat-label { font-size: 0.8rem; }
      .stat-icon { width: 50px; height: 50px; font-size: 20px; margin-bottom: 12px; border-radius: 14px; }
      
      /* Programs Mobile */
      .programs-grid { grid-template-columns: 1fr; gap: 20px; }
      .program-card { padding: 30px 22px; }
      .program-icon-wrap { width: 65px; height: 65px; font-size: 28px; }
      
      /* Facilities Mobile */
      .facilities-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
      .facility-card { padding: 22px 14px; }
      .facility-icon { width: 55px; height: 55px; font-size: 22px; }
      .facility-card h4 { font-size: 0.95rem; }
      .facility-card p { font-size: 0.8rem; }
      
      /* Gallery Mobile */
      .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
      .gallery-item { border-radius: 14px; }
      
      /* Contact Mobile */
      .contact-grid { grid-template-columns: 1fr; gap: 15px; }
      .contact-card { padding: 30px 20px; }
      
      /* Footer Mobile */
      .footer { padding: 70px 0 30px; }
      .footer-grid { grid-template-columns: 1fr; text-align: center; gap: 35px; }
      .social-links { justify-content: center; }
      .footer-links a { justify-content: center; }
      .footer-links a:hover { transform: translateX(0); }
      
      /* CTA Mobile */
      .cta-section { padding: 70px 0; }
      .cta-buttons { flex-direction: column; align-items: center; gap: 12px; }
      .btn-cta-primary, .btn-cta-secondary { width: 100%; max-width: 280px; justify-content: center; }
      
      /* Hide decorative elements */
      .scroll-indicator { display: none; }
      .parallax-icon { display: none; }
      
      /* Marquee mobile */
      .marquee-section { padding: 14px 0; }
      .marquee-item { padding: 0 25px; font-size: 0.8rem; }
    }
    
    @media (max-width: 576px) {
      .navbar-premium { padding: 8px 0; }
      .navbar-premium .container { padding: 0 12px; }
      
      .nav-brand { gap: 10px; }
      .brand-logo { width: 40px; height: 40px; min-width: 40px; font-size: 18px; border-radius: 10px; }
      .brand-name { font-size: 1rem; }
      .brand-tagline { font-size: 0.55rem; letter-spacing: 1px; }
      
      .mobile-toggle { width: 40px; height: 40px; min-width: 40px; font-size: 1.3rem; border-radius: 10px; }
      
      .nav-links { padding: 75px 16px 30px; }
      .nav-link { padding: 14px 16px !important; font-size: 0.95rem; }
      
      .hero-title { font-size: 1.75rem; }
      .hero-subtitle { font-size: 0.9rem; }
      
      .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
      .stat-card { padding: 15px 10px; }
      .stat-number { font-size: 1.5rem; }
      .stat-icon { width: 42px; height: 42px; font-size: 18px; }
      
      .facilities-grid { grid-template-columns: 1fr; }
      
      .about-features { grid-template-columns: 1fr; }
      .about-image img { height: 300px; }
      .about-image-badge { bottom: -15px; right: -10px; padding: 15px 22px; }
      .about-image-badge .number { font-size: 2rem; }
      
      .principal-card { border-radius: 22px; }
      .principal-content { padding: 30px 20px; }
      .principal-content blockquote { font-size: 1rem; padding-left: 15px; }
      .principal-image img { min-height: 280px; }
      
      .admission-card-3d { padding: 25px 18px; }
      .form-group-premium input,
      .form-group-premium select { padding: 14px 16px 14px 45px; font-size: 0.9rem; }
      .btn-submit-premium { padding: 16px; }
    }
    
    /* Mobile Menu Overlay - NO BLUR */
    .nav-overlay {
      display: none;
    }
    
    /* Body lock when menu is open */
    body.nav-open {
      overflow: hidden;
      position: fixed;
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="animated-bg"></div>
  
  <div class="content-wrapper">
    <!-- Premium Navbar -->
    <nav class="navbar-premium" id="navbar">
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
            <li><a class="nav-link" href="#about" onclick="closeNav()">About</a></li>
            <li><a class="nav-link" href="#programs" onclick="closeNav()">Programs</a></li>
            <li><a class="nav-link" href="#facilities" onclick="closeNav()">Facilities</a></li>
            <li><a class="nav-link" href="#gallery" onclick="closeNav()">Gallery</a></li>
            <li><a class="nav-link" href="#contact" onclick="closeNav()">Contact</a></li>
            <li><a class="nav-link nav-cta" href="#hero-form" onclick="closeNav()"><i class="bi bi-pencil-square me-2"></i>Apply Now</a></li>
          </ul>
          
          <button class="mobile-toggle" onclick="toggleNav()" aria-label="Toggle Menu">
            <i class="bi bi-list" id="menuIcon"></i>
          </button>
        </div>
      </div>
    </nav>
    
    <!-- Hero Banner Slider (if enabled) -->
    <?php include __DIR__ . '/partials/hero-slider.php'; ?>
    
    <!-- Hero Section with Video Background & Parallax -->
    <section class="hero-premium">
      <!-- Video Background (optional - uses hero_video setting) -->
      <?php $heroVideo = getSetting('hero_video', 'https://cdn.pixabay.com/video/2020/09/30/50718-466259222_large.mp4'); ?>
      <div class="hero-video-bg">
        <video autoplay muted loop playsinline poster="<?= e($heroImage) ?>">
          <source src="<?= e($heroVideo) ?>" type="video/mp4">
        </video>
        <div class="hero-video-overlay"></div>
      </div>
      
      <!-- Parallax Layers -->
      <div class="parallax-container">
        <div class="parallax-layer parallax-layer-1" data-parallax-speed="0.03">
          <div class="parallax-shape shape-circle-1"></div>
          <div class="parallax-shape shape-circle-2"></div>
        </div>
        <div class="parallax-layer parallax-layer-2" data-parallax-speed="0.05">
          <div class="parallax-shape shape-circle-3"></div>
          <div class="parallax-shape shape-square"></div>
          <div class="parallax-shape shape-diamond"></div>
        </div>
        <div class="parallax-layer parallax-layer-3" data-parallax-speed="0.08">
          <i class="bi bi-book parallax-icon icon-book"></i>
          <i class="bi bi-mortarboard parallax-icon icon-graduation"></i>
          <i class="bi bi-trophy parallax-icon icon-trophy"></i>
          <i class="bi bi-star parallax-icon icon-star"></i>
          <i class="bi bi-pencil parallax-icon icon-pencil"></i>
        </div>
      </div>
      
      <!-- Floating Particles -->
      <div class="hero-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
      </div>
      
      <!-- Scroll Indicator -->
      <div class="scroll-indicator">
        <span>Scroll</span>
        <div class="scroll-mouse"></div>
      </div>
      
      <div class="container">
        <div class="hero-grid">
          <div class="hero-content">
            <?php if ($admissionOpen): ?>
            <div class="admission-badge-animated">
              <div class="badge-dot"></div>
              Admissions Open for <?= e($admissionYear) ?>
            </div>
            <?php endif; ?>
            
            <h1 class="hero-title">
              <span class="line"><span>Building Tomorrow's</span></span>
              <span class="line"><span class="gradient-text">Leaders</span> <span>Today</span></span>
            </h1>
            
            <p class="hero-subtitle"><?= e($heroSubtitle) ?> Join our community of learners where excellence meets opportunity, and every student discovers their unique potential.</p>
            
            <div class="hero-buttons">
              <a href="#hero-form" class="btn-magnetic btn-primary-magnetic">
                <i class="bi bi-pencil-square"></i>
                Start Application
              </a>
              <a href="#about" class="btn-magnetic btn-secondary-magnetic">
                <i class="bi bi-play-circle"></i>
                Explore Campus
              </a>
            </div>
            
            <div class="hero-stats">
              <div class="hero-stat">
                <div class="hero-stat-number"><?= e($statYears) ?></div>
                <div class="hero-stat-label">Years Excellence</div>
              </div>
              <div class="hero-stat">
                <div class="hero-stat-number"><?= e($statStudents) ?></div>
                <div class="hero-stat-label">Happy Students</div>
              </div>
              <div class="hero-stat">
                <div class="hero-stat-number"><?= e($statResults) ?></div>
                <div class="hero-stat-label">Board Results</div>
              </div>
            </div>
          </div>
          
          <div class="admission-card-3d" id="hero-form">
            <div class="card-glow"></div>
            <div class="card-accent-bar"></div>
            <div class="card-content">
              <div class="card-badge-premium">
                <i class="bi bi-lightning-charge-fill"></i>
                Quick Enquiry
              </div>
              <h3>Begin Your Journey</h3>
              <p>Fill in your details and our team will connect with you within 24 hours.</p>
              
              <?php if ($success): ?>
                <div class="alert alert-success mb-4">
                  <i class="bi bi-check-circle me-2"></i>
                  Thank you! Your enquiry has been submitted successfully.
                </div>
              <?php elseif ($error): ?>
                <div class="alert alert-danger mb-4"><?= e($error) ?></div>
              <?php endif; ?>
              
              <form class="form-premium" method="post">
                <div class="form-group-premium">
                  <input type="text" name="student_name" placeholder="Student's Name *" required>
                  <i class="bi bi-person form-icon"></i>
                </div>
                <div class="form-group-premium">
                  <input type="text" name="parent_name" placeholder="Parent's Name *" required>
                  <i class="bi bi-people form-icon"></i>
                </div>
                <div class="form-group-premium">
                  <input type="tel" name="phone" placeholder="Phone Number *" required>
                  <i class="bi bi-telephone form-icon"></i>
                </div>
                <div class="form-group-premium">
                  <select name="class_applying" required>
                    <option value="">Select Class *</option>
                    <option value="Nursery">Nursery</option>
                    <option value="LKG">LKG</option>
                    <option value="UKG">UKG</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                      <option value="Class <?= $i ?>">Class <?= $i ?></option>
                    <?php endfor; ?>
                  </select>
                  <i class="bi bi-book form-icon"></i>
                </div>
                <input type="hidden" name="email" value="enquiry@school.com">
                <button type="submit" class="btn-submit-premium">
                  Submit Enquiry <i class="bi bi-arrow-right ms-2"></i>
                </button>
              </form>
              
              <div class="card-footer-info">
                <i class="bi bi-shield-check"></i>
                <span>Your information is secure with us</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Scrolling Marquee -->
    <div class="marquee-section">
      <div class="marquee-track">
        <div class="marquee-item"><i class="bi bi-trophy"></i> 100% Board Results</div>
        <div class="marquee-item"><i class="bi bi-star"></i> CBSE Affiliated</div>
        <div class="marquee-item"><i class="bi bi-people"></i> Expert Faculty</div>
        <div class="marquee-item"><i class="bi bi-building"></i> Modern Infrastructure</div>
        <div class="marquee-item"><i class="bi bi-laptop"></i> Smart Classrooms</div>
        <div class="marquee-item"><i class="bi bi-heart"></i> Safe Environment</div>
        <div class="marquee-item"><i class="bi bi-trophy"></i> 100% Board Results</div>
        <div class="marquee-item"><i class="bi bi-star"></i> CBSE Affiliated</div>
        <div class="marquee-item"><i class="bi bi-people"></i> Expert Faculty</div>
        <div class="marquee-item"><i class="bi bi-building"></i> Modern Infrastructure</div>
        <div class="marquee-item"><i class="bi bi-laptop"></i> Smart Classrooms</div>
        <div class="marquee-item"><i class="bi bi-heart"></i> Safe Environment</div>
      </div>
    </div>
    
    <!-- Stats Section -->
    <section class="stats-section">
      <div class="container">
        <div class="stats-container" data-scroll>
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
              <div class="stat-number"><?= e($statYears) ?></div>
              <div class="stat-label">Years of Excellence</div>
            </div>
            <div class="stat-card delay-1">
              <div class="stat-icon"><i class="bi bi-mortarboard"></i></div>
              <div class="stat-number"><?= e($statStudents) ?></div>
              <div class="stat-label">Happy Students</div>
            </div>
            <div class="stat-card delay-2">
              <div class="stat-icon"><i class="bi bi-person-check"></i></div>
              <div class="stat-number"><?= e($statTeachers) ?></div>
              <div class="stat-label">Expert Teachers</div>
            </div>
            <div class="stat-card delay-3">
              <div class="stat-icon"><i class="bi bi-trophy"></i></div>
              <div class="stat-number"><?= e($statResults) ?></div>
              <div class="stat-label">Board Results</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- About Section -->
    <section class="section" id="about">
      <div class="container">
        <div class="about-grid">
          <div class="about-image-wrapper" data-scroll="fade-left">
            <div class="about-image">
              <img src="<?= e($aboutImage) ?>" alt="About Us">
            </div>
            <div class="about-image-badge">
              <div class="number"><?= e($statYears) ?></div>
              <div class="label">Years Legacy</div>
            </div>
          </div>
          <div class="about-content" data-scroll="fade-right">
            <div class="section-badge"><i class="bi bi-info-circle"></i> About Us</div>
            <h2><?= e($aboutTitle) ?></h2>
            <p><?= e($aboutText) ?></p>
            
            <div class="about-features">
              <div class="about-feature">
                <div class="about-feature-icon"><i class="bi bi-award"></i></div>
                <div>
                  <h5>Award Winning</h5>
                  <p>Recognized for excellence in education</p>
                </div>
              </div>
              <div class="about-feature">
                <div class="about-feature-icon"><i class="bi bi-people"></i></div>
                <div>
                  <h5>Expert Faculty</h5>
                  <p>Highly qualified teaching staff</p>
                </div>
              </div>
              <div class="about-feature">
                <div class="about-feature-icon"><i class="bi bi-laptop"></i></div>
                <div>
                  <h5>Modern Tech</h5>
                  <p>Digital-first learning approach</p>
                </div>
              </div>
              <div class="about-feature">
                <div class="about-feature-icon"><i class="bi bi-heart"></i></div>
                <div>
                  <h5>Safe Campus</h5>
                  <p>Secure learning environment</p>
                </div>
              </div>
            </div>
            
            <a href="#programs" class="btn-magnetic btn-primary-magnetic">
              <i class="bi bi-arrow-right"></i>
              Explore Programs
            </a>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Why Choose Us -->
    <section class="section why-section">
      <div class="container">
        <div class="section-header" data-scroll>
          <div class="section-badge"><i class="bi bi-star"></i> Why Choose Us</div>
          <h2 class="section-title">What Makes Us Different</h2>
          <p class="section-subtitle">We believe in nurturing not just academic excellence, but well-rounded individuals ready to take on the world.</p>
        </div>
        
        <div class="why-grid">
          <div class="why-card" data-scroll>
            <div class="why-icon"><i class="bi bi-lightbulb"></i></div>
            <h4>Innovative Learning</h4>
            <p>Cutting-edge teaching methods combined with technology to make learning engaging and effective for every student.</p>
          </div>
          <div class="why-card" data-scroll class="delay-1">
            <div class="why-icon"><i class="bi bi-person-heart"></i></div>
            <h4>Individual Attention</h4>
            <p>Small class sizes ensure personalized care and focused guidance for each student's unique learning journey.</p>
          </div>
          <div class="why-card" data-scroll class="delay-2">
            <div class="why-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <h4>Proven Excellence</h4>
            <p>Consistent outstanding results with students excelling in academics, sports, and extracurricular activities.</p>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Programs Section -->
    <section class="section section-alt" id="programs">
      <div class="container">
        <div class="section-header" data-scroll>
          <div class="section-badge"><i class="bi bi-book"></i> Academic Programs</div>
          <h2 class="section-title">Nurturing Future Leaders</h2>
          <p class="section-subtitle">Comprehensive education programs designed for holistic development from early years to senior secondary.</p>
        </div>
        
        <div class="programs-grid">
          <?php foreach ($programs as $index => $program): ?>
          <div class="program-card" data-scroll class="delay-<?= $index % 3 + 1 ?>">
            <div class="program-icon-wrap"><i class="bi <?= e($program['icon']) ?>"></i></div>
            <h4><?= e($program['title']) ?></h4>
            <p><?= e($program['desc']) ?></p>
            <a href="#hero-form" class="program-link">Learn More <i class="bi bi-arrow-right"></i></a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    
    <!-- Principal Section -->
    <section class="section">
      <div class="container">
        <div class="section-header" data-scroll>
          <div class="section-badge"><i class="bi bi-quote"></i> Principal's Message</div>
        </div>
        
        <div class="principal-card" data-scroll="zoom-in">
          <div class="principal-image">
            <img src="<?= e($principalImage) ?>" alt="<?= e($principalName) ?>">
          </div>
          <div class="principal-content">
            <div class="quote-icon">"</div>
            <blockquote>"<?= e($principalMessage) ?>"</blockquote>
            <div class="principal-info">
              <div class="principal-info-text">
                <h4><?= e($principalName) ?></h4>
                <p><?= e($principalTitle) ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Facilities Section -->
    <section class="section section-alt" id="facilities">
      <div class="container">
        <div class="section-header" data-scroll>
          <div class="section-badge"><i class="bi bi-building"></i> Our Facilities</div>
          <h2 class="section-title">World-Class Infrastructure</h2>
          <p class="section-subtitle">State-of-the-art facilities to support every aspect of student development.</p>
        </div>
        
        <div class="facilities-grid">
          <?php foreach ($facilities as $index => $facility): ?>
          <div class="facility-card" data-scroll>
            <div class="facility-icon"><i class="bi <?= e($facility['icon']) ?>"></i></div>
            <h4><?= e($facility['title']) ?></h4>
            <p><?= e($facility['desc']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    
    <!-- Gallery Section -->
    <section class="section" id="gallery">
      <div class="container">
        <div class="section-header" data-scroll>
          <div class="section-badge"><i class="bi bi-images"></i> Gallery</div>
          <h2 class="section-title">Campus Life</h2>
          <p class="section-subtitle">A glimpse into our vibrant campus and memorable moments.</p>
        </div>
        
        <div class="gallery-grid" data-scroll>
          <?php foreach ($GALLERY_IMAGES as $img): ?>
          <div class="gallery-item">
            <img src="<?= e($img['url']) ?>" alt="<?= e($img['title']) ?>">
            <i class="bi bi-zoom-in gallery-icon"></i>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    
    <!-- News Section -->
    <?php if (!empty($newsArticles)): ?>
    <section class="section section-alt">
      <div class="container">
        <div class="section-header" data-scroll>
          <div class="section-badge"><i class="bi bi-newspaper"></i> Latest Updates</div>
          <h2 class="section-title">News & Announcements</h2>
        </div>
        
        <div class="news-grid">
          <?php foreach (array_slice($newsArticles, 0, 3) as $news): ?>
          <div class="news-card" data-scroll>
            <?php if (!empty($news['image'])): ?>
              <img src="<?= e($news['image']) ?>" alt="<?= e($news['title']) ?>" class="news-card-image">
            <?php else: ?>
              <div class="news-card-image"></div>
            <?php endif; ?>
            <div class="news-card-body">
              <div class="news-card-date">
                <i class="bi bi-calendar3"></i>
                <?= e(!empty($news['date']) ? date('M d, Y', strtotime($news['date'])) : '') ?>
              </div>
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
    <section class="section">
      <div class="container">
        <div class="section-header" data-scroll>
          <div class="section-badge"><i class="bi bi-chat-quote"></i> Testimonials</div>
          <h2 class="section-title">What Parents Say</h2>
        </div>
        
        <div class="testimonials-grid">
          <?php foreach (array_slice($testimonials, 0, 3) as $t): ?>
          <div class="testimonial-card" data-scroll>
            <div class="testimonial-quote-icon">"</div>
            <div class="testimonial-stars">
              <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="bi <?= $i < (int)($t['rating'] ?? 5) ? 'bi-star-fill' : 'bi-star' ?>"></i>
              <?php endfor; ?>
            </div>
            <p class="testimonial-content">"<?= e(substr($t['content'], 0, 180)) ?>"</p>
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
    
    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content" data-scroll>
          <h2>Ready to Join Our Family?</h2>
          <p>Take the first step towards a brighter future for your child. Our admissions team is here to guide you through the process.</p>
          <div class="cta-buttons">
            <a href="#hero-form" class="btn-cta-primary">
              <i class="bi bi-pencil-square"></i>
              Apply Now
            </a>
            <a href="tel:<?= e($phone1) ?>" class="btn-cta-secondary">
              <i class="bi bi-telephone"></i>
              Call Us
            </a>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Contact Section -->
    <section class="section section-alt" id="contact">
      <div class="container">
        <div class="section-header" data-scroll>
          <div class="section-badge"><i class="bi bi-envelope"></i> Get in Touch</div>
          <h2 class="section-title">Contact Us</h2>
        </div>
        
        <div class="contact-grid">
          <div class="contact-card" data-scroll>
            <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
            <h4>Address</h4>
            <p><?= e($address) ?></p>
          </div>
          <div class="contact-card" data-scroll class="delay-1">
            <div class="contact-icon"><i class="bi bi-telephone"></i></div>
            <h4>Phone</h4>
            <a href="tel:<?= e($phone1) ?>"><?= e($phone1) ?></a>
            <a href="tel:<?= e($phone2) ?>"><?= e($phone2) ?></a>
          </div>
          <div class="contact-card" data-scroll class="delay-2">
            <div class="contact-icon"><i class="bi bi-envelope"></i></div>
            <h4>Email</h4>
            <a href="mailto:<?= e($emailContact) ?>"><?= e($emailContact) ?></a>
            <a href="mailto:<?= e($admissionEmail) ?>"><?= e($admissionEmail) ?></a>
          </div>
          <div class="contact-card" data-scroll class="delay-3">
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
              <a href="<?= e($socialFacebook) ?>" class="social-link" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
              <a href="<?= e($socialInstagram) ?>" class="social-link" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
              <a href="<?= e($socialYoutube) ?>" class="social-link" target="_blank" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
              <a href="<?= e($socialTwitter) ?>" class="social-link" target="_blank" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            </div>
          </div>
          <div class="footer-links">
            <h5>Quick Links</h5>
            <ul>
              <li><a href="#about"><i class="bi bi-chevron-right"></i> About Us</a></li>
              <li><a href="#programs"><i class="bi bi-chevron-right"></i> Programs</a></li>
              <li><a href="#facilities"><i class="bi bi-chevron-right"></i> Facilities</a></li>
              <li><a href="#gallery"><i class="bi bi-chevron-right"></i> Gallery</a></li>
            </ul>
          </div>
          <div class="footer-links">
            <h5>Academics</h5>
            <ul>
              <li><a href="#programs"><i class="bi bi-chevron-right"></i> Primary</a></li>
              <li><a href="#programs"><i class="bi bi-chevron-right"></i> Middle School</a></li>
              <li><a href="#programs"><i class="bi bi-chevron-right"></i> High School</a></li>
              <li><a href="#hero-form"><i class="bi bi-chevron-right"></i> Admissions</a></li>
            </ul>
          </div>
          <div class="footer-links">
            <h5>Contact</h5>
            <ul>
              <li><a href="tel:<?= e($phone1) ?>"><i class="bi bi-telephone"></i> <?= e($phone1) ?></a></li>
              <li><a href="mailto:<?= e($emailContact) ?>"><i class="bi bi-envelope"></i> <?= e($emailContact) ?></a></li>
              <li><a href="#contact"><i class="bi bi-geo-alt"></i> Visit Campus</a></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          &copy; <?= date('Y') ?> <?= e($schoolName) ?>. All Rights Reserved. | Crafted with <i class="bi bi-heart-fill text-danger"></i> for Education
        </div>
      </div>
    </footer>
    
    <!-- Floating WhatsApp Button -->
    <?php $whatsappNumber = getSetting('whatsapp_number', $phone1); ?>
    <?php $whatsappMessage = urlencode(getSetting('whatsapp_message', 'Hello! I am interested in admission for my child. Please share more details.')); ?>
    <div class="whatsapp-float">
      <div class="whatsapp-tooltip">
        <i class="bi bi-chat-dots me-2"></i>Chat with us!
      </div>
      <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsappNumber) ?>?text=<?= $whatsappMessage ?>" 
         class="whatsapp-button" 
         target="_blank" 
         rel="noopener noreferrer"
         aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
      </a>
    </div>
    
    <!-- Lightbox Gallery Modal -->
    <div class="lightbox-overlay" id="lightboxOverlay">
      <button class="lightbox-close" onclick="closeLightbox()" aria-label="Close">
        <i class="bi bi-x-lg"></i>
      </button>
      <div class="lightbox-counter">
        <span id="lightboxCurrent">1</span> / <span id="lightboxTotal">8</span>
      </div>
      <button class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)" aria-label="Previous">
        <i class="bi bi-chevron-left"></i>
      </button>
      <div class="lightbox-container">
        <div class="lightbox-image-wrapper">
          <img src="" alt="" class="lightbox-image" id="lightboxImage">
        </div>
        <div class="lightbox-zoom-controls">
          <button class="zoom-btn" onclick="zoomLightbox(-0.25)" aria-label="Zoom Out">
            <i class="bi bi-zoom-out"></i>
          </button>
          <div class="zoom-level" id="zoomLevel">100%</div>
          <button class="zoom-btn" onclick="zoomLightbox(0.25)" aria-label="Zoom In">
            <i class="bi bi-zoom-in"></i>
          </button>
          <button class="zoom-btn" onclick="resetZoom()" aria-label="Reset Zoom">
            <i class="bi bi-arrows-fullscreen"></i>
          </button>
        </div>
      </div>
      <button class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)" aria-label="Next">
        <i class="bi bi-chevron-right"></i>
      </button>
      <div class="lightbox-title" id="lightboxTitle"></div>
      <div class="lightbox-thumbnails" id="lightboxThumbnails"></div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Navbar Scroll Effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    });
    
    // Mobile Navigation - Simple Toggle (No Overlay)
    const navLinks = document.getElementById('navLinks');
    const mobileToggle = document.querySelector('.mobile-toggle');
    const menuIcon = document.getElementById('menuIcon');
    let scrollPosition = 0;
    
    function toggleNav() {
      const isActive = navLinks.classList.contains('active');
      
      if (isActive) {
        closeNav();
      } else {
        // Save scroll position before locking
        scrollPosition = window.pageYOffset;
        navLinks.classList.add('active');
        mobileToggle.classList.add('active');
        menuIcon.classList.remove('bi-list');
        menuIcon.classList.add('bi-x-lg');
        document.body.classList.add('nav-open');
        document.body.style.top = `-${scrollPosition}px`;
      }
    }
    
    function closeNav() {
      navLinks.classList.remove('active');
      mobileToggle.classList.remove('active');
      menuIcon.classList.add('bi-list');
      menuIcon.classList.remove('bi-x-lg');
      document.body.classList.remove('nav-open');
      document.body.style.top = '';
      window.scrollTo(0, scrollPosition);
    }
    
    // Close nav on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && navLinks.classList.contains('active')) {
        closeNav();
      }
    });
    
    // Close nav when clicking a link
    document.querySelectorAll('.nav-links .nav-link').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 992) {
          closeNav();
        }
      });
    });
    
    // ======================
    // PARALLAX EFFECT ENGINE
    // ======================
    const parallaxLayers = document.querySelectorAll('[data-parallax-speed]');
    const heroVideo = document.querySelector('.hero-video-bg video');
    const heroSection = document.querySelector('.hero-premium');
    
    let ticking = false;
    let lastScrollY = 0;
    let lastMouseX = 0;
    let lastMouseY = 0;
    
    // Scroll-based Parallax
    function updateParallax() {
      const scrollY = window.scrollY;
      const heroRect = heroSection ? heroSection.getBoundingClientRect() : null;
      
      // Only apply parallax when hero is in view
      if (heroRect && heroRect.bottom > 0) {
        parallaxLayers.forEach(layer => {
          const speed = parseFloat(layer.dataset.parallaxSpeed) || 0.05;
          const yOffset = scrollY * speed;
          layer.style.transform = `translateY(${yOffset}px)`;
        });
        
        // Video scale effect on scroll
        if (heroVideo) {
          const scale = 1.1 + (scrollY * 0.0002);
          heroVideo.style.transform = `scale(${Math.min(scale, 1.3)})`;
        }
      }
      
      ticking = false;
    }
    
    // Mouse-based Parallax for extra depth
    function updateMouseParallax(e) {
      if (!heroSection) return;
      
      const heroRect = heroSection.getBoundingClientRect();
      if (heroRect.bottom < 0 || heroRect.top > window.innerHeight) return;
      
      const centerX = window.innerWidth / 2;
      const centerY = window.innerHeight / 2;
      const mouseX = (e.clientX - centerX) / centerX;
      const mouseY = (e.clientY - centerY) / centerY;
      
      parallaxLayers.forEach((layer, index) => {
        const baseSpeed = parseFloat(layer.dataset.parallaxSpeed) || 0.05;
        const multiplier = (index + 1) * 15;
        const xOffset = mouseX * multiplier;
        const yOffset = mouseY * multiplier + (window.scrollY * baseSpeed);
        layer.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
      });
    }
    
    window.addEventListener('scroll', () => {
      lastScrollY = window.scrollY;
      if (!ticking) {
        requestAnimationFrame(updateParallax);
        ticking = true;
      }
    });
    
    document.addEventListener('mousemove', (e) => {
      lastMouseX = e.clientX;
      lastMouseY = e.clientY;
      requestAnimationFrame(() => updateMouseParallax(e));
    });
    
    // Initial parallax state
    updateParallax();
    
    // Scroll Animations with IntersectionObserver
    const scrollElements = document.querySelectorAll('[data-scroll]');
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, {
      threshold: 0.15,
      rootMargin: '0px 0px -50px 0px'
    });
    
    scrollElements.forEach(el => observer.observe(el));
    
    // Magnetic Button Effect
    document.querySelectorAll('.btn-magnetic').forEach(btn => {
      btn.addEventListener('mousemove', (e) => {
        const rect = btn.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        btn.style.setProperty('--mouse-x', x + '%');
        btn.style.setProperty('--mouse-y', y + '%');
      });
    });
    
    // Counter Animation
    function animateCounters() {
      document.querySelectorAll('.stat-number').forEach(counter => {
        const target = counter.innerText.replace(/[^0-9]/g, '');
        const suffix = counter.innerText.replace(/[0-9]/g, '');
        let current = 0;
        const increment = Math.ceil(target / 50);
        const timer = setInterval(() => {
          current += increment;
          if (current >= target) {
            counter.innerText = target + suffix;
            clearInterval(timer);
          } else {
            counter.innerText = current + suffix;
          }
        }, 40);
      });
    }
    
    // Trigger counter animation when stats section is visible
    const statsSection = document.querySelector('.stats-section');
    const statsObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounters();
          statsObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    
    if (statsSection) statsObserver.observe(statsSection);
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
    
    // Hide scroll indicator on scroll
    const scrollIndicator = document.querySelector('.scroll-indicator');
    if (scrollIndicator) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
          scrollIndicator.style.opacity = '0';
          scrollIndicator.style.transform = 'translateX(-50%) translateY(20px)';
        } else {
          scrollIndicator.style.opacity = '1';
          scrollIndicator.style.transform = 'translateX(-50%) translateY(0)';
        }
      });
    }
    
    // ======================
    // LIGHTBOX GALLERY
    // ======================
    const galleryItems = document.querySelectorAll('.gallery-item');
    const lightboxOverlay = document.getElementById('lightboxOverlay');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxTitle = document.getElementById('lightboxTitle');
    const lightboxCurrent = document.getElementById('lightboxCurrent');
    const lightboxTotal = document.getElementById('lightboxTotal');
    const lightboxThumbnails = document.getElementById('lightboxThumbnails');
    const zoomLevelEl = document.getElementById('zoomLevel');
    
    let currentImageIndex = 0;
    let currentZoom = 1;
    let galleryImages = [];
    
    // Collect all gallery images
    galleryItems.forEach((item, index) => {
      const img = item.querySelector('img');
      if (img) {
        galleryImages.push({
          src: img.src,
          title: img.alt || `Image ${index + 1}`
        });
        
        // Click handler for each gallery item
        item.addEventListener('click', () => openLightbox(index));
      }
    });
    
    // Update total count
    if (lightboxTotal) lightboxTotal.textContent = galleryImages.length;
    
    // Build thumbnails
    function buildThumbnails() {
      if (!lightboxThumbnails) return;
      lightboxThumbnails.innerHTML = '';
      galleryImages.forEach((img, idx) => {
        const thumb = document.createElement('div');
        thumb.className = 'lightbox-thumb' + (idx === currentImageIndex ? ' active' : '');
        thumb.innerHTML = `<img src="${img.src}" alt="${img.title}">`;
        thumb.addEventListener('click', () => goToImage(idx));
        lightboxThumbnails.appendChild(thumb);
      });
    }
    
    // Open lightbox
    function openLightbox(index) {
      currentImageIndex = index;
      currentZoom = 1;
      updateLightboxImage();
      buildThumbnails();
      lightboxOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    
    // Close lightbox
    function closeLightbox() {
      lightboxOverlay.classList.remove('active');
      document.body.style.overflow = '';
      resetZoom();
    }
    
    // Navigate
    function navigateLightbox(direction) {
      currentImageIndex += direction;
      if (currentImageIndex < 0) currentImageIndex = galleryImages.length - 1;
      if (currentImageIndex >= galleryImages.length) currentImageIndex = 0;
      resetZoom();
      updateLightboxImage();
      updateThumbnails();
    }
    
    // Go to specific image
    function goToImage(index) {
      currentImageIndex = index;
      resetZoom();
      updateLightboxImage();
      updateThumbnails();
    }
    
    // Update image
    function updateLightboxImage() {
      if (!galleryImages[currentImageIndex]) return;
      const imgData = galleryImages[currentImageIndex];
      lightboxImage.src = imgData.src;
      lightboxImage.alt = imgData.title;
      lightboxTitle.textContent = imgData.title;
      lightboxCurrent.textContent = currentImageIndex + 1;
    }
    
    // Update thumbnails active state
    function updateThumbnails() {
      const thumbs = lightboxThumbnails.querySelectorAll('.lightbox-thumb');
      thumbs.forEach((thumb, idx) => {
        thumb.classList.toggle('active', idx === currentImageIndex);
      });
      
      // Scroll active thumbnail into view
      const activeThumb = lightboxThumbnails.querySelector('.lightbox-thumb.active');
      if (activeThumb) {
        activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
      }
    }
    
    // Zoom functions
    function zoomLightbox(delta) {
      currentZoom = Math.max(0.5, Math.min(3, currentZoom + delta));
      lightboxImage.style.transform = `scale(${currentZoom})`;
      zoomLevelEl.textContent = Math.round(currentZoom * 100) + '%';
    }
    
    function resetZoom() {
      currentZoom = 1;
      lightboxImage.style.transform = 'scale(1)';
      zoomLevelEl.textContent = '100%';
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      if (!lightboxOverlay.classList.contains('active')) return;
      
      switch(e.key) {
        case 'Escape': closeLightbox(); break;
        case 'ArrowLeft': navigateLightbox(-1); break;
        case 'ArrowRight': navigateLightbox(1); break;
        case '+':
        case '=': zoomLightbox(0.25); break;
        case '-': zoomLightbox(-0.25); break;
        case '0': resetZoom(); break;
      }
    });
    
    // Close on overlay click
    lightboxOverlay.addEventListener('click', (e) => {
      if (e.target === lightboxOverlay) closeLightbox();
    });
    
    // Touch/Swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    lightboxOverlay.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    lightboxOverlay.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
      const swipeThreshold = 50;
      const diff = touchStartX - touchEndX;
      
      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          navigateLightbox(1); // Swipe left = next
        } else {
          navigateLightbox(-1); // Swipe right = prev
        }
      }
    }
  </script>
</body>
</html>
