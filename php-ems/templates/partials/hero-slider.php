<?php
/**
 * Hero Banner Slider Partial
 * Include at the top of templates when slider is enabled
 * Requires: $heroSliderEnabled, $activeBanners from data-loader.php
 */

if (!isset($heroSliderEnabled) || !$heroSliderEnabled || empty($activeBanners)) {
    return;
}
?>
<style>
.hero-slider-wrapper {
    position: relative;
    width: 100%;
    overflow: hidden;
    background: #000;
}

.hero-slider {
    display: flex;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-slide {
    min-width: 100%;
    position: relative;
}

.hero-slide img {
    width: 100%;
    height: auto;
    max-height: 70vh;
    object-fit: cover;
    display: block;
}

.hero-slide a {
    display: block;
}

/* Navigation Arrows */
.slider-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.9);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #333;
    transition: all 0.3s;
    z-index: 10;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.slider-nav:hover {
    background: #fff;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 25px rgba(0,0,0,0.25);
}

.slider-nav.prev { left: 20px; }
.slider-nav.next { right: 20px; }

/* Dots */
.slider-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
}

.slider-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    border: 2px solid white;
    cursor: pointer;
    transition: all 0.3s;
}

.slider-dot.active,
.slider-dot:hover {
    background: white;
    transform: scale(1.2);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .slider-nav {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    .slider-nav.prev { left: 10px; }
    .slider-nav.next { right: 10px; }
    
    .slider-dots {
        bottom: 15px;
    }
    
    .slider-dot {
        width: 10px;
        height: 10px;
    }
    
    .hero-slide img {
        max-height: 50vh;
    }
}

@media (max-width: 480px) {
    .slider-nav {
        width: 36px;
        height: 36px;
    }
    .hero-slide img {
        max-height: 40vh;
    }
}
</style>

<div class="hero-slider-wrapper" id="heroSlider">
    <div class="hero-slider" id="heroSliderTrack">
        <?php foreach ($activeBanners as $banner): ?>
        <div class="hero-slide">
            <?php if (!empty($banner['link_url'])): ?>
                <a href="<?= e($banner['link_url']) ?>">
                    <img src="<?= e($banner['image']) ?>" alt="<?= e($banner['alt_text'] ?? 'Banner') ?>" loading="lazy">
                </a>
            <?php else: ?>
                <img src="<?= e($banner['image']) ?>" alt="<?= e($banner['alt_text'] ?? 'Banner') ?>" loading="lazy">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (count($activeBanners) > 1): ?>
    <button class="slider-nav prev" onclick="heroSliderPrev()" aria-label="Previous">
        <i class="bi bi-chevron-left"></i>
    </button>
    <button class="slider-nav next" onclick="heroSliderNext()" aria-label="Next">
        <i class="bi bi-chevron-right"></i>
    </button>
    
    <div class="slider-dots">
        <?php for ($i = 0; $i < count($activeBanners); $i++): ?>
        <div class="slider-dot <?= $i === 0 ? 'active' : '' ?>" onclick="heroSliderGoTo(<?= $i ?>)"></div>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<script>
(function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('#heroSliderTrack .hero-slide');
    const totalSlides = slides.length;
    const track = document.getElementById('heroSliderTrack');
    const dots = document.querySelectorAll('.slider-dot');
    let autoPlayInterval;
    
    function updateSlider() {
        if (track) {
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
        }
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentSlide);
        });
    }
    
    window.heroSliderNext = function() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider();
        resetAutoPlay();
    };
    
    window.heroSliderPrev = function() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlider();
        resetAutoPlay();
    };
    
    window.heroSliderGoTo = function(index) {
        currentSlide = index;
        updateSlider();
        resetAutoPlay();
    };
    
    function startAutoPlay() {
        if (totalSlides > 1) {
            autoPlayInterval = setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }, 5000);
        }
    }
    
    function resetAutoPlay() {
        clearInterval(autoPlayInterval);
        startAutoPlay();
    }
    
    // Touch/Swipe support
    let touchStartX = 0;
    let touchEndX = 0;
    
    const slider = document.getElementById('heroSlider');
    if (slider) {
        slider.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        slider.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    heroSliderNext();
                } else {
                    heroSliderPrev();
                }
            }
        }, { passive: true });
    }
    
    // Start autoplay
    startAutoPlay();
})();
</script>
