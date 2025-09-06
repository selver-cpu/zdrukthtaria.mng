/**
 * Touch Handler Module for ColiDecor
 * 
 * Ky modul ofron mbështetje për ekranet me prekje në aplikacionin ColiDecor,
 * duke përmirësuar përvojën e përdoruesit në pajisjet me ekran me prekje.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Kontrollo nëse jemi në një pajisje me prekje
    const isTouchDevice = ('ontouchstart' in window) || 
                         (navigator.maxTouchPoints > 0) || 
                         (navigator.msMaxTouchPoints > 0);
    
    if (isTouchDevice) {
        console.log('Touch device detected - enabling touch optimizations');
        document.body.classList.add('touch-device');
        initTouchOptimizations();
    }
    
    function initTouchOptimizations() {
        optimizeFormElements();
        optimizeButtons();
        optimizeTables();
        setupSwipeNavigation();
        setupTouchFeedback();
    }
    
    function optimizeFormElements() {
        // Optimizo formularët për prekje
        document.querySelectorAll('input, select, textarea').forEach(el => {
            if (!el.classList.contains('touch-optimized')) {
                el.classList.add('touch-optimized', 'touch-input');
            }
        });
    }
    
    function optimizeButtons() {
        // Optimizo butonat për prekje
        document.querySelectorAll('button, .btn, [type="submit"], [type="button"]').forEach(el => {
            if (!el.classList.contains('touch-optimized')) {
                el.classList.add('touch-optimized', 'touch-button');
            }
        });
    }
    
    function optimizeTables() {
        // Optimizo tabelat për prekje
        document.querySelectorAll('table').forEach(table => {
            if (!table.classList.contains('touch-optimized')) {
                table.classList.add('touch-optimized');
                
                // Bëj tabelën të rrëshqitshme horizontalisht
                if (!table.parentElement.classList.contains('table-responsive')) {
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('table-responsive', 'touch-scroll');
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            }
        });
    }
    
    function setupSwipeNavigation() {
        let touchStartX = 0;
        let touchEndX = 0;
        const swipeThreshold = 50;
        
        document.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        document.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
        
        function handleSwipe() {
            const deltaX = touchEndX - touchStartX;
            
            if (Math.abs(deltaX) > swipeThreshold) {
                if (deltaX > 0) {
                    // Swipe right - back navigation
                    const backButton = document.querySelector('.back-button, .btn-back, [data-action="back"]');
                    if (backButton) {
                        backButton.click();
                    } else if (window.history.length > 1) {
                        window.history.back();
                    }
                } else {
                    // Swipe left - forward navigation
                    const nextButton = document.querySelector('.next-button, .btn-next, [data-action="next"]');
                    if (nextButton) {
                        nextButton.click();
                    }
                }
            }
        }
    }
    
    function setupTouchFeedback() {
        // Shto feedback vizual për elementët e klikueshëm
        document.querySelectorAll('a, button, .btn, .nav-link, .dropdown-item').forEach(el => {
            if (!el.classList.contains('touch-feedback-setup')) {
                el.classList.add('touch-feedback-setup', 'touch-ripple');
                
                el.addEventListener('touchstart', function() {
                    this.classList.add('touch-active');
                });
                
                el.addEventListener('touchend', function() {
                    this.classList.remove('touch-active');
                    setTimeout(() => this.classList.add('touch-feedback-animation'), 0);
                    setTimeout(() => this.classList.remove('touch-feedback-animation'), 300);
                });
            }
        });
    }
});
