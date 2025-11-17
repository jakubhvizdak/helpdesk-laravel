<template>
    <div
        class="fixed inset-0 flex items-center justify-center z-50 p-4"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden relative animate-fadeIn">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 7a2 2 0 012-2h5l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>                        </svg>
                    </div>

                    <h1 class="text-2xl font-bold text-white">
                        Správa projektov pre {{ user.name }} {{ user.surname }}
                    </h1>
                </div>

                <button
                    @click="$emit('close')"
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>


            <div class="overflow-y-auto px-6 py-4 flex-1">
                <div class="mb-4">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Hľadať projekt..."
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    />
                </div>

                <div v-if="loading" class="flex justify-center py-12">
                    <div class="w-14 h-14 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>

                <div v-else class="max-h-[calc(5*3.5rem)] overflow-y-auto space-y-3">
                    <div
                        v-for="project in filteredProjects"
                        :key="project.id"
                        class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer"
                    >
                        <input
                            type="checkbox"
                            :value="project.id"
                            v-model="selectedProjectIds"
                            class="w-5 h-5 accent-blue-600"
                        />
                        <span class="text-gray-900 font-medium">{{ project.name }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-white">
                <button
                    @click="$emit('close')"
                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors w-full sm:w-auto"
                >
                    Zrušiť
                </button>

                <button
                    @click="saveProjects"
                    :disabled="saving"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 w-full sm:w-auto"
                >
                    <span v-if="!saving">Uložiť</span>
                    <svg v-else class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../../js/api.js';

export default {
    name: 'EditUsersProjectModal',
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    inject: ['notify'],
    data() {
        return {
            projects: [],
            selectedProjectIds: [],
            loading: true,
            saving: false,
            search: ''
        };
    },
    computed: {
        filteredProjects() {
            return this.projects.filter(project =>
                project.name.toLowerCase().includes(this.search.toLowerCase())
            );
        }
    },
    async mounted() {
        await this.loadProjects();
    },
    methods: {
        async loadProjects() {
            this.loading = true;
            try {
                const res = await api.get('/api/projects');
                this.projects = res.data;
                this.selectedProjectIds = this.user.projects?.map(p => p.id) || [];
            } catch (e) {
                console.error('Chyba pri načítaní priradených projektov:', e);
                this.notify?.('Chyba pri načítaní priradených projektov', 'error');
            } finally {
                this.loading = false;
            }
        },
        async saveProjects() {
            this.saving = true;
            try {
                await api.put(`/api/users/${this.user.id}/projects`, {
                    project_ids: this.selectedProjectIds
                });
                this.$emit('projects-updated', this.selectedProjectIds);
                this.notify?.('Priradené projekty boli úspešne aktualizované.', 'success');
                this.$emit('close');
            } catch (e) {
                console.error('Chyba pri ukladaní priradených projektov:', e);
                this.notify?.('Chyba pri ukladaní priradených projektov.', 'error');
            } finally {
                this.saving = false;
            }
        }
    }
};
</script>
