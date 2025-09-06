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

<!-- Calendar JavaScript moved to external file -->
<script src="{{ asset('js/calendar-view.js') }}"></script>
