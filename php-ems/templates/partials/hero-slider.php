<?php
/**
 * Hero Banner Slider Partial
 * Premium slider with smooth animations, positioned below navbar
 * Requires: $heroSliderEnabled, $activeBanners from data-loader.php
 */

if (!isset($heroSliderEnabled) || !$heroSliderEnabled || empty($activeBanners)) {
    return;
}
?>
<style>
/* ======================
   HERO SLIDER - PREMIUM
====================== */
.hero-slider-wrapper {
    position: relative;
    width: 100%;
    margin-top: 80px; /* Space for fixed navbar */
    overflow: hidden;
    background: #0a0a0a;
}

.hero-slider {
    display: flex;
    width: 100%;
    will-change: transform;
}

.hero-slide {
    min-width: 100%;
    width: 100%;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.hero-slide img {
    width: 100%;
    height: auto;
    min-height: 300px;
    max-height: 70vh;
    object-fit: cover;
    display: block;
    transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.hero-slide:hover img {
    transform: scale(1.02);
}

.hero-slide a {
    display: block;
    width: 100%;
}

/* Slide Transition Animation */
.hero-slider.sliding {
    transition: transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

/* Ken Burns Effect on Active Slide */
@keyframes kenburns {
    0% { transform: scale(1); }
    100% { transform: scale(1.08); }
}

.hero-slide.active img {
    animation: kenburns 8s ease-out forwards;
}

/* Navigation Arrows */
.slider-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: #1a1a2e;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    z-index: 10;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    opacity: 0;
}

.hero-slider-wrapper:hover .slider-nav {
    opacity: 1;
}

.slider-nav:hover {
    background: #fff;
    transform: translateY(-50%) scale(1.15);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}

.slider-nav:active {
    transform: translateY(-50%) scale(0.95);
}

.slider-nav.prev { 
    left: 24px; 
}

.slider-nav.next { 
    right: 24px; 
}

/* Dots Navigation */
.slider-dots {
    position: absolute;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 12px;
    z-index: 10;
    padding: 10px 20px;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 100px;
}

.slider-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.4);
    border: none;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    padding: 0;
}

.slider-dot:hover {
    background: rgba(255, 255, 255, 0.7);
    transform: scale(1.2);
}

.slider-dot.active {
    background: #fff;
    width: 32px;
    border-radius: 100px;
    box-shadow: 0 2px 10px rgba(255, 255, 255, 0.3);
}

/* Progress Bar */
.slider-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(90deg, #0052cc, #ff6b35, #00c853);
    background-size: 200% 100%;
    width: 0%;
    z-index: 11;
    animation: gradient-shift 3s ease infinite;
    transition: width 0.1s linear;
}

@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Mobile Responsive */
@media (max-width: 992px) {
    .hero-slider-wrapper {
        margin-top: 70px;
    }
    
    .slider-nav {
        width: 46px;
        height: 46px;
        font-size: 1.2rem;
        opacity: 1;
    }
    
    .slider-nav.prev { left: 16px; }
    .slider-nav.next { right: 16px; }
}

@media (max-width: 768px) {
    .hero-slider-wrapper {
        margin-top: 65px;
    }
    
    .slider-nav {
        width: 42px;
        height: 42px;
        font-size: 1.1rem;
    }
    
    .slider-nav.prev { left: 12px; }
    .slider-nav.next { right: 12px; }
    
    .slider-dots {
        bottom: 16px;
        padding: 8px 16px;
        gap: 10px;
    }
    
    .slider-dot {
        width: 8px;
        height: 8px;
    }
    
    .slider-dot.active {
        width: 26px;
    }
    
    .hero-slide img {
        min-height: 200px;
        max-height: 50vh;
    }
}

@media (max-width: 480px) {
    .hero-slider-wrapper {
        margin-top: 60px;
    }
    
    .slider-nav {
        width: 38px;
        height: 38px;
        font-size: 1rem;
    }
    
    .slider-nav.prev { left: 8px; }
    .slider-nav.next { right: 8px; }
    
    .hero-slide img {
        min-height: 180px;
        max-height: 45vh;
    }
    
    .slider-dots {
        bottom: 12px;
        padding: 6px 12px;
        gap: 8px;
    }
    
    .slider-dot {
        width: 6px;
        height: 6px;
    }
    
    .slider-dot.active {
        width: 20px;
    }
}

/* Ensure no horizontal overflow */
html, body {
    overflow-x: hidden;
}
</style>

<div class="hero-slider-wrapper" id="heroSlider">
    <div class="hero-slider" id="heroSliderTrack">
        <?php foreach ($activeBanners as $index => $banner): ?>
        <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>">
            <?php if (!empty($banner['link_url'])): ?>
                <a href="<?= e($banner['link_url']) ?>">
                    <img src="<?= e($banner['image']) ?>" alt="<?= e($banner['alt_text'] ?? 'Banner') ?>" loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
                </a>
            <?php else: ?>
                <img src="<?= e($banner['image']) ?>" alt="<?= e($banner['alt_text'] ?? 'Banner') ?>" loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (count($activeBanners) > 1): ?>
    <button class="slider-nav prev" onclick="heroSliderPrev()" aria-label="Previous slide">
        <i class="bi bi-chevron-left"></i>
    </button>
    <button class="slider-nav next" onclick="heroSliderNext()" aria-label="Next slide">
        <i class="bi bi-chevron-right"></i>
    </button>
    
    <div class="slider-dots" role="tablist" aria-label="Slide navigation">
        <?php for ($i = 0; $i < count($activeBanners); $i++): ?>
        <button class="slider-dot <?= $i === 0 ? 'active' : '' ?>" 
                onclick="heroSliderGoTo(<?= $i ?>)" 
                role="tab" 
                aria-label="Go to slide <?= $i + 1 ?>"
                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"></button>
        <?php endfor; ?>
    </div>
    
    <div class="slider-progress" id="sliderProgress"></div>
    <?php endif; ?>
</div>

<script>
(function() {
    'use strict';
    
    let currentSlide = 0;
    const slides = document.querySelectorAll('#heroSliderTrack .hero-slide');
    const totalSlides = slides.length;
    const track = document.getElementById('heroSliderTrack');
    const dots = document.querySelectorAll('.slider-dot');
    const progress = document.getElementById('sliderProgress');
    const sliderWrapper = document.getElementById('heroSlider');
    
    let autoPlayInterval;
    let progressInterval;
    let isPaused = false;
    const autoPlayDuration = 5000; // 5 seconds per slide
    const progressUpdateInterval = 50; // Update progress every 50ms
    
    if (!track || totalSlides === 0) return;
    
    // Initialize
    function init() {
        updateSlider(false);
        startAutoPlay();
        setupPauseOnHover();
        setupTouchSwipe();
        setupKeyboardNav();
    }
    
    // Update slider position with smooth animation
    function updateSlider(animate = true) {
        if (animate) {
            track.classList.add('sliding');
        } else {
            track.classList.remove('sliding');
        }
        
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        // Update dots
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentSlide);
            dot.setAttribute('aria-selected', i === currentSlide ? 'true' : 'false');
        });
        
        // Update active slide class for Ken Burns
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === currentSlide);
        });
        
        // Remove sliding class after animation
        if (animate) {
            setTimeout(() => {
                track.classList.remove('sliding');
            }, 700);
        }
    }
    
    // Navigation functions
    window.heroSliderNext = function() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider(true);
        resetAutoPlay();
    };
    
    window.heroSliderPrev = function() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlider(true);
        resetAutoPlay();
    };
    
    window.heroSliderGoTo = function(index) {
        if (index === currentSlide) return;
        currentSlide = index;
        updateSlider(true);
        resetAutoPlay();
    };
    
    // Progress bar animation
    function animateProgress() {
        if (!progress || totalSlides <= 1) return;
        
        let progressValue = 0;
        const increment = 100 / (autoPlayDuration / progressUpdateInterval);
        
        clearInterval(progressInterval);
        progress.style.width = '0%';
        
        progressInterval = setInterval(() => {
            if (isPaused) return;
            
            progressValue += increment;
            progress.style.width = progressValue + '%';
            
            if (progressValue >= 100) {
                clearInterval(progressInterval);
            }
        }, progressUpdateInterval);
    }
    
    // Auto-play functions
    function startAutoPlay() {
        if (totalSlides <= 1) return;
        
        animateProgress();
        
        autoPlayInterval = setInterval(() => {
            if (!isPaused) {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider(true);
                animateProgress();
            }
        }, autoPlayDuration);
    }
    
    function resetAutoPlay() {
        clearInterval(autoPlayInterval);
        clearInterval(progressInterval);
        startAutoPlay();
    }
    
    // Pause on hover
    function setupPauseOnHover() {
        if (!sliderWrapper) return;
        
        sliderWrapper.addEventListener('mouseenter', () => {
            isPaused = true;
        });
        
        sliderWrapper.addEventListener('mouseleave', () => {
            isPaused = false;
        });
    }
    
    // Touch/Swipe support
    function setupTouchSwipe() {
        if (!sliderWrapper) return;
        
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let isSwiping = false;
        
        sliderWrapper.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
            isSwiping = true;
        }, { passive: true });
        
        sliderWrapper.addEventListener('touchmove', e => {
            if (!isSwiping) return;
            
            const diffX = Math.abs(e.changedTouches[0].screenX - touchStartX);
            const diffY = Math.abs(e.changedTouches[0].screenY - touchStartY);
            
            // If horizontal swipe is dominant, prevent vertical scroll
            if (diffX > diffY && diffX > 10) {
                e.preventDefault();
            }
        }, { passive: false });
        
        sliderWrapper.addEventListener('touchend', e => {
            if (!isSwiping) return;
            
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    heroSliderNext();
                } else {
                    heroSliderPrev();
                }
            }
            
            isSwiping = false;
        }, { passive: true });
    }
    
    // Keyboard navigation
    function setupKeyboardNav() {
        document.addEventListener('keydown', e => {
            // Only if slider is in viewport
            const rect = sliderWrapper.getBoundingClientRect();
            const inViewport = rect.top < window.innerHeight && rect.bottom > 0;
            
            if (!inViewport) return;
            
            if (e.key === 'ArrowLeft') {
                heroSliderPrev();
            } else if (e.key === 'ArrowRight') {
                heroSliderNext();
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
