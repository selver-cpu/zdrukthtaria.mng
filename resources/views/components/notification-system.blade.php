@props(['notifications' => []])

<!-- Notification Container -->
<div id="notification-container" class="fixed top-24 right-4 z-[9998] space-y-3 max-w-sm">
    <!-- Notifications will be dynamically added here -->
</div>

<!-- Notification Template (Hidden) -->
<template id="notification-template">
    <div class="notification-item bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg p-4 transform translate-x-full transition-all duration-300 touch-manipulation">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <div class="notification-icon w-10 h-10 rounded-full flex items-center justify-center">
                    <!-- Icon will be set dynamically -->
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="notification-title text-sm font-semibold text-gray-900 dark:text-white"></p>
                <p class="notification-message text-sm text-gray-600 dark:text-gray-300 mt-1"></p>
                <p class="notification-time text-xs text-gray-400 dark:text-gray-500 mt-2"></p>
            </div>
            <button class="notification-close flex-shrink-0 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors touch-manipulation">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="notification-progress w-full h-1 bg-gray-200 dark:bg-gray-700 rounded-full mt-3 overflow-hidden">
            <div class="notification-progress-bar h-full bg-blue-500 rounded-full transition-all duration-300" style="width: 100%"></div>
        </div>
    </div>
</template>

<script>
class NotificationSystem {
    constructor() {
        this.container = document.getElementById('notification-container');
        this.template = document.getElementById('notification-template');
        this.notifications = new Map();
        this.init();
    }
    
    init() {
        // Listen for custom notification events
        document.addEventListener('show-notification', (e) => {
            this.show(e.detail);
        });
        
        // Auto-hide notifications after 5 seconds
        this.autoHideTimer = null;
    }
    
    show(options = {}) {
        const {
            id = Date.now(),
            type = 'info',
            title = '',
            message = '',
            duration = 5000,
            persistent = false,
            action = null
        } = options;
        
        // Create notification element
        const notification = this.createNotification(id, type, title, message, action);
        
        // Add to container
        this.container.appendChild(notification);
        this.notifications.set(id, notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto-hide if not persistent
        if (!persistent && duration > 0) {
            this.startProgressBar(notification, duration);
            setTimeout(() => {
                this.hide(id);
            }, duration);
        }
        
        // Add haptic feedback
        if (navigator.vibrate) {
            const vibrationPattern = type === 'error' ? [100, 50, 100] : [50];
            navigator.vibrate(vibrationPattern);
        }
        
        return id;
    }
    
    createNotification(id, type, title, message, action) {
        const template = this.template.content.cloneNode(true);
        const notification = template.querySelector('.notification-item');
        
        notification.dataset.id = id;
        notification.dataset.type = type;
        
        // Set icon and colors based on type
        const iconContainer = notification.querySelector('.notification-icon');
        const icon = this.getIcon(type);
        const colors = this.getColors(type);
        
        iconContainer.className += ` ${colors.bg}`;
        iconContainer.innerHTML = `<svg class="h-6 w-6 ${colors.text}" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>`;
        
        // Set content
        notification.querySelector('.notification-title').textContent = title;
        notification.querySelector('.notification-message').textContent = message;
        notification.querySelector('.notification-time').textContent = this.formatTime(new Date());
        
        // Add close handler
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.hide(id);
        });
        
        // Add action button if provided
        if (action) {
            const actionBtn = document.createElement('button');
            actionBtn.className = 'mt-3 w-full py-2 px-4 bg-blue-500 text-white rounded-xl text-sm font-semibold hover:bg-blue-600 transition-colors touch-manipulation transform active:scale-95';
            actionBtn.textContent = action.label;
            actionBtn.addEventListener('click', () => {
                action.handler();
                this.hide(id);
            });
            notification.appendChild(actionBtn);
        }
        
        // Add swipe to dismiss
        this.addSwipeHandler(notification, id);
        
        return notification;
    }
    
    hide(id) {
        const notification = this.notifications.get(id);
        if (!notification) return;
        
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications.delete(id);
        }, 300);
    }
    
    hideAll() {
        this.notifications.forEach((notification, id) => {
            this.hide(id);
        });
    }
    
    startProgressBar(notification, duration) {
        const progressBar = notification.querySelector('.notification-progress-bar');
        progressBar.style.transition = `width ${duration}ms linear`;
        progressBar.style.width = '0%';
    }
    
    addSwipeHandler(notification, id) {
        let startX = 0;
        let currentX = 0;
        let isDragging = false;
        
        notification.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
            notification.style.transition = 'none';
        });
        
        notification.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            
            currentX = e.touches[0].clientX;
            const diffX = currentX - startX;
            
            if (diffX > 0) {
                notification.style.transform = `translateX(${diffX}px)`;
                notification.style.opacity = Math.max(0.3, 1 - (diffX / 200));
            }
        });
        
        notification.addEventListener('touchend', () => {
            if (!isDragging) return;
            
            isDragging = false;
            notification.style.transition = 'all 0.3s ease';
            
            const diffX = currentX - startX;
            
            if (diffX > 100) {
                this.hide(id);
            } else {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }
        });
    }
    
    getIcon(type) {
        const icons = {
            success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>',
            info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            project: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>'
        };
        return icons[type] || icons.info;
    }
    
    getColors(type) {
        const colors = {
            success: { bg: 'bg-green-100 dark:bg-green-900', text: 'text-green-600 dark:text-green-400' },
            error: { bg: 'bg-red-100 dark:bg-red-900', text: 'text-red-600 dark:text-red-400' },
            warning: { bg: 'bg-yellow-100 dark:bg-yellow-900', text: 'text-yellow-600 dark:text-yellow-400' },
            info: { bg: 'bg-blue-100 dark:bg-blue-900', text: 'text-blue-600 dark:text-blue-400' },
            project: { bg: 'bg-purple-100 dark:bg-purple-900', text: 'text-purple-600 dark:text-purple-400' }
        };
        return colors[type] || colors.info;
    }
    
    formatTime(date) {
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Tani';
        if (diff < 3600000) return `${Math.floor(diff / 60000)} min më parë`;
        if (diff < 86400000) return `${Math.floor(diff / 3600000)} orë më parë`;
        return date.toLocaleDateString('sq-AL');
    }
}

// Initialize notification system
document.addEventListener('DOMContentLoaded', function() {
    window.notificationSystem = new NotificationSystem();
    
    // Helper functions for easy use
    window.showNotification = function(options) {
        return window.notificationSystem.show(options);
    };
    
    window.showSuccess = function(title, message, options = {}) {
        return window.showNotification({ ...options, type: 'success', title, message });
    };
    
    window.showError = function(title, message, options = {}) {
        return window.showNotification({ ...options, type: 'error', title, message });
    };
    
    window.showWarning = function(title, message, options = {}) {
        return window.showNotification({ ...options, type: 'warning', title, message });
    };
    
    window.showInfo = function(title, message, options = {}) {
        return window.showNotification({ ...options, type: 'info', title, message });
    };
    
    // Example usage for testing
    // setTimeout(() => {
    //     showSuccess('Sukses!', 'Projekti u ruajt me sukses.');
    //     showInfo('Informacion', 'Keni 3 detyra të reja.');
    //     showWarning('Paralajmërim', 'Afati i projektit po afrohet.');
    // }, 2000);
});
</script>
