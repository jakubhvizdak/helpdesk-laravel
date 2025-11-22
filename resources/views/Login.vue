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
                    <div class="relative">
                        <input
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="********"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors focus:outline-none"
                            tabindex="-1"
                        >
                            <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div v-if="error" class="text-red-500 text-sm font-medium bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                    {{ error }}
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg flex items-center justify-center gap-2 shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
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
            showPassword: false,
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
            if (!this.email || !this.password) {
                this.error = 'Email a heslo sú povinné';
                return;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(this.email)) {
                this.error = 'Neplatný formát emailu';
                return;
            }

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
        }
    }
};
</script>
