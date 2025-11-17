<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Správa projektov</h1>
                    <p class="text-gray-600 text-sm sm:text-base">Spravujte projekty a ich priradených používateľov</p>
                </div>
                <button @click="showAddModal = true"
                        class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl hover:from-green-600 hover:to-green-700 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <FolderPlus class="w-5 h-5" /> Pridať projekt
                </button>
            </div>

            <div v-if="loading" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                <p class="text-gray-600 font-medium">Načítavam projekty...</p>
            </div>

            <div v-else-if="projects.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Názov</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Popis</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Servisné hodiny</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Hodinová sadzba(€)</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Akcie</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="project in projects" :key="project.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">#{{ project.id }}</td>
                            <td class="px-3 sm:px-6 py-4">
                                <input v-model="project.name" class="border border-gray-300 rounded-lg px-2 py-2 w-full text-sm" />
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <input v-model="project.description" class="border border-gray-300 rounded-lg px-2 py-2 w-full text-sm" />
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <input v-model="project.service_hours.hours_total" type="number" step="0.01"
                                       class="border border-gray-300 rounded-lg px-2 py-2 w-full text-sm" />
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <input v-model="project.service_hours.hourly_price" type="number" step="0.01"
                                       class="border border-gray-300 rounded-lg px-2 py-2 w-full text-sm" />
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <button @click="saveProject(project)" :disabled="savingProjectId === project.id"
                                            class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <Save class="w-4 h-4" v-if="savingProjectId !== project.id"/>
                                        <Loader class="w-4 h-4 animate-spin" v-else/>
                                    </button>

                                    <button @click="confirmDelete(project.id)"
                                            class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 transition-colors">
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <Folder class="w-8 h-8 text-gray-400"/>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Žiadne projekty</h3>
                <p class="text-gray-600 mb-6">Začnite pridaním nového projektu do systému</p>
                <button @click="showAddModal = true" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl inline-flex items-center gap-2 shadow-lg transition-all duration-300">
                    <FolderPlus class="w-5 h-5"/> Pridať projekt
                </button>
            </div>

            <AddProjectModal
                v-if="showAddModal"
                @close="showAddModal = false"
                @project-added="onProjectAdded"
            />
            <ConfirmModal
                v-if="showConfirmModal"
                title="Odstrániť projekt"
                message="Naozaj chcete odstrániť tento projekt? Táto akcia je nevratná."
                @cancel="showConfirmModal = false"
                @confirm="deleteConfirmed"
            />
        </div>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import { Folder, FolderPlus, Save, Trash2, Loader, User } from 'lucide-vue-next';
import api from '../js/api.js';
import AddProjectModal from '../components/modals/AddProjectModal.vue';
import ConfirmModal from '../components/modals/ConfirmModal.vue';

export default {
    name: 'ProjectManagement',
    components: { Navbar, Folder, FolderPlus, Save, Trash2, Loader, User, AddProjectModal, ConfirmModal },
    inject: ['notify'],
    data() {
        return {
            projects: [],
            loading: true,
            savingProjectId: null,
            showAddModal: false,
            showConfirmModal: false,
            projectToDelete: null,
        };
    },
    async mounted() {
        await this.reloadProjects();
    },
    methods: {
        async reloadProjects() {
            this.loading = true;
            try {
                const res = await api.get('/api/projects');

                this.projects = res.data.map(p => ({
                    ...p,
                    service_hours: {
                        hours_total: parseFloat(p.service_hours?.hours_total ?? 0),
                        hourly_price: parseFloat(p.service_hours?.hourly_price ?? 0),
                    }
                }));

            } catch (err) {
                console.error('Chyba pri načítaní projektov:', err);
                this.notify?.('Chyba pri načítaní projektov', 'error');
            } finally {
                this.loading = false;
            }
        },

        async saveProject(project) {
            this.savingProjectId = project.id;
            try {
                await api.put(`/api/projects/${project.id}`, {
                    name: project.name,
                    description: project.description,
                    hours_total: project.service_hours?.hours_total,
                    hourly_price: project.service_hours?.hourly_price,
                });

                this.notify?.('Projekt bol úspešne upravený.', 'success');
                await this.reloadProjects();
            } catch (err) {
                console.error('Chyba pri ukladaní projektu:', err);
                this.notify?.('Chyba pri ukladaní projektu.', 'error');
            } finally {
                this.savingProjectId = null;
            }
        },

        confirmDelete(id) {
            this.projectToDelete = id;
            this.showConfirmModal = true;
        },

        async deleteConfirmed() {
            this.showConfirmModal = false;
            try {
                await api.delete(`/api/projects/${this.projectToDelete}`);
                this.notify?.('Projekt bol úspešne vymazaný.', 'success');
                await this.reloadProjects();
            } catch (err) {
                console.error('Chyba pri odstraňovaní projektu:', err);
                this.notify?.('Chyba pri odstraňovaní projektu.', 'error');
            } finally {
                this.projectToDelete = null;
            }
        },

        onProjectAdded() {
            this.notify?.('Projekt bol úspešne vytvorený.', 'success');
            this.showAddModal = false;
            this.reloadProjects();
        }
    },
};
</script>
