<template>
    <div
        v-if="visible"
        class="fixed inset-0 flex items-center justify-center z-50 p-4"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden relative animate-fadeIn">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">
                        Nová úloha
                    </h2>
                </div>
                <button
                    @click="$emit('close')"
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto max-h-[calc(90vh-80px)]">
                <form @submit.prevent="submitForm" class="p-6">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                Projekt <span class="text-red-500">*</span>
                            </label>
                            <template v-if="fixedProject">
                                <div class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 font-medium">
                                    {{ fixedProject.name }}
                                </div>
                            </template>
                            <template v-else>
                                <select
                                    v-model="form.project_id"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    required
                                    @change="loadProjectUsers"
                                >
                                    <option :value="null">-- Vyber projekt --</option>
                                    <option v-for="project in projects" :key="project.id" :value="project.id">
                                        {{ project.name }}
                                    </option>
                                </select>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                Názov úlohy <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Názov úlohy"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                </svg>
                                Popis
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                                placeholder="Detailný popis úlohy a požiadaviek..."
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Termín dokončenia
                                </label>
                                <input
                                    v-model="form.due_date"
                                    type="date"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Priradiť používateľa
                                    <span v-if="!allowUnassigned" class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.assigned_to"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                >
                                    <option :value="null" :selected="form.assigned_to === null">-- Nepriradené --</option>
                                    <option v-for="user in projectUsers" :key="user.id" :value="user.id">
                                        {{ user.name }} {{ user.surname }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Stav úlohy
                            </label>
                            <div class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 bg-blue-50 flex items-center gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-blue-100 text-blue-800">
                                    Priradená
                                </span>
                                <span class="text-sm text-gray-600">Predvolený stav pre novú úlohu</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end mt-8 gap-3">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors"
                        >
                            Zrušiť
                        </button>

                        <button
                            type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2"
                            :disabled="loading"
                        >
                            <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg v-else class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span v-if="!loading">Vytvoriť úlohu</span>
                            <span v-else>Vytváram...</span>
                        </button>
                    </div>
                </form>
            </div>

            <NotificationContainer ref="notifier" />
        </div>
    </div>
</template>

<script>
import NotificationContainer from '../notification/NotificationContainer.vue';

export default {
    name: "AddTaskModal",
    components: { NotificationContainer },
    props: {
        visible: Boolean,
        projectId: { type: Number, default: null },
    },
    data() {
        return {
            loading: false,
            allowUnassigned: true,
            form: {
                title: "",
                description: "",
                due_date: "",
                assigned_to: null,
                project_id: null,
            },
            projects: [],
            projectUsers: [],
            fixedProject: null,
        };
    },
    mounted() {
        if (this.projectId) {
            this.loadFixedProject();
        } else {
            this.loadProjects();
        }
    },
    methods: {
        async loadFixedProject() {
            try {
                const res = await this.$axios.get(`/projects/${this.projectId}`);
                this.fixedProject = res.data;
                this.form.project_id = this.fixedProject.id;
                this.loadProjectUsers();
            } catch (err) {
                console.error("Nepodarilo sa načítať projekt:", err);
            }
        },
        async loadProjects() {
            try {
                const res = await this.$axios.get("/my-projects");
                this.projects = res.data;
            } catch (err) {
                console.error("Nepodarilo sa načítať projekty:", err);
                this.projects = [];
            }
        },
        async loadProjectUsers() {
            if (!this.form.project_id) return;
            try {
                const res = await this.$axios.get(`/projects/${this.form.project_id}/users`);

                this.allowUnassigned = res.data.allow_create_unassigned_task;

                this.projectUsers = res.data.allUsers.filter(user =>
                    res.data.assignedUserIds.includes(user.id)
                );
            } catch (err) {
                console.error("Nepodarilo sa načítať používateľov projektu:", err);
                this.projectUsers = [];
            }
        },
        async submitForm() {
            if (!this.form.title.trim()) {
                this.$refs.notifier.addNotification("Názov úlohy je povinný.", "error");
                return;
            }
            if (!this.form.project_id) {
                this.$refs.notifier.addNotification("Musíš vybrať projekt.", "error");
                return;
            }

            if (this.form.assigned_to === "null") {
                this.form.assigned_to = null;
            }

            if (!this.allowUnassigned && !this.form.assigned_to) {
                this.$refs.notifier.addNotification("Musíš priradiť používateľa.", "error");
                return;
            }

            this.loading = true;
            try {
                const res = await this.$axios.post("/tasks", {
                    title: this.form.title,
                    description: this.form.description,
                    project_id: this.form.project_id,
                    due_date: this.form.due_date || null,
                    assigned_to: this.form.assigned_to || null,
                    status: 'priradena',
                });

                this.$emit("created", res.data);
                this.$emit("notify", { message: "Úloha bola úspešne vytvorená", type: "success" });

                this.form = {
                    title: "",
                    description: "",
                    due_date: "",
                    assigned_to: null,
                    project_id: this.projectId || null,
                };
                this.$emit("close");

            } catch (err) {
                console.error("Chyba pri vytváraní úlohy:", err.response?.data || err);
                const msg = err.response?.data?.message || "Nepodarilo sa vytvoriť úlohu.";
                this.$refs.notifier.addNotification(msg, "error");
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
