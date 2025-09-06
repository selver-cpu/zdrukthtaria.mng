/**
 * Touch UI Module for ColiDecor
 * 
 * Ky modul ofron përmirësime të ndërfaqes së përdoruesit për pajisjet me ekran me prekje,
 * duke optimizuar elementët e UI për përdorim më të lehtë me gishta.
 */

class TouchUI {
    constructor(options = {}) {
        this.options = {
            mobileBreakpoint: 768,
            largeButtonMinHeight: 48,
            largeButtonMinWidth: 48,
            largeButtonPadding: '12px 16px',
            largeInputMinHeight: 48,
            largeInputPadding: '12px',
            largeInputFontSize: '16px',
            ...options
        };
        
        this.isMobile = window.innerWidth < this.options.mobileBreakpoint;
        this.init();
    }
    
    init() {
        console.log('TouchUI initialized');
        this.setupResponsiveUI();
        this.setupTouchableElements();
        this.setupSwipeableElements();
        this.setupTouchForms();
        this.setupTouchTables();
        this.setupTouchDialogs();
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Përditëso gjendjen mobile kur ndryshon madhësia e dritares
        window.addEventListener('resize', () => {
            this.isMobile = window.innerWidth < this.options.mobileBreakpoint;
            this.setupResponsiveUI();
        });
        
        // Dëgjo për ndryshime në DOM për të aplikuar stilet në elementët e rinj
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    this.setupTouchableElements();
                    this.setupTouchForms();
                }
            });
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
    }
    
    setupResponsiveUI() {
        // Apliko klasa të ndryshme bazuar në madhësinë e ekranit
        document.body.classList.toggle('touch-mobile-view', this.isMobile);
        
        // Përshtat madhësinë e butonave dhe inputeve në mobile
        if (this.isMobile) {
            this.enlargeTouchTargets();
        } else {
            this.resetTouchTargets();
        }
    }
    
    enlargeTouchTargets() {
        // Bëj butonat dhe inputet më të mëdha në mobile
        document.querySelectorAll('button, .btn, input[type="button"], input[type="submit"]').forEach(button => {
            button.classList.add('touch-button');
            button.style.minHeight = this.options.largeButtonMinHeight + 'px';
            button.style.minWidth = this.options.largeButtonMinWidth + 'px';
            button.style.padding = this.options.largeButtonPadding;
        });
        
        document.querySelectorAll('input:not([type="button"]):not([type="submit"]), select, textarea').forEach(input => {
            input.classList.add('touch-input');
            input.style.minHeight = this.options.largeInputMinHeight + 'px';
            input.style.padding = this.options.largeInputPadding;
            input.style.fontSize = this.options.largeInputFontSize;
        });
    }
    
    resetTouchTargets() {
        // Rivendos stilet e butonave dhe inputeve në desktop
        document.querySelectorAll('.touch-button').forEach(button => {
            button.style.minHeight = '';
            button.style.minWidth = '';
            button.style.padding = '';
        });
        
        document.querySelectorAll('.touch-input').forEach(input => {
            input.style.minHeight = '';
            input.style.padding = '';
            input.style.fontSize = '';
        });
    }
    
    setupTouchableElements() {
        // Bëj elementët e ndryshëm më të lehtë për t'u prekur
        const touchableElements = document.querySelectorAll('a, button, .btn, .nav-link, .dropdown-item, .card-header');
        
        touchableElements.forEach(element => {
            if (!element.classList.contains('touch-setup')) {
                element.classList.add('touch-target', 'touch-ripple', 'touch-setup');
                
                // Shto feedback vizual për prekje
                element.addEventListener('touchstart', () => {
                    element.classList.add('touch-active');
                });
                
                element.addEventListener('touchend', () => {
                    element.classList.remove('touch-active');
                });
            }
        });
    }
    
    setupSwipeableElements() {
        // Identifiko elementët që mund të përdorin swipe gestures
        
        // Swipe për kartat e projekteve
        document.querySelectorAll('.project-card').forEach(card => {
            this.makeSwipeable(card, {
                swipeLeft: () => {
                    // Shfaq butonat e aksioneve
                    const actions = card.querySelector('.card-actions');
                    if (actions) {
                        actions.classList.add('show');
                    }
                },
                swipeRight: () => {
                    // Fshih butonat e aksioneve
                    const actions = card.querySelector('.card-actions');
                    if (actions) {
                        actions.classList.remove('show');
                    }
                }
            });
        });
        
        // Swipe për tabelat e projekteve
        document.querySelectorAll('.table-row-swipeable').forEach(row => {
            this.makeSwipeable(row, {
                swipeLeft: () => {
                    // Shfaq butonat e aksioneve
                    const actions = row.querySelector('.row-actions');
                    if (actions) {
                        actions.classList.add('show');
                    }
                },
                swipeRight: () => {
                    // Fshih butonat e aksioneve
                    const actions = row.querySelector('.row-actions');
                    if (actions) {
                        actions.classList.remove('show');
                    }
                }
            });
        });
    }
    
    makeSwipeable(element, callbacks) {
        let touchStartX = 0;
        let touchEndX = 0;
        
        element.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        element.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
        
        const handleSwipe = () => {
            const swipeThreshold = 50;
            const deltaX = touchEndX - touchStartX;
            
            if (Math.abs(deltaX) > swipeThreshold) {
                if (deltaX < 0 && callbacks.swipeLeft) {
                    // Swipe left
                    callbacks.swipeLeft();
                } else if (deltaX > 0 && callbacks.swipeRight) {
                    // Swipe right
                    callbacks.swipeRight();
                }
            }
        };
    }
    
    setupTouchForms() {
        // Përmirëso formularët për ekranet me prekje
        document.querySelectorAll('form').forEach(form => {
            // Shto klasa për elementët e formularit
            form.querySelectorAll('input, select, textarea').forEach(input => {
                input.classList.add('touch-input');
            });
            
            // Bëj butonat më të mëdhenj
            form.querySelectorAll('button, [type="submit"], [type="button"]').forEach(button => {
                button.classList.add('touch-button');
            });
            
            // Shto hapësirë më të madhe midis elementëve të formularit në mobile
            if (this.isMobile) {
                form.querySelectorAll('.form-group, .mb-3').forEach(group => {
                    group.style.marginBottom = '1.5rem';
                });
            }
        });
    }
    
    setupTouchTables() {
        // Përmirëso tabelat për ekranet me prekje
        document.querySelectorAll('table').forEach(table => {
            // Bëj tabelën të rrëshqitshme horizontalisht nëse nuk është tashmë
            if (!table.parentElement.classList.contains('table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('table-responsive', 'touch-scroll');
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
            
            // Shto klasa për rreshtat e tabelës
            table.querySelectorAll('tbody tr').forEach(row => {
                row.classList.add('touch-target');
                
                // Shto efekt feedback për prekje
                row.addEventListener('touchstart', () => {
                    row.classList.add('touch-active');
                });
                
                row.addEventListener('touchend', () => {
                    row.classList.remove('touch-active');
                    setTimeout(() => row.classList.add('touch-feedback-animation'), 0);
                    setTimeout(() => row.classList.remove('touch-feedback-animation'), 300);
                });
            });
        });
    }
    
    setupTouchDialogs() {
        // Përmirëso dialogët dhe modalet për ekranet me prekje
        document.querySelectorAll('.modal').forEach(modal => {
            // Shto klasa për modalet
            modal.classList.add('touch-modal');
            
            // Bëj butonat në modal më të mëdhenj
            modal.querySelectorAll('button, .btn').forEach(button => {
                button.classList.add('touch-button');
            });
            
            // Shto mbyllje me swipe për modalet në mobile
            if (this.isMobile) {
                const modalDialog = modal.querySelector('.modal-dialog');
                if (modalDialog) {
                    let touchStartY = 0;
                    let touchEndY = 0;
                    
                    modalDialog.addEventListener('touchstart', (e) => {
                        touchStartY = e.changedTouches[0].screenY;
                    }, false);
                    
                    modalDialog.addEventListener('touchend', (e) => {
                        touchEndY = e.changedTouches[0].screenY;
                        
                        // Nëse përdoruesi bën swipe poshtë, mbyll modalin
                        if (touchEndY - touchStartY > 100) {
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }
                    }, false);
                }
            }
        });
    }
}

// Inicializimi i modulit kur dokumenti është gati
document.addEventListener('DOMContentLoaded', () => {
    window.touchUI = new TouchUI();
});

export default TouchUI;
