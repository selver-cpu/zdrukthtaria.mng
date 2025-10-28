import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Dëgjo për njoftime të reja
window.Echo.private(`njoftimet.${window.userId}`)
    .listen('NjoftimIRi', (e) => {
        // Krijo një instancë të re të komponentit të njoftimit
        const notification = document.createElement('div');
        notification.innerHTML = `
            <x-notification
                type="info"
                message="${e.mesazhi}"
            />
        `;
        
        // Shto njoftimin në DOM
        document.getElementById('notifications-container')
            ?.appendChild(notification);
            
        // Përditëso numëruesin e njoftimeve të palexuara
        const counter = document.getElementById('notifications-counter');
        if (counter) {
            const currentCount = parseInt(counter.textContent || '0');
            counter.textContent = currentCount + 1;
        }
    });
