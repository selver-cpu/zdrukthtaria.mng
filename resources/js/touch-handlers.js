// Touch event handlers for improved mobile experience
document.addEventListener('DOMContentLoaded', () => {
    // Enable touch scrolling on tables
    const tables = document.querySelectorAll('.touch-scroll-table');
    tables.forEach(table => {
        let startX;
        let scrollLeft;

        table.addEventListener('touchstart', (e) => {
            startX = e.touches[0].pageX - table.offsetLeft;
            scrollLeft = table.scrollLeft;
        });

        table.addEventListener('touchmove', (e) => {
            if (!startX) return;
            // Only prevent default for horizontal scrolling on tables
            const x = e.touches[0].pageX - table.offsetLeft;
            const walk = (x - startX) * 2;
            table.scrollLeft = scrollLeft - walk;
            e.preventDefault();
        });

        table.addEventListener('touchend', () => {
            startX = null;
        });
    });

    // Swipe handlers for tab navigation
    const tabContainer = document.querySelector('.touch-pan-x');
    if (tabContainer) {
        let touchStartX = 0;
        let touchEndX = 0;
        
        tabContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        tabContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const tabs = Array.from(document.querySelectorAll('.tab-button'));
            const activeTabIndex = tabs.findIndex(tab => tab.classList.contains('active'));
            
            if (touchEndX < touchStartX - swipeThreshold && activeTabIndex < tabs.length - 1) {
                // Swipe left - next tab
                tabs[activeTabIndex + 1].click();
            }
            if (touchEndX > touchStartX + swipeThreshold && activeTabIndex > 0) {
                // Swipe right - previous tab
                tabs[activeTabIndex - 1].click();
            }
        }
    }

    // Add touch ripple effect to buttons
    const buttons = document.querySelectorAll('.touch-ripple');
    buttons.forEach(button => {
        button.addEventListener('touchstart', createRipple);
    });

    function createRipple(event) {
        const button = event.currentTarget;
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        const touch = event.touches[0];
        const rect = button.getBoundingClientRect();
        
        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${touch.clientX - rect.left - radius}px`;
        circle.style.top = `${touch.clientY - rect.top - radius}px`;
        circle.classList.add('ripple');

        const ripple = button.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);
    }

    // Add pull-to-refresh functionality
    let touchStartY = 0;
    let touchEndY = 0;
    const pullThreshold = 100;
    const refreshIndicator = document.createElement('div');
    refreshIndicator.classList.add('refresh-indicator', 'hidden');
    refreshIndicator.innerHTML = `
        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="ml-2">Përditësimi...</span>
    `;
    document.body.appendChild(refreshIndicator);

    // Only enable pull-to-refresh on specific containers, not globally
    const refreshContainer = document.querySelector('.enable-pull-refresh');
    if (refreshContainer) {
        refreshContainer.addEventListener('touchstart', (e) => {
            touchStartY = e.touches[0].screenY;
        }, false);

        refreshContainer.addEventListener('touchend', (e) => {
            touchEndY = e.changedTouches[0].screenY;
            handlePullToRefresh();
        }, false);

        function handlePullToRefresh() {
            if (window.scrollY === 0 && touchEndY > touchStartY + pullThreshold) {
                refreshIndicator.classList.remove('hidden');
                window.location.reload();
            }
        }
    }
});
