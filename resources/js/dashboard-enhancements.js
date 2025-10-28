// Dashboard Touch-Optimized Enhancements
document.addEventListener('DOMContentLoaded', function() {
    
    // Global Search Functionality
    const searchInput = document.getElementById('global-search');
    const searchResults = document.getElementById('search-results');
    const searchContent = document.getElementById('search-content');
    const voiceSearchBtn = document.getElementById('voice-search');
    
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => performSearch(query), 250);
            } else {
                hideSearchResults();
            }
        });


        
        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                hideSearchResults();
            }
        });
    }
    
    // Voice Search (if supported)
    if (voiceSearchBtn && 'webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'sq-AL'; // Albanian
        
        voiceSearchBtn.addEventListener('click', function() {
            recognition.start();
            voiceSearchBtn.classList.add('text-red-500');
        });
        
        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            searchInput.value = transcript;
            performSearch(transcript);
            voiceSearchBtn.classList.remove('text-red-500');
        };
        
        recognition.onerror = function() {
            voiceSearchBtn.classList.remove('text-red-500');
        };
    } else if (voiceSearchBtn) {
        voiceSearchBtn.style.display = 'none';
    }
    
    // Tab Navigation with Touch Support
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.id.replace('tab-', 'content-');
            
            // Remove active class from all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-gradient-to-br', 'from-blue-500', 'to-blue-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-gray-50', 'hover:bg-gray-100', 'text-gray-700', 'border', 'border-gray-200');
            });
            
            // Add active class to clicked tab
            this.classList.add('active', 'bg-gradient-to-br', 'from-blue-500', 'to-blue-600', 'text-white', 'shadow-md');
            this.classList.remove('bg-gray-50', 'hover:bg-gray-100', 'text-gray-700', 'border', 'border-gray-200');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });
            
            // Show target content
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block');
            }
            
            // Add haptic feedback if available
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        });
    });
    
    // Table/Card View Toggle
    const tableViewBtn = document.getElementById('table-view');
    const cardViewBtn = document.getElementById('card-view');
    const projectsTable = document.getElementById('projects-table');
    const projectsCards = document.getElementById('projects-cards');
    
    if (tableViewBtn && cardViewBtn) {
        tableViewBtn.addEventListener('click', function() {
            // Switch to table view
            projectsTable.classList.remove('hidden');
            projectsCards.classList.add('hidden');
            
            // Update button states
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('bg-gray-200', 'text-gray-600');
            cardViewBtn.classList.add('bg-gray-200', 'text-gray-600');
            cardViewBtn.classList.remove('bg-blue-500', 'text-white');
            
            // Store preference
            localStorage.setItem('projectsView', 'table');
            
            if (navigator.vibrate) navigator.vibrate(50);
        });
        
        cardViewBtn.addEventListener('click', function() {
            // Switch to card view
            projectsCards.classList.remove('hidden');
            projectsTable.classList.add('hidden');
            
            // Update button states
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('bg-gray-200', 'text-gray-600');
            tableViewBtn.classList.add('bg-gray-200', 'text-gray-600');
            tableViewBtn.classList.remove('bg-blue-500', 'text-white');
            
            // Store preference
            localStorage.setItem('projectsView', 'cards');
            
            if (navigator.vibrate) navigator.vibrate(50);
        });
        
        // Load saved preference
        const savedView = localStorage.getItem('projectsView');
        if (savedView === 'cards') {
            cardViewBtn.click();
        }
    }
    
    // Table Sorting
    const sortableHeaders = document.querySelectorAll('[data-sort]');
    let currentSort = { column: null, direction: 'asc' };
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.dataset.sort;
            const tbody = document.getElementById('projects-tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Determine sort direction
            if (currentSort.column === column) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.direction = 'asc';
            }
            currentSort.column = column;
            
            // Sort rows
            rows.sort((a, b) => {
                let aVal, bVal;
                
                switch (column) {
                    case 'name':
                        aVal = a.cells[0].textContent.trim();
                        bVal = b.cells[0].textContent.trim();
                        break;
                    case 'client':
                        aVal = a.cells[1].textContent.trim();
                        bVal = b.cells[1].textContent.trim();
                        break;
                    case 'status':
                        aVal = a.cells[2].textContent.trim();
                        bVal = b.cells[2].textContent.trim();
                        break;
                    case 'date':
                        aVal = a.cells[3].textContent.trim();
                        bVal = b.cells[3].textContent.trim();
                        break;
                }
                
                if (currentSort.direction === 'asc') {
                    return aVal.localeCompare(bVal);
                } else {
                    return bVal.localeCompare(aVal);
                }
            });
            
            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
            
            // Update sort indicators
            sortableHeaders.forEach(h => {
                const icon = h.querySelector('svg');
                icon.classList.remove('text-blue-500');
                icon.classList.add('text-gray-400');
            });
            
            const activeIcon = this.querySelector('svg');
            activeIcon.classList.remove('text-gray-400');
            activeIcon.classList.add('text-blue-500');
            
            if (navigator.vibrate) navigator.vibrate(30);
        });
    });
    
    // Touch Gestures for Cards
    let touchStartX = 0;
    let touchStartY = 0;
    
    document.querySelectorAll('.touch-manipulation').forEach(element => {
        element.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        });
        
        element.addEventListener('touchmove', function(e) {
            if (!touchStartX || !touchStartY) return;
            
            const touchEndX = e.touches[0].clientX;
            const touchEndY = e.touches[0].clientY;
            
            const diffX = touchStartX - touchEndX;
            const diffY = touchStartY - touchEndY;
            
            // Prevent default scrolling for horizontal swipes
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                e.preventDefault();
            }
        });
    });
    
    // Loading States
    function showLoading(element) {
        const loadingSpinner = document.createElement('div');
        loadingSpinner.className = 'loading-spinner flex items-center justify-center p-4';
        loadingSpinner.innerHTML = `
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <span class="ml-2 text-gray-600">Duke ngarkuar...</span>
        `;
        element.appendChild(loadingSpinner);
    }
    
    function hideLoading(element) {
        const spinner = element.querySelector('.loading-spinner');
        if (spinner) spinner.remove();
    }
    
    // Search Function
    async function performSearch(query) {
        if (!searchContent) return;
        
        showLoading(searchContent);
        searchResults.classList.remove('hidden');
        
        try {
            // Simulate API call - replace with actual endpoint
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            hideLoading(searchContent);
            displaySearchResults(data);
        } catch (error) {
            hideLoading(searchContent);
            searchContent.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-red-500">Gabim në kërkim. Provoni përsëri.</p>
                </div>
            `;
        }
    }
    
    function displaySearchResults(results) {
        if (!results || results.length === 0) {
            searchContent.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-gray-500">Nuk u gjetën rezultate.</p>
                </div>
            `;
            return;
        }
        
        const html = results.map(result => `
            <div class="p-3 hover:bg-gray-50 rounded-lg cursor-pointer touch-manipulation" onclick="window.location.href='${result.url}'">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${getIconForType(result.type)}
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${result.title}</p>
                        <p class="text-sm text-gray-500 truncate">${result.description}</p>
                    </div>
                    <div class="text-xs text-gray-400">${result.type}</div>
                </div>
            </div>
        `).join('');
        
        searchContent.innerHTML = html;
    }
    
    function hideSearchResults() {
        if (searchResults) {
            searchResults.classList.add('hidden');
        }
    }
    
    function getIconForType(type) {
        switch (type) {
            case 'projekt':
                return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>';
            case 'klient':
                return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>';
            case 'detyre':
                return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>';
            default:
                return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>';
        }
    }
    
    // Performance optimization: Lazy load images and content
    const observerOptions = {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                element.classList.add('animate-fade-in');
                observer.unobserve(element);
            }
        });
    }, observerOptions);
    
    // Observe all cards and table rows
    document.querySelectorAll('.touch-manipulation').forEach(el => {
        observer.observe(el);
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    
    .touch-manipulation {
        -webkit-tap-highlight-color: transparent;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        user-select: none;
    }
    
    .touch-manipulation:active {
        transform: scale(0.98);
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
`;
document.head.appendChild(style);
