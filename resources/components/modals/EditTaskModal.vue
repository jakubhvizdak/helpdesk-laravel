<template>
    <transition name="modal">
        <div v-show="visible" class="fixed inset-0 flex items-center justify-center z-50 p-4 bg-black/40" @click.self="$emit('close')">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[95vh] overflow-hidden flex flex-col animate-fadeIn">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-white">Upraviť úlohu</h2>
                    </div>
                    <button @click="$emit('close')" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 custom-scrollbar">
                    <div v-if="loadingData" class="flex flex-col items-center justify-center py-24">
                        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                        <p class="text-gray-500 mt-4 font-medium">Načítavam údaje...</p>
                    </div>

                    <form v-else @submit.prevent="submit" class="p-6 space-y-6">
                        <div class="pb-6 border-b border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <span>Pridať komentár</span>
                            </label>
                            <textarea
                                v-model="comment"
                                rows="3"
                                class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none shadow-sm"
                                placeholder="Napíš komentár k úlohe..."
                            ></textarea>

                                <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <span>Priložiť súbory</span>
                                </label>

                                <input
                                    type="file"
                                    multiple
                                    @change="handleFileUpload"
                                    class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                />

                                <ul class="mt-3 space-y-2">
                                    <li
                                        v-for="(file, index) in files"
                                        :key="index"
                                        class="flex items-center justify-between bg-gray-100 rounded-lg px-3 py-2"
                                    >
                                        <span class="text-sm text-gray-700">{{ file.name }}</span>
                                        <button
                                            @click="removeFile(index)"
                                            class="text-red-500 hover:text-red-700 text-xs font-semibold"
                                        >
                                            Odstrániť
                                        </button>
                                    </li>
                                </ul>
                            </div>


                        <div class="pb-6 border-b border-gray-200">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-700">Odpracovaný čas</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Hodiny</label>
                                    <input
                                        v-model.number="hours"
                                        type="number"
                                        min="0"
                                        step="0.25"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                        placeholder="2.5"
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Typ aktivity</label>
                                    <select
                                        v-model="activity"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                    >
                                        <option value="">-- Vyber aktivitu --</option>
                                        <option
                                            v-for="option in activityOptions"
                                            :key="option.id"
                                            :value="option.id"
                                        >
                                            {{ option.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-xs font-medium text-gray-600 mb-2">Dátum odpracovania</label>
                                <input
                                    v-model="workedAt"
                                    type="datetime-local"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                />
                            </div>

                            <div class="mt-4">
                                <label class="block text-xs font-medium text-gray-600 mb-2">Popis vykonanej práce</label>
                                <textarea
                                    v-model="timeDescription"
                                    rows="3"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none shadow-sm"
                                    placeholder="Detailný popis vykonanej práce..."
                                ></textarea>
                            </div>
                        </div>

                        <div class="pb-6">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-700">Nastavenia úlohy</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Odovzdať do</label>
                                    <input
                                        v-model="dueDate"
                                        type="date"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Stav úlohy</label>
                                    <select
                                        v-model="status"
                                        :disabled="statusOptions.length <= 1"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                                    >
                                        <option
                                            v-for="s in statusOptions"
                                            :key="s.id"
                                            :value="s.id"
                                        >
                                            {{ s.label }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Priradené</label>
                                    <select
                                        v-model="assignedTo"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                    >
                                        <option value="">-- Vyber používateľa --</option>
                                        <option
                                            v-for="user in projectUsers"
                                            :key="user.id"
                                            :value="user.id"
                                        >
                                            {{ user.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-2">
                            <button
                                type="button"
                                @click="$emit('close')"
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold transition-all border-2 border-gray-200 hover:border-gray-300"
                            >
                                Zrušiť
                            </button>

                            <button
                                type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2"
                                :disabled="loading"
                            >
                                <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                <span v-if="!loading">Uložiť zmeny</span>
                                <span v-else>Ukladám...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <NotificationContainer ref="notifier" />
            </div>
        </div>
    </transition>
</template>

<script>
import NotificationContainer from "../notification/NotificationContainer.vue";

export default {
    name: "EditTaskModal",
    components: {NotificationContainer},
    props: {
        visible: Boolean,
        taskId: {type: Number, required: true},
    },
    data() {
        return {
            comment: "",
            files: [],
            hours: null,
            activity: "",
            timeDescription: "",
            workedAt: "",
            originalDueDate: "",
            dueDate: "",
            status: null,
            loading: false,
            loadingData: false,
            activityOptions: [],
            statusOptions: [],
            currentStatusId: null,
            assignedTo: null,
            projectUsers: [],
        };
    },
    watch: {
        visible(newVal) {
            if (newVal) this.loadModalData();
        }
    },
    methods: {
        async loadProjectUsers(projectId) {
            if (!projectId) {
                console.warn("Task nemá projekt!");
                return;
            }
            try {
                const res = await this.$axios.get(`/projects/${projectId}/assigned-users`);
                console.log("Assigned users:", res.data);
                this.projectUsers = res.data.map(u => ({
                    id: u.id,
                    name: `${u.name} ${u.surname}`
                }));
            } catch (err) {
                console.error("Nepodarilo sa načítať používateľov projektu:", err);
                this.$refs.notifier.addNotification("Nepodarilo sa načítať používateľov projektu.", "error");
            }
        },

        handleFileUpload(e) {
            this.files = Array.from(e.target.files);
        },

        removeFile(index) {
            this.files.splice(index, 1);
        },

        async loadModalData() {
            const now = new Date();
            const tzOffset = now.getTimezoneOffset() * 60000;
            this.loadingData = true;
            this.comment = "";
            this.files = [];
            this.hours = null;
            this.activity = "";
            this.timeDescription = "";
            this.workedAt = new Date(now - tzOffset).toISOString().slice(0, 16);
            this.status = null;
            this.projectUsers = [];

            try {
                const taskRes = await this.$axios.get(`/tasks/${this.taskId}`);
                const task = taskRes.data;

                this.currentStatusId = task.status_id;
                this.dueDate = task.due_date ? task.due_date.slice(0, 10) : "";
                this.originalDueDate = this.dueDate;

                await Promise.all([
                    this.loadActivities(),
                    this.loadAllowedTransitions(task),
                    this.loadProjectUsers(task.project_id)
                ]);

                this.assignedTo = this.projectUsers.find(u => u.id === task.assigned_to)?.id || "";
            } finally {
                this.loadingData = false;
            }
        },

        async loadActivities() {
            try {
                const res = await this.$axios.get("/task-activities");
                this.activityOptions = res.data;
            } catch (err) {
                console.error("Nepodarilo sa načítať aktivity:", err);
                this.$refs.notifier.addNotification("Nepodarilo sa načítať zoznam aktivít.", "error");
            }
        },

        async loadAllowedTransitions(task) {
            try {
                const res = await this.$axios.get(`/tasks/${this.taskId}/allowed-transitions`);
                let transitions = res.data.filter(t => t.id !== task.status_id);
                const currentStatusLabel = task.status?.label || 'Aktuálny stav';
                this.statusOptions = [
                    {id: task.status_id, label: currentStatusLabel},
                    ...transitions
                ];
                this.status = task.status_id;
            } catch (err) {
                console.error("Nepodarilo sa načítať statusy:", err);
                this.$refs.notifier.addNotification("Nepodarilo sa načítať povolené stavy úlohy.", "error");
            }
        },

        async submit() {
            if (this.files.length > 0 && !this.comment) {
                this.$refs.notifier.addNotification("Ak chcete nahrať súbory, musíte napísať komentár.", "error");
                return;
            }

            if (!this.comment && !this.hours && !this.status && !this.dueDate && !this.assignedTo && !this.timeDescription) {
                this.$refs.notifier.addNotification("Musíš vyplniť aspoň jedno pole.", "error");
                return;
            }

            if (this.hours && !this.activity) {
                this.$refs.notifier.addNotification("Ak zadáš odpracovaný čas, musíš vybrať aj typ aktivity.", "error");
                return;
            }

            if (this.activity && !this.hours) {
                this.$refs.notifier.addNotification("Ak vyberieš aktivitu, musíš zadať aj odpracovaný čas.", "error");
                return;
            }

            if (this.timeDescription && !this.hours) {
                this.$refs.notifier.addNotification("Ak vyplníš popis práce, musíš zadať aj odpracovaný čas.", "error");
                return;
            }

            if (this.timeDescription && !this.activity) {
                this.$refs.notifier.addNotification("Ak vyplníš popis práce, musíš vybrať aj typ aktivity.", "error");
                return;
            }

            if (this.files.length > 0 && !this.comment) {
                this.$refs.notifier.addNotification("Ak chcete nahrať súbory, musíte napísať komentár.", "error");
                return;
            }

            if (!this.comment && !this.hours && !this.status && !this.dueDate && !this.assignedTo) {
                this.$refs.notifier.addNotification("Musíš vyplniť aspoň jedno pole.", "error");
                return;
            }

            let updatedFields = {};

            this.loading = true;
            try {
                let newComment = null;
                let newTime = null;

                if (this.comment) {
                    const resComment = await this.$axios.post(`/tasks/${this.taskId}/comments`, {text: this.comment});
                    newComment = resComment.data;
                    newComment.attachments = [];
                    this.$refs.notifier.addNotification("Komentár bol úspešne pridaný", "success");

                    if (this.files.length > 0) {
                        const formData = new FormData();
                        this.files.forEach(file => formData.append("files[]", file));

                        try {
                            const resFiles = await this.$axios.post(
                                `/tasks/${this.taskId}/comments/${newComment.id}/attachments`,
                                formData,
                                {headers: {"Content-Type": "multipart/form-data"}}
                            );

                            newComment.attachments = resFiles.data.attachments || [];

                            this.$refs.notifier.addNotification("Súbory boli nahrané ku komentáru.", "success");
                            this.files = [];
                        } catch (err) {
                            console.error("Upload k komentáru zlyhal:", err);
                            this.$refs.notifier.addNotification("Nepodarilo sa nahrať súbory ku komentáru.", "error");
                        }
                    }
                }

                if (this.hours && this.activity) {
                    const resTime = await this.$axios.post(`/tasks/${this.taskId}/times`, {
                        hours: this.hours,
                        activity_id: this.activity,
                        description: this.timeDescription,
                        worked_at: this.workedAt,
                    });
                    newTime = resTime.data;
                    this.$refs.notifier.addNotification("Odpracovaný čas bol úspešne uložený", "success");
                }

                let taskUpdatePayload = {};
                if (this.dueDate !== this.originalDueDate) taskUpdatePayload.due_date = this.dueDate || null;
                if (this.assignedTo !== "" && this.assignedTo !== null) taskUpdatePayload.assigned_to = this.assignedTo;

                if (Object.keys(taskUpdatePayload).length > 0) {
                    const res = await this.$axios.put(`/tasks/${this.taskId}`, taskUpdatePayload);

                    if (res.data.assigned_to !== undefined) this.assignedTo = res.data.assigned_to;
                    if (res.data.due_date !== undefined) this.dueDate = res.data.due_date ? res.data.due_date.slice(0, 10) : "";

                    if (taskUpdatePayload.due_date) this.$refs.notifier.addNotification("Dátum odovzdania bol úspešne aktualizovaný", "success");
                    if (taskUpdatePayload.assigned_to !== undefined) this.$refs.notifier.addNotification("Priradenie úlohy bolo úspešne aktualizované", "success");

                    updatedFields.assigned_to = this.assignedTo;
                    updatedFields.due_date = this.dueDate;
                }

                if (this.status && this.status !== this.currentStatusId) {
                    try {
                        await this.$axios.patch(`/tasks/${this.taskId}/status`, {status_id: this.status});

                        const updatedTaskRes = await this.$axios.get(`/tasks/${this.taskId}`);
                        const updatedTask = updatedTaskRes.data;

                        updatedFields.status = updatedTask.status;
                        this.currentStatusId = this.status;

                        this.$refs.notifier.addNotification("Stav úlohy bol úspešne zmenený", "success");
                    } catch (err) {
                        console.error(err);
                        this.$refs.notifier.addNotification("Nepodarilo sa zmeniť stav úlohy", "error");
                    }
                }

                this.$emit("updated", {comment: newComment, time: newTime, updatedFields});
                this.$emit("close");
            } catch (err) {
                console.error(err);
                const msg = err.response?.data?.message || "Nepodarilo sa uložiť zmeny";
                this.$refs.notifier.addNotification(msg, "error");
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>
