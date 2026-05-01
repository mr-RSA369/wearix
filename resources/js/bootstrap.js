import axios from 'axios';

window.axios = axios;

// IMPORTANT: base URL (Laravel backend)
window.axios.defaults.baseURL = 'http://localhost:8000';

// REQUIRED for Sanctum (cookies)
window.axios.defaults.withCredentials = true;

// existing header
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
