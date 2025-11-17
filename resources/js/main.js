import { createApp } from 'vue';
import App from './App.vue';
import router from '../router/index.js';
import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:8000/api';
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;

const app = createApp(App);

app.config.globalProperties.$axios = axios;

app.use(router).mount('#app');
