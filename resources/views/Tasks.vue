<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Úlohy</h1>
                <p class="text-gray-600">Prehľad a správa všetkých úloh vo vašich projektoch</p>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Názov úlohy</label>
                        <input
                            v-model="filters.title"
                            type="text"
                            placeholder="Zadaj názov"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Projekt</label>
                        <input
                            v-model="filters.project"
                            type="text"
                            placeholder="Zadaj názov projektu"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stav</label>
                        <input
                            v-model="filters.status"
                            type="text"
                            placeholder="Zadaj stav"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priradené</label>
                        <select v-model.number="filters.assigned" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="">Všetci</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.full_name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Na stránku</label>
                        <select v-model.number="filters.perPage" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex justify-end">
                <button
                    @click="showAddTaskModal = true"
                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Pridať úlohu
                </button>
            </div>

            <div v-if="loading && !loaded" class="flex justify-center items-center h-64">
                <div class="relative">
                    <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-16 w-16"></div>
                    <p class="text-gray-600 mt-4 text-center">Načítavam úlohy...</p>
                </div>
            </div>

            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden animate-fadeIn">
                <div class="overflow-x-auto">
                    <table class="responsive-table min-w-full">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Názov úlohy</th>
                            <th>Projekt</th>
                            <th>Priradené</th>
                            <th>Stav</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-if="loading">
                            <tr v-for="n in 5" :key="'skeleton-' + n">
                                <td data-label="#"><div class="skeleton h-4 w-8"></div></td>
                                <td data-label="Názov úlohy"><div class="skeleton h-4 w-full"></div></td>
                                <td data-label="Projekt"><div class="skeleton h-4 w-32"></div></td>
                                <td data-label="Priradené"><div class="skeleton h-4 w-32"></div></td>
                                <td data-label="Stav"><div class="skeleton h-6 w-20"></div></td>
                            </tr>
                        </template>

                        <template v-else>
                            <tr
                                v-for="task in filteredTasks"
                                :key="task.id"
                                @click="$router.push(`/task/${task.id}`)"
                                class="cursor-pointer"
                            >
                                <td data-label="#">
                                    <span class="text-gray-500 font-medium">#{{ task.id }}</span>
                                </td>
                                <td data-label="Názov úlohy">
                                        <span class="font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                            {{ task.title }}
                                        </span>
                                </td>
                                <td data-label="Projekt">
                                    <span class="text-gray-700">{{ task.project?.name || '-' }}</span>
                                </td>
                                <td data-label="Priradené">
                                    <div v-if="task.assigned" class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                            {{ getInitials(task.assigned.name, task.assigned.surname) }}
                                        </div>
                                        <span class="text-gray-900 font-medium">
                                            {{ task.assigned.name + ' ' + task.assigned.surname }}
                                        </span>
                                    </div>
                                    <span v-else class="text-gray-500">-</span>
                                </td>
                                <td data-label="Stav">
                                        <span
                                            v-if="task.status"
                                            :class="[task.status.color_bg, task.status.color_text, 'inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold']"
                                        >
                                            {{ task.status.label }}
                                        </span>
                                    <span v-else class="text-gray-500 text-xs">Neznámy stav</span>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>

                <div v-if="!loading && loaded && filteredTasks.length === 0" class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Žiadne úlohy</h3>
                    <p class="text-gray-600">Žiadne úlohy nevyhovujú zvoleným filtrom</p>
                </div>

                <div v-if="tasks.data.length > 0" class="border-t border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-600">
                        Strana {{ tasks.current_page }} z {{ tasks.last_page }}
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="changePage(tasks.current_page - 1)"
                            :disabled="tasks.current_page === 1"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2 text-sm font-medium"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Predošlá
                        </button>
                        <button
                            @click="changePage(tasks.current_page + 1)"
                            :disabled="tasks.current_page === tasks.last_page"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2 text-sm font-medium"
                        >
                            Ďalšia
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <AddTaskModal
            :visible="showAddTaskModal"
            :project-id="null"
            @close="showAddTaskModal = false"
            @created="onTaskCreated"
            @notify="showNotification"
        />

        <NotificationContainer ref="notifier" />
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import AddTaskModal from '../components/modals/AddTaskModal.vue';
import NotificationContainer from '../components/notification/NotificationContainer.vue';
import { getInitials } from '../js/composables/string.js';

export default {
    name: 'Tasks',
    components: { Navbar, AddTaskModal, NotificationContainer },
    data() {
        return {
            loading: false,
            loaded: false,
            showAddTaskModal: false,
            tasks: { data: [], current_page: 1, last_page: 1 },
            users: [],
            filters: {
                title: '',
                project: '',
                status: '',
                assigned: '',
                perPage: 20,
                page: 1,
            },
        };
    },
    computed: {
        filteredTasks() {
            let filtered = [...this.tasks.data];
            if (this.filters.title) {
                const searchTerm = this.filters.title.toLowerCase();
                filtered = filtered.filter(task => task.title.toLowerCase().includes(searchTerm));
            }
            if (this.filters.project) {
                const searchTerm = this.filters.project.toLowerCase();
                filtered = filtered.filter(task => task.project?.name.toLowerCase().includes(searchTerm));
            }
            if (this.filters.status) {
                const searchTerm = this.filters.status.toLowerCase();
                filtered = filtered.filter(task => task.status?.label.toLowerCase().includes(searchTerm));
            }
            return filtered;
        }
    },
    watch: {
        'filters.assigned': function() { this.fetchTasks(1); },
        'filters.perPage': function() { this.fetchTasks(1); },
    },
    async mounted() {
        await this.fetchUsers();
        await this.fetchTasks();
    },
    methods: {
        async fetchUsers() {
            const res = await this.$axios.get('/users');
            this.users = res.data.map(u => ({ ...u, full_name: `${u.name} ${u.surname}` }));
        },
        async fetchTasks(page = 1) {
            this.loading = true;
            this.filters.page = page;
            try {
                const params = { page: this.filters.page, perPage: this.filters.perPage };
                if (this.filters.assigned) params.assigned = this.filters.assigned;
                const res = await this.$axios.get('/tasks', { params });
                this.tasks = res.data;
                this.loaded = true;
            } catch (err) {
                this.showNotification({ message: "Nepodarilo sa načítať úlohy.", type: "error" });
            } finally {
                this.loading = false;
            }
        },
        changePage(page) {
            if (page >= 1 && page <= this.tasks.last_page) this.fetchTasks(page);
        },
        onTaskCreated(task) {
            this.tasks.data.unshift(task);
        },
        getInitials(name, surname) {
            return getInitials(name, surname);
        },
        resetFilters() {
            this.filters = { title: '', project: '', status: '', assigned: '', perPage: 20, page: 1 };
            this.fetchTasks(1);
        },
        showNotification({ message, type }) {
            this.$refs.notifier.addNotification(message, type);
        }
    },
};
</script>
