@props(['events' => [], 'currentDate' => null])

@php
$currentDate = $currentDate ?? now();
$startOfMonth = $currentDate->copy()->startOfMonth();
$endOfMonth = $currentDate->copy()->endOfMonth();
$startOfCalendar = $startOfMonth->copy()->startOfWeek();
$endOfCalendar = $endOfMonth->copy()->endOfWeek();
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Calendar Header -->
    <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
        <div class="flex items-center justify-between mb-4">
            <button id="prev-month" class="p-3 rounded-xl hover:bg-white/20 transition-colors touch-manipulation transform active:scale-95">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <div class="text-center">
                <h2 id="calendar-title" class="text-2xl font-bold">{{ $currentDate->format('F Y') }}</h2>
                <p class="text-blue-100 text-sm mt-1">{{ $currentDate->format('l, j M Y') }}</p>
            </div>
            
            <button id="next-month" class="p-3 rounded-xl hover:bg-white/20 transition-colors touch-manipulation transform active:scale-95">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
        
        <!-- View Toggle -->
        <div class="flex justify-center space-x-2">
            <button id="month-view" class="px-4 py-2 bg-white/20 rounded-xl text-sm font-semibold transition-colors touch-manipulation active">Muaj</button>
            <button id="week-view" class="px-4 py-2 hover:bg-white/20 rounded-xl text-sm font-semibold transition-colors touch-manipulation">Javë</button>
            <button id="day-view" class="px-4 py-2 hover:bg-white/20 rounded-xl text-sm font-semibold transition-colors touch-manipulation">Ditë</button>
        </div>
    </div>
    
    <!-- Calendar Grid -->
    <div id="calendar-content" class="p-6">
        <!-- Month View -->
        <div id="month-calendar" class="calendar-view">
            <!-- Days of Week Header -->
            <div class="grid grid-cols-7 gap-1 mb-4">
                @foreach(['Hën', 'Mar', 'Mër', 'Enj', 'Pre', 'Sht', 'Dje'] as $day)
                    <div class="p-3 text-center text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $day }}</div>
                @endforeach
            </div>
            
            <!-- Calendar Days -->
            <div id="calendar-grid" class="grid grid-cols-7 gap-1">
                <!-- Days will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Week View -->
        <div id="week-calendar" class="calendar-view hidden">
            <div class="space-y-2">
                <!-- Week days will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Day View -->
        <div id="day-calendar" class="calendar-view hidden">
            <div class="space-y-4">
                <!-- Day events will be populated by JavaScript -->
            </div>
        </div>
    </div>
    
    <!-- Quick Add Event -->
    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
        <button id="quick-add-event" class="w-full py-4 px-6 bg-blue-500 hover:bg-blue-600 text-white rounded-2xl font-semibold transition-colors touch-manipulation transform active:scale-95 flex items-center justify-center space-x-2">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Shto Ngjarje të Re</span>
        </button>
    </div>
</div>

<!-- Event Modal -->
<div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Shto Ngjarje</h3>
                <button id="close-modal" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors touch-manipulation">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="event-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Titulli</label>
                    <input type="text" name="title" required class="w-full h-12 px-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-blue-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Përshkrimi</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-blue-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Data</label>
                        <input type="date" name="date" required class="w-full h-12 px-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-blue-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ora</label>
                        <input type="time" name="time" class="w-full h-12 px-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-blue-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Lloji</label>
                    <select name="type" class="w-full h-12 px-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-blue-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="meeting">Mbledhje</option>
                        <option value="deadline">Afat</option>
                        <option value="task">Detyrë</option>
                        <option value="reminder">Kujtesë</option>
                    </select>
                </div>
                
                <div class="flex space-x-3 pt-4">
                    <button type="button" id="cancel-event" class="flex-1 py-3 px-4 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors touch-manipulation transform active:scale-95">
                        Anulo
                    </button>
                    <button type="submit" class="flex-1 py-3 px-4 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-semibold transition-colors touch-manipulation transform active:scale-95">
                        Ruaj
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
class CalendarView {
    constructor() {
        this.currentDate = new Date();
        this.currentView = 'month';
        this.events = @json($events);
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.render();
    }
    
    bindEvents() {
        // Navigation
        document.getElementById('prev-month').addEventListener('click', () => {
            this.navigateMonth(-1);
        });
        
        document.getElementById('next-month').addEventListener('click', () => {
            this.navigateMonth(1);
        });
        
        // View toggles
        document.getElementById('month-view').addEventListener('click', () => {
            this.switchView('month');
        });
        
        document.getElementById('week-view').addEventListener('click', () => {
            this.switchView('week');
        });
        
        document.getElementById('day-view').addEventListener('click', () => {
            this.switchView('day');
        });
        
        // Modal events
        document.getElementById('quick-add-event').addEventListener('click', () => {
            this.openModal();
        });
        
        document.getElementById('close-modal').addEventListener('click', () => {
            this.closeModal();
        });
        
        document.getElementById('cancel-event').addEventListener('click', () => {
            this.closeModal();
        });
        
        document.getElementById('event-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveEvent();
        });
        
        // Close modal on backdrop click
        document.getElementById('event-modal').addEventListener('click', (e) => {
            if (e.target.id === 'event-modal') {
                this.closeModal();
            }
        });
    }
    
    navigateMonth(direction) {
        this.currentDate.setMonth(this.currentDate.getMonth() + direction);
        this.render();
        
        if (navigator.vibrate) {
            navigator.vibrate(30);
        }
    }
    
    switchView(view) {
        this.currentView = view;
        
        // Update button states
        document.querySelectorAll('[id$="-view"]').forEach(btn => {
            btn.classList.remove('bg-white/20');
            btn.classList.add('hover:bg-white/20');
        });
        
        document.getElementById(`${view}-view`).classList.add('bg-white/20');
        document.getElementById(`${view}-view`).classList.remove('hover:bg-white/20');
        
        // Show/hide calendar views
        document.querySelectorAll('.calendar-view').forEach(view => {
            view.classList.add('hidden');
        });
        
        document.getElementById(`${view}-calendar`).classList.remove('hidden');
        
        this.render();
        
        if (navigator.vibrate) {
            navigator.vibrate(50);
        }
    }
    
    render() {
        this.updateTitle();
        
        switch (this.currentView) {
            case 'month':
                this.renderMonth();
                break;
            case 'week':
                this.renderWeek();
                break;
            case 'day':
                this.renderDay();
                break;
        }
    }
    
    updateTitle() {
        const title = document.getElementById('calendar-title');
        const options = { year: 'numeric', month: 'long' };
        title.textContent = this.currentDate.toLocaleDateString('sq-AL', options);
    }
    
    renderMonth() {
        const grid = document.getElementById('calendar-grid');
        const startOfMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
        const endOfMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
        const startOfCalendar = new Date(startOfMonth);
        startOfCalendar.setDate(startOfCalendar.getDate() - startOfCalendar.getDay() + 1);
        
        grid.innerHTML = '';
        
        for (let i = 0; i < 42; i++) {
            const date = new Date(startOfCalendar);
            date.setDate(startOfCalendar.getDate() + i);
            
            const dayElement = this.createDayElement(date, startOfMonth, endOfMonth);
            grid.appendChild(dayElement);
        }
    }
    
    createDayElement(date, startOfMonth, endOfMonth) {
        const dayElement = document.createElement('div');
        const isCurrentMonth = date >= startOfMonth && date <= endOfMonth;
        const isToday = this.isToday(date);
        const dayEvents = this.getEventsForDate(date);
        
        dayElement.className = `
            p-2 min-h-[80px] border border-gray-100 dark:border-gray-700 rounded-xl cursor-pointer transition-all hover:bg-blue-50 dark:hover:bg-blue-900/20 touch-manipulation transform active:scale-95
            ${isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900 text-gray-400'}
            ${isToday ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/30' : ''}
        `;
        
        dayElement.innerHTML = `
            <div class="text-sm font-semibold ${isToday ? 'text-blue-600 dark:text-blue-400' : ''}">${date.getDate()}</div>
            <div class="mt-1 space-y-1">
                ${dayEvents.slice(0, 2).map(event => `
                    <div class="text-xs p-1 rounded ${this.getEventColor(event.type)} truncate">
                        ${event.title}
                    </div>
                `).join('')}
                ${dayEvents.length > 2 ? `<div class="text-xs text-gray-500">+${dayEvents.length - 2} më shumë</div>` : ''}
            </div>
        `;
        
        dayElement.addEventListener('click', () => {
            this.selectDate(date);
        });
        
        return dayElement;
    }
    
    getEventsForDate(date) {
        return this.events.filter(event => {
            const eventDate = new Date(event.date);
            return eventDate.toDateString() === date.toDateString();
        });
    }
    
    getEventColor(type) {
        const colors = {
            meeting: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            deadline: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            task: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            reminder: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
        };
        return colors[type] || colors.task;
    }
    
    isToday(date) {
        const today = new Date();
        return date.toDateString() === today.toDateString();
    }
    
    selectDate(date) {
        this.currentDate = new Date(date);
        this.openModal(date);
        
        if (navigator.vibrate) {
            navigator.vibrate(50);
        }
    }
    
    openModal(selectedDate = null) {
        const modal = document.getElementById('event-modal');
        const form = document.getElementById('event-form');
        
        if (selectedDate) {
            const dateInput = form.querySelector('[name="date"]');
            dateInput.value = selectedDate.toISOString().split('T')[0];
        }
        
        modal.classList.remove('hidden');
        
        // Focus first input
        setTimeout(() => {
            form.querySelector('[name="title"]').focus();
        }, 100);
    }
    
    closeModal() {
        const modal = document.getElementById('event-modal');
        const form = document.getElementById('event-form');
        
        modal.classList.add('hidden');
        form.reset();
    }
    
    saveEvent() {
        const form = document.getElementById('event-form');
        const formData = new FormData(form);
        
        const event = {
            id: Date.now(),
            title: formData.get('title'),
            description: formData.get('description'),
            date: formData.get('date'),
            time: formData.get('time'),
            type: formData.get('type')
        };
        
        this.events.push(event);
        this.render();
        this.closeModal();
        
        // Show success notification
        if (window.showSuccess) {
            window.showSuccess('Sukses!', 'Ngjarjea u shtua me sukses.');
        }
        
        if (navigator.vibrate) {
            navigator.vibrate([50, 50, 50]);
        }
    }
    
    renderWeek() {
        // Week view implementation
        const container = document.getElementById('week-calendar');
        container.innerHTML = '<div class="text-center text-gray-500 py-8">Pamja e javës do të implementohet së shpejti</div>';
    }
    
    renderDay() {
        // Day view implementation
        const container = document.getElementById('day-calendar');
        container.innerHTML = '<div class="text-center text-gray-500 py-8">Pamja e ditës do të implementohet së shpejti</div>';
    }
}

// Initialize calendar when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new CalendarView();
});
</script>
