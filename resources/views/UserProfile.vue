<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <button
                @click="$router.back()"
                class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors mb-4"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="font-medium">Späť</span>
            </button>

            <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="text-gray-600 font-medium">Načítavam profil...</p>
                </div>
            </div>

            <div v-else class="space-y-6">
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow text-lg font-bold text-blue-600">
                                {{ getInitialsForProfile() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-base sm:text-lg font-bold text-white truncate">{{ user.name }} {{ user.surname }}</h2>
                                <div class="inline-flex items-center gap-2 bg-white/20 px-2 py-1 rounded-full mt-1">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-xs font-semibold text-white">{{ getRoleLabel(user.role) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <div class="bg-blue-600 bg-opacity-0 rounded-lg p-3 flex items-center gap-2 min-w-[140px]">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-white text-xs">Registrácia</p>
                                    <p class="font-bold text-white text-sm">{{ formatDate(user.created_at) }}</p>
                                </div>
                            </div>

                            <div class="bg-green-600 bg-opacity-0 rounded-lg p-3 flex items-center gap-2 min-w-[140px]">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <div>
                                    <p class="text-white text-xs">Posledné prihlásenie</p>
                                    <p class="font-bold text-white text-sm">{{ formatDate(user.last_login_at) || 'Nikdy' }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Kontaktné informácie
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium text-gray-900">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">Rola</p>
                                    <p class="font-medium text-gray-900">{{ getRoleLabel(user.role) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import { getInitials } from '../js/composables/string';
import { formatDate as formatDateHelper } from '../js/composables/date';
import axios from 'axios';

export default {
    name: 'UserProfile',
    components: { Navbar },
    data() {
        return {
            user: {},
            loading: true,
        };
    },
    async mounted() {
        await this.fetchUser();
    },
    methods: {
        async fetchUser() {
            this.loading = true;
            try {
                const id = this.$route.params.id;
                const res = await axios.get(`/profile/${id}`);
                this.user = res.data;
            } catch (err) {
                console.error('Chyba pri načítaní používateľa:', err);
            } finally {
                this.loading = false;
            }
        },
        getInitialsForProfile() {
            return getInitials(this.user.name, this.user.surname);
        },
        formatDate(value) {
            return formatDateHelper(value);
        },
        getRoleLabel(role) {
            const roles = {
                'admin': 'Administrátor',
                'user': 'Používateľ',
                'customer': 'Zákazník'
            };
            return roles[role] || role;
        }
    },
};
</script>
