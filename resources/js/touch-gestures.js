/**
 * Touch Gestures Module for ColiDecor
 * 
 * Ky modul ofron mbështetje për gjestet e prekjes në aplikacionin ColiDecor,
 * duke përmirësuar përvojën e përdoruesit në pajisjet me ekran me prekje.
 */

class TouchGesturesHandler {
    constructor(options = {}) {
        this.options = {
            swipeThreshold: 50,
            tapTimeout: 200,
            doubleTapTimeout: 300,
            longPressTimeout: 500,
            ...options
        };
        
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.touchEndX = 0;
        this.touchEndY = 0;
        this.lastTapTime = 0;
        this.longPressTimer = null;
        
        this.init();
    }
    
    init() {
        console.log('TouchGesturesHandler initialized');
        this.setupEventListeners();
        this.setupTouchFeedback();
        this.setupTouchNavigation();
        this.setupTouchForms();
        this.setupTouchTables();
    }
    
    setupEventListeners() {
        document.addEventListener('touchstart', this.handleTouchStart.bind(this), false);
        document.addEventListener('touchend', this.handleTouchEnd.bind(this), false);
        document.addEventListener('touchmove', this.handleTouchMove.bind(this), false);
    }
    
    handleTouchStart(event) {
        this.touchStartX = event.changedTouches[0].screenX;
        this.touchStartY = event.changedTouches[0].screenY;
        
        // Kontrollo për long press
        this.longPressTimer = setTimeout(() => {
            this.triggerEvent('longpress', event.target, {
                x: this.touchStartX,
                y: this.touchStartY
            });
        }, this.options.longPressTimeout);
    }
    
    handleTouchEnd(event) {
        clearTimeout(this.longPressTimer);
        
        this.touchEndX = event.changedTouches[0].screenX;
        this.touchEndY = event.changedTouches[0].screenY;
        
        // Kontrollo për tap
        if (Math.abs(this.touchEndX - this.touchStartX) < 10 && 
            Math.abs(this.touchEndY - this.touchStartY) < 10) {
            
            const currentTime = new Date().getTime();
            const tapTimeDiff = currentTime - this.lastTapTime;
            
            if (tapTimeDiff < this.options.doubleTapTimeout && tapTimeDiff > 0) {
                this.triggerEvent('doubletap', event.target, {
                    x: this.touchEndX,
                    y: this.touchEndY
                });
                this.lastTapTime = 0; // Reset për të shmangur triple tap
            } else {
                this.lastTapTime = currentTime;
                
                // Përdor setTimeout për të dalluar tap nga doubletap
                setTimeout(() => {
                    if (this.lastTapTime === currentTime) {
                        this.triggerEvent('tap', event.target, {
                            x: this.touchEndX,
                            y: this.touchEndY
                        });
                    }
                }, this.options.doubleTapTimeout);
            }
        }
        
        // Kontrollo për swipe
        this.handleSwipe(event);
    }
    
    handleTouchMove(event) {
        // Anulo long press nëse përdoruesi lëviz gishtin
        clearTimeout(this.longPressTimer);
    }
    
    handleSwipe(event) {
        const deltaX = this.touchEndX - this.touchStartX;
        const deltaY = this.touchEndY - this.touchStartY;
        
        // Kontrollo nëse lëvizja është mjaftueshëm e madhe për t'u konsideruar swipe
        if (Math.abs(deltaX) > this.options.swipeThreshold || 
            Math.abs(deltaY) > this.options.swipeThreshold) {
            
            // Përcakto drejtimin e swipe
            if (Math.abs(deltaX) > Math.abs(deltaY)) {
                // Horizontal swipe
                if (deltaX > 0) {
                    this.triggerEvent('swiperight', event.target, { deltaX, deltaY });
                } else {
                    this.triggerEvent('swipeleft', event.target, { deltaX, deltaY });
                }
            } else {
                // Vertical swipe
                if (deltaY > 0) {
                    this.triggerEvent('swipedown', event.target, { deltaX, deltaY });
                } else {
                    this.triggerEvent('swipeup', event.target, { deltaX, deltaY });
                }
            }
        }
    }
    
    triggerEvent(eventName, element, data) {
        console.log(`Touch event: ${eventName}`, data);
        
        // Krijo një event të ri custom
        const event = new CustomEvent(`touch:${eventName}`, {
            bubbles: true,
            cancelable: true,
            detail: data
        });
        
        // Lësho eventin në element
        element.dispatchEvent(event);
    }
    
    setupTouchFeedback() {
        // Shto efektin e feedback-ut vizual për elementët me klasën .touch-feedback
        document.querySelectorAll('.touch-feedback').forEach(element => {
            element.addEventListener('touchstart', () => {
                element.classList.add('touch-active');
            });
            
            element.addEventListener('touchend', () => {
                element.classList.remove('touch-active');
                setTimeout(() => element.classList.add('touch-feedback-animation'), 0);
                setTimeout(() => element.classList.remove('touch-feedback-animation'), 300);
            });
        });
    }
    
    setupTouchNavigation() {
        // Përmirëso navigimin për ekranet me prekje
        const navItems = document.querySelectorAll('.nav-link, .dropdown-item, .sidebar-link');
        navItems.forEach(item => {
            item.classList.add('touch-target', 'touch-no-select');
        });
    }
    
    setupTouchForms() {
        // Përmirëso formularët për ekranet me prekje
        const formControls = document.querySelectorAll('input, select, textarea, button');
        formControls.forEach(control => {
            if (!control.classList.contains('touch-input') && 
                !control.classList.contains('touch-button')) {
                
                if (control.tagName === 'BUTTON' || control.type === 'submit' || control.type === 'button') {
                    control.classList.add('touch-button', 'touch-target');
                } else {
                    control.classList.add('touch-input');
                }
            }
        });
    }
    
    setupTouchTables() {
        // Përmirëso tabelat për ekranet me prekje
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            // Bëj tabelën të rrëshqitshme horizontalisht
            if (!table.parentElement.classList.contains('table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('table-responsive', 'touch-scroll');
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
            
            // Bëj rreshtat e tabelës më të lehtë për t'u prekur
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.classList.add('touch-target');
                row.style.minHeight = '44px';
            });
        });
    }
}

// Inicializimi i modulit kur dokumenti është gati
document.addEventListener('DOMContentLoaded', () => {
    window.touchGestures = new TouchGesturesHandler();
    
    // Shembull i përdorimit të eventeve të prekjes
    document.addEventListener('touch:swipeleft', (e) => {
        // Kontrollo nëse jemi në një faqe projekti dhe ka një sidebar
        const sidebar = document.querySelector('.sidebar');
        if (sidebar && window.innerWidth < 768) {
            sidebar.classList.remove('show');
        }
    });
    
    document.addEventListener('touch:swiperight', (e) => {
        // Kontrollo nëse jemi në një faqe projekti dhe ka një sidebar
        const sidebar = document.querySelector('.sidebar');
        if (sidebar && window.innerWidth < 768) {
            sidebar.classList.add('show');
        }
    });
});

export default TouchGesturesHandler;
