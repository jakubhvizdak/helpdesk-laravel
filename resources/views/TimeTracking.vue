<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Záznamy času</h1>
                <p class="text-gray-600">Prehľad odpracovaného času na úlohách a projektoch</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtre
                    </h2>
                    <button
                        @click="resetFilters"
                        class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Vyčistiť
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Používateľ</label>
                        <input
                            v-model="filters.user"
                            type="text"
                            placeholder="Zadaj meno"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Úloha / projekt</label>
                        <input
                            v-model="filters.task"
                            type="text"
                            placeholder="Zadaj názov úlohy"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aktivita</label>
                        <input
                            v-model="filters.activity"
                            type="text"
                            placeholder="napr. Vývoj"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Od dátumu</label>
                        <input
                            v-model="filters.dateFrom"
                            type="date"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Do dátumu</label>
                        <input
                            v-model="filters.dateTo"
                            type="date"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>
                </div>
            </div>

            <div v-if="loading" class="flex justify-center items-center h-64">
                <div class="relative">
                    <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-16 w-16"></div>
                    <p class="text-gray-600 mt-4 text-center">Načítavam záznamy...</p>
                </div>
            </div>

            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fadeIn">
                <div class="overflow-x-auto">
                    <table class="responsive-table min-w-full">
                        <thead>
                        <tr>
                            <th>Dátum</th>
                            <th>Používateľ</th>
                            <th>Úloha</th>
                            <th>Čas</th>
                            <th>Aktivita</th>
                            <th>Komentár</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="time in filteredTimes"
                            :key="time.id"
                        >
                            <td data-label="Dátum">
                                {{ formatDateTime(time.created_at) }}
                            </td>
                            <td data-label="Používateľ">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                        {{ getInitials(time.user?.name, time.user?.surname) }}
                                    </div>
                                    <span class="text-gray-900 font-medium">
                                            {{ (time.user?.name || '') + ' ' + (time.user?.surname || '') || 'Neznámy' }}
                                        </span>
                                </div>
                            </td>
                            <td data-label="Úloha">
                                <router-link
                                    :to="`/task/${time.task.id}`"
                                    class="text-blue-600 hover:text-blue-700 hover:underline font-medium"
                                >
                                    {{ time.task ? `#${time.task.id} — ${time.task.title}` : 'Neznáma úloha' }}
                                </router-link>
                            </td>
                            <td data-label="Čas">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ time.hours }}h
                                    </span>
                            </td>
                            <td data-label="Aktivita">
                                {{ time.activity?.label || 'Neznáma aktivita' }}
                            </td>
                            <td data-label="Komentár">
                                {{ time.description || '-' }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="filteredTimes.length === 0" class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Žiadne záznamy</h3>
                    <p class="text-gray-600">Žiadne záznamy nevyhovujú zvoleným filtrom</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import axios from 'axios';
import { getInitials } from '../js/composables/string';
import { formatDateTime } from '../js/composables/date';


export default {
    name: 'TimeTracking',
    components: { Navbar },
    data() {
        return {
            times: [],
            loading: true,
            user: null,
            filters: {
                user: '',
                task: '',
                activity: '',
                dateFrom: '',
                dateTo: '',
            },
        };
    },
    computed: {
        filteredTimes() {
            return this.times.filter((t) => {
                const name = t.user?.name?.toLowerCase() || '';
                const task = t.task?.title?.toLowerCase() || '';
                const activity = t.activity?.label?.toLowerCase() || '';
                const createdAt = new Date(t.created_at);

                if (this.user?.role === 'customer' && t.task?.private) {
                    return false;
                }

                return (
                    (!this.filters.user || name.includes(this.filters.user.toLowerCase())) &&
                    (!this.filters.task || task.includes(this.filters.task.toLowerCase())) &&
                    (!this.filters.activity || activity.includes(this.filters.activity.toLowerCase())) &&
                    (!this.filters.dateFrom || createdAt >= new Date(this.filters.dateFrom)) &&
                    (!this.filters.dateTo || createdAt <= new Date(this.filters.dateTo + 'T23:59:59'))
                );
            });
        },
    },
    methods: {
        async fetchData() {
            this.loading = true;
            try {
                const res = await axios.get('http://127.0.0.1:8000/api/time-tracking', {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
                });
                this.times = res.data;
            } catch (err) {
                console.error('Chyba pri načítaní dát:', err.response || err);
                this.times = [];
            } finally {
                this.loading = false;
            }
        },
        formatDateTime(value) {
            return formatDateTime(value);
        },
        getInitials(name, surname) {
            return getInitials(name, surname);
        },
        resetFilters() {
            this.filters = {
                user: '',
                task: '',
                activity: '',
                dateFrom: '',
                dateTo: '',
            };
        },
    },
    async mounted() {
        try {
            const me = await this.$axios.get('/me');
            this.user = me.data;
        } catch (err) {
            console.error('Chyba pri načítaní info o používateľovi', err);
        }

        this.fetchData();
    },
};
</script>
