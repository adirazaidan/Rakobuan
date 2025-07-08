import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

// MENAMBAHKAN HEADER PENTING AGAR DIKENALI SEBAGAI AJAX
window.axios.defaults.headers.common['X-Requested-with'] = 'XMLHttpRequest';

// Mengambil dan mengatur CSRF token untuk keamanan
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});