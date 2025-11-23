<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

            <div class="mb-4 sm:mb-6">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Profil</h1>
                <p class="text-gray-600 text-xs sm:text-sm">Spravujte svoje osobné údaje a nastavenia</p>
            </div>

            <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="relative">
                        <div class="w-14 h-14 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">Načítavam profil...</p>
                </div>
            </div>

            <div v-else class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">

                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow text-lg font-bold text-blue-600">
                            {{ getInitialsForProfile() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-white truncate">{{ profile.name }} {{ profile.surname }}</h2>
                            <p class="text-sm sm:text-base text-white truncate">{{ profile.email }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="bg-blue-600 bg-opacity-0 rounded-lg p-3 flex items-center gap-2 min-w-[150px]">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-white text-xs">Registrácia</p>
                                <p class="font-bold text-white text-sm">{{ formatDate(profile.created_at) }}</p>
                            </div>
                        </div>

                        <div class="bg-green-600 bg-opacity-0 rounded-lg p-3 flex items-center gap-2 min-w-[150px]">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <p class="text-white text-xs">Posledné prihlásenie</p>
                                <p class="font-bold text-white text-sm">{{ formatDate(profile.last_login_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="updateProfile" class="p-4 sm:p-6 space-y-4 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1">Meno</label>
                            <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"/>
                        </div>
                        <div>
                            <label class="block mb-1">Priezvisko</label>
                            <input v-model="form.surname" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"/>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block mb-1">Email</label>
                            <input v-model="form.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"/>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1">Nové heslo</label>
                            <input v-model="form.password" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"/>
                        </div>
                        <div>
                            <label class="block mb-1">Potvrďte heslo</label>
                            <input v-model="form.password_confirmation" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"/>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="saving"
                            class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                        >
                            <Save class="w-5 h-5" v-if="!saving" />
                            <svg v-else class="w-5 h-5 animate-spin" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <span>{{ saving ? 'Ukladám...' : 'Uložiť zmeny' }}</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import { Save } from 'lucide-vue-next';
import { formatDate as formatDateHelper } from '../js/composables/date';
import { getInitials } from '../js/composables/string';

export default {
    name: 'Profile',
    inject: ['notify'],
    components: { Navbar, Save },
    data() {
        return {
            loading: true,
            saving: false,
            profile: {},
            form: { name: '', surname: '', email: '', password: '', password_confirmation: '' }
        };
    },
    async mounted() { await this.fetchProfile(); },
    methods: {
        async fetchProfile() {
            this.loading = true;
            try {
                const res = await this.$axios.get('/profile');
                this.profile = res.data;
                this.form.name = res.data.name;
                this.form.surname = res.data.surname;
                this.form.email = res.data.email;
            } catch (err) {
                console.error('Chyba pri načítaní profilu:', err);
                this.notify?.('Chyba pri načítaní profilu', 'error');
            } finally { this.loading = false; }
        },
        async updateProfile() {
            this.saving = true;
            try {
                const res = await this.$axios.put('/profile', this.form);
                this.profile = res.data;
                this.notify('Profil bol úspešne aktualizovaný!', 'success');
                this.form.password = '';
                this.form.password_confirmation = '';
            } catch (err) {
                console.error('Chyba pri aktualizácii profilu:', err);
                this.notify('Nepodarilo sa aktualizovať profil.', 'error');
            } finally { this.saving = false; }
        },
        getInitialsForProfile() {
            return getInitials(this.profile.name, this.profile.surname);
        },
        formatDate(value) {
            return formatDateHelper(value);
        },
    }
};
</script>
