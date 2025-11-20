<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8 sm:p-10">
            <h2 class="text-3xl font-bold mb-6 text-center text-gray-900">Prihlásenie</h2>

            <form @submit.prevent="login" class="space-y-5" novalidate>
                <div>
                    <label class="block mb-1 text-gray-700 font-medium">Email</label>
                    <input
                        v-model="email"
                        type="email"
                        placeholder="mail@mail.com"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    />
                </div>

                <div>
                    <label class="block mb-1 text-gray-700 font-medium">Heslo</label>
                    <input
                        v-model="password"
                        type="password"
                        placeholder="********"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    />
                </div>

                <div v-if="error" class="text-red-500 text-sm font-medium">{{ error }}</div>

                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg flex items-center justify-center gap-2 shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105"
                >
                    <svg v-if="loading" class="w-5 h-5 animate-spin text-white" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span>{{ loading ? 'Prihlasujem...' : 'Prihlásiť sa' }}</span>
                </button>
            </form>
            <div class="text-xs text-gray-400 mt-2">
                {{ version }}
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import router from '../router';

export default {
    data() {
        return {
            email: '',
            password: '',
            error: '',
            loading: false,
            version: ''
        };
    },
    async mounted() {
        try {
            const res = await axios.get('/version');
            this.version = res.data.version;
        } catch (err) {
            this.version = 'dev';
        }
    },
    methods: {
        async login() {
            this.loading = true;
            this.error = '';
            try {
                const res = await axios.post('/login', {
                    email: this.email,
                    password: this.password
                });

                localStorage.setItem('token', res.data.token);
                localStorage.setItem('user', JSON.stringify(res.data.user));
                axios.defaults.headers.common['Authorization'] = `Bearer ${res.data.token}`;
                router.push('/dashboard');
            } catch (err) {
                this.error = err.response?.data?.message || 'Chyba prihlásenia';
            } finally {
                this.loading = false;
            }
            if (!this.email || !this.password) {
                this.error = 'Email a heslo sú povinné';
                this.loading = false;
                return;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(this.email)) {
                this.error = 'Neplatný formát emailu';
                this.loading = false;
                return;
            }
        }
    }
};
</script>
