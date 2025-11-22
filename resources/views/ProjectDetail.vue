<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar />

        <div class="p-6 w-full max-w-6xl mx-auto">
            <div v-if="loading" class="text-center py-16">
                <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-16 w-16 mx-auto"></div>
            </div>

            <div v-else-if="project" class="space-y-6">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 relative">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ project.name }}</h1>
                            <p class="text-gray-700">{{ project.description || '' }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                @click="$router.push(`/project/${project.id}/documentation`)"
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100 transition-colors flex items-center gap-2 shadow-sm"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 20h9M12 4h9m-9 8h9M4 8h.01M4 16h.01M4 4h.01M4 20h.01"/>
                                </svg>
                                <span>Dokumentácia</span>
                            </button>

                        <button
                            @click="showTaskModal = true"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Pridať úlohu</span>
                        </button>
                    </div>
                    </div>

                    <div v-if="project.service_hours" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                            <p class="text-sm text-gray-500">Počet hodín</p>
                            <p class="text-2xl font-semibold text-gray-800">
                                {{ project.service_hours.hours_total }} h
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                            <p class="text-sm text-gray-500">Zostávajúce hodiny</p>
                            <p class="text-2xl font-semibold text-green-600">
                                {{ project.service_hours.hours_remaining }} h
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                            <p class="text-sm text-gray-500">Minuté hodiny</p>
                            <p class="text-2xl font-semibold text-blue-600">
                                {{ usedHours }} h
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                            <p class="text-sm text-gray-500">Hodinová sadzba</p>
                            <p class="text-2xl font-semibold text-indigo-600">
                                {{ formatCurrency(project.service_hours.hourly_price) }}
                            </p>
                        </div>
                    </div>

                    <div v-else class="text-gray-500 mt-4">
                        Pre tento projekt zatiaľ neboli zadané servisné hodiny.
                    </div>
                </div>

                <div class="overflow-x-auto bg-white shadow-md rounded-lg border border-gray-200">
                    <template v-if="project.tasks && project.tasks.length">
                        <table class="min-w-full text-sm text-left text-gray-700 table-fixed">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-3 border-b">Názov úlohy</th>
                                <th class="px-6 py-3 border-b">Priradené</th>
                                <th class="px-6 py-3 border-b text-center">Stav</th>
                                <th class="px-6 py-3 border-b">Upravené</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="task in visibleTasks"
                                :key="task.id"
                                class="border-t hover:bg-blue-50 transition"
                            >
                                <td
                                    class="px-6 py-3 font-medium text-gray-800 cursor-pointer hover:text-blue-600 hover:no-underline"
                                    @click="$router.push(`/task/${task.id}`)"
                                >
                                    {{ task.title }}
                                </td>

                                <td class="px-6 py-3">
                                    {{ task.assigned ? task.assigned.name + ' ' + task.assigned.surname : '-' }}
                                </td>

                                <td class="px-6 py-3 text-center">
                  <span
                      v-if="task.status"
                      :class="`${task.status.color_bg} ${task.status.color_text} px-3 py-1 rounded-full text-xs font-semibold`"
                  >
                    {{ task.status.label }}
                  </span>
                                    <span
                                        v-else
                                        class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold"
                                    >
                    -
                  </span>
                                </td>

                                <td class="px-6 py-3">{{ formatDateTime(task.updated_at) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </template>

                    <p v-else class="text-gray-500 text-center py-6">
                        Tento projekt zatiaľ nemá žiadne úlohy.
                    </p>
                </div>

                <AddTaskModal
                    :visible="showTaskModal"
                    :project-id="project.id"
                    :currentUser="user"
                    @close="showTaskModal = false"
                    @created="project.tasks.unshift($event)"
                    @notify="showNotification"
                />

            </div>

            <div v-else class="text-center text-gray-500 py-16">
                Projekt sa nenašiel.
            </div>

            <NotificationContainer ref="notifier" />
        </div>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import AddTaskModal from '../components/modals/AddTaskModal.vue';
import NotificationContainer from '../components/notification/NotificationContainer.vue';
import { formatCurrency as formatCurrencyHelper } from '../js/composables/string';
import { formatDateTime } from '../js/composables/date';

export default {
    name: 'ProjectDetail',
    components: { Navbar, AddTaskModal, NotificationContainer },
    data() {
        return {
            project: null,
            loading: true,
            showTaskModal: false,
        };
    },
    computed: {
        visibleTasks() {
            if (!this.project?.tasks) return [];

            if (this.user?.role === 'customer') {
                return this.project.tasks.filter(t => !t.private);
            }

            return this.project.tasks;
        },
        usedHours() {
            if (!this.project?.service_hours) return 0;
            return (
                this.project.service_hours.hours_total -
                this.project.service_hours.hours_remaining
            ).toFixed(2);
        },
    },
    async mounted() {
        const userRes = await this.$axios.get('/me');
        this.user = userRes.data;

        const id = this.$route.params.id;
        try {
            const res = await this.$axios.get(`/projects/${id}`);
            this.project = res.data;
        } catch (err) {
            console.error('Chyba pri načítaní projektu:', err);
        } finally {
            this.loading = false;
        }
    },
    methods: {
        formatDateTime(value) {
            return formatDateTime(value);
        },
        formatCurrency(value) {
            return formatCurrencyHelper(value);
        },
        showNotification({ message, type }) {
            this.$refs.notifier.addNotification(message, type);
        },
    },
};
</script>
