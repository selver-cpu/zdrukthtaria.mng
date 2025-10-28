class CalendarView {
    constructor(element) {
        this.container = element;
        this.currentDate = new Date();
        this.currentView = 'month';
        try {
            this.events = JSON.parse(this.container.dataset.events || '[]');
        } catch (e) {
            console.error('Error parsing calendar events:', e);
            this.events = [];
        }
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

document.addEventListener('DOMContentLoaded', function() {
    const calendarElement = document.getElementById('calendar-container');
    if (calendarElement) {
        new CalendarView(calendarElement);
    }
});
