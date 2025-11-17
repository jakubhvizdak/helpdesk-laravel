<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                            Správa používateľov
                        </h1>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Spravujte používateľov a ich role
                        </p>
                    </div>
                    <button
                        @click="showAddModal = true"
                        class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl hover:from-green-600 hover:to-green-700 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                    >
                        <UserPlus class="w-5 h-5" />
                        <span class="font-semibold">Pridať používateľa</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 sm:mb-8">
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Celkovo používateľov</p>
                            <p class="text-2xl font-bold text-gray-900">{{ users.length }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <Users class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Administrátori</p>
                            <p class="text-2xl font-bold text-gray-900">{{ getUsersByRole('admin') }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <Shield class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Zákazníci</p>
                            <p class="text-2xl font-bold text-gray-900">{{ getUsersByRole('customer') }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <UserCheck class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    </div>
                    <p class="text-gray-600 font-medium">Načítavam používateľov...</p>
                </div>
            </div>

            <div v-else-if="users.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Meno</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden sm:table-cell">Priezvisko</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Rola</th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Akcie</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">#{{ user.id }}</span>
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <input
                                    v-model="user.name"
                                    class="border border-gray-300 rounded-lg px-2 sm:px-3 py-2 w-full min-w-[100px] focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                                    placeholder="Meno"
                                />
                            </td>
                            <td class="px-3 sm:px-6 py-4 hidden sm:table-cell">
                                <input
                                    v-model="user.surname"
                                    class="border border-gray-300 rounded-lg px-3 py-2 w-full min-w-[100px] focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                                    placeholder="Priezvisko"
                                />
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <input
                                    v-model="user.email"
                                    type="email"
                                    class="border border-gray-300 rounded-lg px-2 sm:px-3 py-2 w-full min-w-[150px] focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                                    placeholder="email@example.com"
                                />
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <select
                                    v-model="user.role"
                                    class="border border-gray-300 rounded-lg px-2 sm:px-3 py-2 w-full min-w-[100px] focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                                >
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <button
                                        @click="saveUser(user)"
                                        :disabled="savingUserId === user.id"
                                        class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        title="Uložiť"
                                    >
                                        <Save class="w-4 h-4" v-if="savingUserId !== user.id" />
                                        <Loader class="w-4 h-4 animate-spin" v-else />
                                    </button>

                                    <button
                                        @click="confirmDelete(user.id)"
                                        class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 transition-colors"
                                        title="Odstrániť"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>

                                    <button
                                        @click="editUserProjects(user)"
                                        class="bg-yellow-500 text-white p-2 rounded-lg hover:bg-yellow-600 transition-colors"
                                        title="Spravovať projekty"
                                    >
                                        <Folder class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <Users class="w-8 h-8 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Žiadni používatelia</h3>
                    <p class="text-gray-600 mb-6">Začnite pridaním nového používateľa do systému</p>
                    <button
                        @click="showAddModal = true"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 inline-flex items-center gap-2 shadow-lg transition-all duration-300"
                    >
                        <UserPlus class="w-5 h-5" />
                        <span>Pridať prvého používateľa</span>
                    </button>
                </div>
            </div>

            <AddUserModal
                v-if="showAddModal"
                @close="showAddModal = false"
                @user-added="reloadUsers"
            />

            <ConfirmModal
                v-if="showConfirmModal"
                title="Odstrániť používateľa"
                message="Naozaj chcete odstrániť tohto používateľa? Táto akcia je nevratná."
                @cancel="showConfirmModal = false"
                @confirm="deleteConfirmed"
            />

            <EditUsersProjectModal
                v-if="showEditProjectsModal"
                :user="selectedUserForProjects"
                @close="showEditProjectsModal = false"
                @projects-updated="onProjectsUpdated"
            />
        </div>
    </div>
</template>

<script>
import { inject } from 'vue';
import { Users, UserPlus, Shield, UserCheck, Save, Trash2, Loader, Folder } from 'lucide-vue-next';
import api from '../js/api.js';
import AddUserModal from '../components/modals/AddUserModal.vue';
import ConfirmModal from '../components/modals/ConfirmModal.vue';
import EditUsersProjectModal from '../components/modals/EditUsersProjectsModal.vue';
import Navbar from '../components/Navbar.vue';

export default {
    name: 'UserManagement',
    components: {
        Navbar,
        AddUserModal,
        ConfirmModal,
        EditUsersProjectModal,
        Users,
        UserPlus,
        Shield,
        UserCheck,
        Save,
        Trash2,
        Loader,
        Folder
    },

    setup() {
        const notify = inject('notify');
        return { notify };
    },

    data() {
        return {
            users: [],
            showAddModal: false,
            showConfirmModal: false,
            showEditProjectsModal: false,
            selectedUserForProjects: null,
            userToDelete: null,
            loading: true,
            savingUserId: null,
        };
    },

    async mounted() {
        await this.reloadUsers();
    },

    methods: {
        async reloadUsers() {
            this.loading = true;
            try {
                const res = await api.get('/api/users');
                this.users = res.data;
            } catch (e) {
                console.error('Chyba pri načítaní používateľov:', e);
                this.notify?.('Chyba pri načítaní používateľov', 'error');
            } finally {
                this.loading = false;
            }
        },

        async saveUser(user) {
            this.savingUserId = user.id;
            try {
                const payload = {
                    name: user.name,
                    surname: user.surname,
                    email: user.email,
                    role: user.role,
                    password: user.password || null,
                };
                await api.put(`/api/users/${user.id}`, payload);
                this.notify('Užívateľ bol uložený.', 'success');
                await this.reloadUsers();
            } catch (e) {
                console.error('Chyba pri ukladaní užívateľa:', e);
                if (e.response?.data?.errors) {
                    Object.values(e.response.data.errors).flat().forEach(msg => this.notify(msg, 'error'));
                } else {
                    this.notify('Chyba pri ukladaní užívateľa.', 'error');
                }
            } finally {
                this.savingUserId = null;
            }
        },

        confirmDelete(id) {
            this.userToDelete = id;
            this.showConfirmModal = true;
        },

        async deleteConfirmed() {
            try {
                await api.delete(`/api/users/${this.userToDelete}`);
                this.notify?.('Užívateľ bol odstránený.', 'success');
                await this.reloadUsers();
            } catch (e) {
                console.error('Chyba pri odstraňovaní používateľa:', e);
                this.notify?.('Chyba pri odstraňovaní používateľa.', 'error');
            } finally {
                this.showConfirmModal = false;
                this.userToDelete = null;
            }
        },

        getUsersByRole(role) {
            return this.users.filter(u => u.role === role).length;
        },

        editUserProjects(user) {
            this.selectedUserForProjects = user;
            this.showEditProjectsModal = true;
        },

        onProjectsUpdated() {
            this.showEditProjectsModal = false;
            this.selectedUserForProjects = null;
            this.reloadUsers();
        }
    },
};
</script>
