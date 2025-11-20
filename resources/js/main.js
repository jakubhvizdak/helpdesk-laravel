import { createApp } from 'vue';
import App from './App.vue';
import router from '../router/index.js';
import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:8000/api';
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;

axios.interceptors.response.use(
    response => response,
    error => {
        if (
            error.response &&
            error.response.status === 401 &&
            !error.config.url.includes('/login')
        ) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }

        return Promise.reject(error);
    }
);

const app = createApp(App);

app.config.globalProperties.$axios = axios;

app.use(router).mount('#app');
