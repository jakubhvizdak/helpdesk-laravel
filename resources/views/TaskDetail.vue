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
                                <h1 class="text-2xl font-bold text-gray-900">{{ task.title }}</h1>
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

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Diskusia</h2>
                        <button @click="showEditModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Upraviť/Reagovať
                        </button>
                    </div>

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
            loading: true,
            showEditModal: false,
            loadingComments: true,
            loadingTimes: true,
            userSideMap: {},
            usedSides: { left: 0, right: 0 },
        };
    },
    computed: {
        workedHours() {
            if (!Array.isArray(this.times)) return 0;
            return this.times.map(t => parseFloat(t.hours) || 0).reduce((sum, val) => sum + val, 0).toFixed(2);
        }
    },
    async mounted() {
        await this.fetchTask();
        await this.fetchComments();
        await this.fetchTimes();
    },
    methods: {
        async fetchTask() {
            try {
                const res = await this.$axios.get(`/tasks/${this.$route.params.id}`);
                this.task = res.data;
            } catch (err) {
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
        handleUpdate(data) {
            if (data.comment) this.comments.push(data.comment);
            if (data.time) this.times.push(data.time);
            if (data.updatedFields) {
                const f = data.updatedFields;

                if (f.status) this.task.status = f.status;
                if (f.due_date) this.task.due_date = f.due_date;

                if (f.assigned_to !== undefined) {
                    try {
                        this.fetchTask();
                    } catch (err) {
                        console.error("Nepodarilo sa obnoviť task po zmene priradenia:", err);
                    }
                }
            }
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

