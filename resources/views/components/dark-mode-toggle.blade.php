@props(['position' => 'fixed'])

<div class="{{ $position === 'fixed' ? '' : 'relative' }}">
    <button id="dark-mode-toggle" 
            class="p-3 bg-white dark:bg-gray-800 rounded-full shadow-lg border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-xl h-12 w-12 flex items-center justify-center">
        <!-- Sun Icon (Light Mode) -->
        <svg id="sun-icon" class="h-6 w-6 text-yellow-500 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        
        <!-- Moon Icon (Dark Mode) -->
        <svg id="moon-icon" class="h-6 w-6 text-blue-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const sunIcon = document.getElementById('sun-icon');
    const moonIcon = document.getElementById('moon-icon');
    
    if (!darkModeToggle || !sunIcon || !moonIcon) {
        console.error('Dark mode elements not found');
        return;
    }
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    console.log('Current theme:', currentTheme);
    
    // Apply the current theme
    if (currentTheme === 'dark') {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
        sunIcon.classList.add('hidden');
        moonIcon.classList.remove('hidden');
    } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
        sunIcon.classList.remove('hidden');
        moonIcon.classList.add('hidden');
    }
    
    // Toggle theme on button click
    darkModeToggle.addEventListener('click', function() {
        console.log('Dark mode toggle clicked');
        const isDark = document.documentElement.classList.contains('dark');
        console.log('Is currently dark:', isDark);
        
        if (isDark) {
            // Switch to light mode
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
            console.log('Switched to light mode');
        } else {
            // Switch to dark mode
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
            console.log('Switched to dark mode');
        }
        
        // Add haptic feedback if available
        if (navigator.vibrate) {
            navigator.vibrate(50);
        }
        
        // Animate the toggle
        this.style.transform = 'scale(0.9)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 150);
    });
});
</script>
