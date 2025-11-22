<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar />

        <div class="p-4 sm:p-6 w-full max-w-5xl mx-auto">
            <div v-if="loading" class="flex justify-center items-center py-32">
                <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
            </div>

            <div v-else-if="task" class="space-y-6">
                <button @click="$router.back()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="font-medium">Späť</span>
                </button>

                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ task.title }}</h1>
                                    <span
                                        v-if="task.private"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg flex-shrink-0 shadow-sm border border-amber-200"
                                        title="Súkromná úloha"
                                    >
                    <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                    </svg>
                </span>
                                </div>
                            </div>
                            <span
                                v-if="task.status"
                                :class="[task.status.color_bg, task.status.color_text, 'px-3 py-1 rounded-lg text-sm font-semibold whitespace-nowrap h-fit']"
                            >
                            {{ task.status.label }}
                        </span>
                        </div>
                        <p v-if="task.description" class="text-gray-600 mt-3">{{ task.description }}</p>
                        <p v-else class="text-gray-400 mt-3 italic">Bez popisu</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                <div>
                                    <p class="text-gray-500 mb-1">Projekt</p>
                                    <p class="font-semibold text-gray-900">{{ task.project?.name || '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <div>
                                    <p class="text-gray-500 mb-1">Priradené</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ task.assigned ? task.assigned.name + ' ' + task.assigned.surname : 'Nepriradené' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-gray-500 mb-1">Termín</p>
                                    <p class="font-semibold text-gray-900">{{ task.due_date ? formatDate(task.due_date) : '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-gray-500 mb-1">Odpracované</p>
                                    <p class="font-semibold text-gray-900">{{ workedHours }} h</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex gap-2">
                        <button
                            @click="activeTab = 'discussion'"
                            :class="activeTab === 'discussion' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="px-4 py-2 rounded-lg font-medium transition-colors"
                        >
                            Diskusia
                        </button>
                        <button
                            @click="activeTab = 'history'"
                            :class="activeTab === 'history' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="px-4 py-2 rounded-lg font-medium transition-colors"
                        >
                            História
                        </button>
                    </div>
                    <button
                        v-if="activeTab === 'discussion'"
                        @click="showEditModal = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        Upraviť/Reagovať
                    </button>
                </div>

                <div v-if="activeTab === 'discussion'" class="bg-white rounded-xl shadow-md p-6">
                    <div v-if="loadingComments" class="flex justify-center py-8">
                        <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    </div>
                    <div v-else-if="comments.length" class="space-y-4">
                        <div v-for="comment in comments" :key="comment.id" :class="['flex gap-3', getUserSide(comment.user.id) === 'right' ? 'flex-row-reverse' : '']">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm bg-blue-600">
                                    {{ getInitials(comment.user.name, comment.user.surname) }}
                                </div>
                            </div>
                            <div :class="['max-w-[70%]', getUserSide(comment.user.id) === 'right' ? 'items-end' : 'items-start']">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ comment.user.name }} {{ comment.user.surname }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDateDifference(comment.created_at) }}</p>
                                </div>
                                <div :class="['rounded-2xl px-4 py-3 break-words', getUserSide(comment.user.id) === 'right' ? 'bg-blue-600 text-white rounded-br-md' : 'bg-gray-100 text-gray-900 rounded-bl-md']">
                                    <p class="text-sm whitespace-pre-wrap">{{ comment.text }}</p>
                                </div>
                                <ul v-if="comment.attachments && comment.attachments.length" class="mt-2 space-y-1">
                                    <li v-for="file in comment.attachments" :key="file.id">
                                        <a
                                            :href="file.url"
                                            target="_blank"
                                            class="text-blue-600 hover:underline text-sm flex items-center gap-1"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4v16m8-8H4"/>
                                            </svg>
                                            {{ file.filename }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12 text-gray-500">
                        <p>Zatiaľ žiadne komentáre</p>
                    </div>
                </div>

                <div v-if="activeTab === 'history'" class="bg-white rounded-xl shadow-md p-6">
                    <div v-if="loadingHistory" class="flex flex-col items-center justify-center py-12">
                        <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                        <p class="mt-4 text-sm text-gray-500">Načítavam históriu...</p>
                    </div>

                    <div v-else-if="filteredLogs.length" class="space-y-3">
                        <div
                            v-for="log in filteredLogs"
                            :key="log.id"
                            class="relative pl-10 pb-4 group"
                        >
                            <div class="absolute left-2 top-0 bottom-0 w-0.5 bg-gray-200 group-last:hidden"></div>

                            <div class="absolute left-0 top-1.5 w-5 h-5 rounded-full bg-blue-500 border-4 border-white shadow-sm"></div>

                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors border border-gray-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-500">
                        {{ formatDate(log.created_at) }}
                    </span>
                                </div>

                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Používateľ <strong class="font-semibold text-gray-900">{{ log.user.name }} {{ log.user.surname }}</strong>

                                    <span v-if="log.type === 'status_change'">
                        zmenil stav z "{{ log.old_value }}" na "{{ log.new_value }}"
                    </span>
                                    <span v-else-if="log.type === 'assigned_user'">
                        zmenil priradeného používateľa z "{{ log.old_value }}" na "{{ log.new_value }}"
                    </span>
                                    <span v-else-if="log.type === 'due_date'">
                        zmenil termín z "{{ log.old_value }}" na "{{ log.new_value }}"
                    </span>
                                    <span v-else>
                        zmenil {{ log.type }} z "{{ log.old_value }}" na "{{ log.new_value }}"
                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-700 mb-1">Zatiaľ žiadne zmeny</h3>
                        <p class="text-sm text-gray-500">História zmien sa zobrazí po prvej úprave</p>
                    </div>
                </div>
                </div>

            <div v-else class="text-center py-32 text-gray-500">
                <p class="text-xl font-semibold">Úloha sa nenašla</p>
            </div>
        </div>

        <EditTaskModal
            v-if="task"
            :visible="showEditModal"
            :task-id="task.id"
            @close="showEditModal = false"
            @updated="handleUpdate"
        />

        <NotificationContainer ref="notifier" />
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import EditTaskModal from '../components/modals/EditTaskModal.vue';
import NotificationContainer from '../components/notification/NotificationContainer.vue';
import { formatDate, formatDateDifference } from '../js/composables/date.js';
import { getInitials } from '../js/composables/string.js';

export default {
    name: 'TaskDetail',
    components: { Navbar, EditTaskModal, NotificationContainer },
    data() {
        return {
            task: null,
            comments: [],
            times: [],
            editLogs: [],
            loading: true,
            showEditModal: false,
            loadingComments: true,
            loadingTimes: true,
            loadingHistory: true,
            activeTab: 'discussion',
            userSideMap: {},
            usedSides: { left: 0, right: 0 },
        };
    },
    computed: {
        workedHours() {
            if (!Array.isArray(this.times)) return 0;
            return this.times.map(t => parseFloat(t.hours) || 0).reduce((sum, val) => sum + val, 0).toFixed(2);
        },
        filteredLogs() {
            return this.editLogs.filter(log => log && log.user && log.type !== 'comment_added');
        }
    },
    async mounted() {
        await this.fetchTask();
        await this.fetchComments();
        await this.fetchTimes();
        await this.fetchEditLogs();
    },
    watch: {
        '$route.params.id': {
            immediate: false,
            handler() {
                this.loading = true;
                this.fetchTask();
                this.fetchComments();
                this.fetchTimes();
                this.fetchEditLogs();
            }
        }
    },
    methods: {
        async fetchTask() {
            try {
                const res = await this.$axios.get(`/tasks/${this.$route.params.id}`);
                this.task = res.data;
            } catch (err) {
                if (err.response?.status === 403) {
                    this.$router.push('/dashboard');
                    return;
                }
                console.error(err);
            } finally {
                this.loading = false;
            }
        },
        async fetchComments() {
            this.loadingComments = true;
            try {
                const res = await this.$axios.get(`/tasks/${this.$route.params.id}/comments`);
                this.comments = res.data;
            } catch (err) {
                console.error(err);
            } finally {
                this.loadingComments = false;
            }
        },
        async fetchTimes() {
            this.loadingTimes = true;
            try {
                const res = await this.$axios.get(`/tasks/${this.$route.params.id}/times`);
                this.times = res.data;
            } catch (err) {
                console.error(err);
            } finally {
                this.loadingTimes = false;
            }
        },
        async fetchEditLogs() {
            this.loadingHistory = true;
            try {
                const res = await this.$axios.get(`/tasks/${this.$route.params.id}/edit-log`);
                this.editLogs = res.data;
            } catch (err) {
                console.error(err);
            } finally {
                this.loadingHistory = false;
            }
        },
        async handleUpdate(data) {
            if (data.comment) this.comments.push(data.comment);
            if (data.time) this.times.push(data.time);
            if (data.updatedFields) {
                const f = data.updatedFields;

                if (f.status) this.task.status = f.status;
                if (f.due_date) this.task.due_date = f.due_date;

                if (f.assigned_to !== undefined) {
                    try {
                        await this.fetchTask();
                    } catch (err) {
                        console.error("Nepodarilo sa obnoviť task po zmene priradenia:", err);
                    }
                }
            }

            await this.fetchEditLogs();
        },
        getUserSide(userId) {
            if (!this.userSideMap[userId]) {
                const side = this.usedSides.left <= this.usedSides.right ? 'left' : 'right';
                this.userSideMap[userId] = side;
                this.usedSides[side]++;
            }
            return this.userSideMap[userId];
        },
        getInitials(name, surname) {
            return getInitials(name, surname);
        },
        formatDate(value) {
            return formatDate(value);
        },
        formatDateDifference(value) {
            return formatDateDifference(value);
        },
    },
};
</script>

