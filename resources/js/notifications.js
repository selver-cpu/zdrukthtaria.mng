document.addEventListener('DOMContentLoaded', () => {
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    
    if (userId && window.Echo) {
        // Listen for new notifications
        window.Echo.private(`njoftimet.${userId}`)
            .listen('NjoftimIRi', (e) => {
                // Update notification count
                const countBadge = document.querySelector('.notification-count');
                if (countBadge) {
                    const currentCount = parseInt(countBadge.textContent || '0');
                    countBadge.textContent = currentCount + 1;
                    countBadge.classList.remove('hidden');
                }

                // Add new notification to dropdown
                const notificationsList = document.querySelector('.notifications-list');
                if (notificationsList) {
                    const template = `
                        <a href="${e.link || '#'}" 
                           class="block px-4 py-3 hover:bg-gray-50 transition ease-in-out duration-150 bg-blue-50">
                            <div class="flex items-center">
                                <span class="flex h-2 w-2 relative mr-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 truncate">
                                        ${e.mesazhi}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        ${e.data_krijimit}
                                    </p>
                                </div>
                            </div>
                        </a>
                    `;
                    
                    // Add new notification at the top
                    notificationsList.insertAdjacentHTML('afterbegin', template);
                    
                    // Remove oldest notification if we have more than 5
                    const notifications = notificationsList.children;
                    if (notifications.length > 5) {
                        notifications[notifications.length - 1].remove();
                    }
                }

                // Show notification toast
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-sm w-full transform transition-all duration-300 translate-y-full';
                toast.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900">${e.mesazhi}</p>
                            <p class="mt-1 text-sm text-gray-500">${e.data_krijimit}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Mbyll</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                document.body.appendChild(toast);
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-y-full');
                });

                // Remove toast after 5 seconds
                setTimeout(() => {
                    toast.classList.add('translate-y-full');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);

                // Handle close button click
                toast.querySelector('button').addEventListener('click', () => {
                    toast.classList.add('translate-y-full');
                    setTimeout(() => toast.remove(), 300);
                });
            });
    }
});
