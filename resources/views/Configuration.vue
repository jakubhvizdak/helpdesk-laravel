<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
        <Navbar />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                            Konfigurácia – stavy úloh
                        </h1>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Spravujte prechody medzi stavmi úloh
                        </p>
                    </div>
                    <button
                        @click="openModal = true"
                        class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl hover:from-green-600 hover:to-green-700 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                    >
                        <span class="text-xl">+</span>
                        <span class="font-semibold">Pridať prechod</span>
                    </button>
                </div>
            </div>

            <div v-if="loading" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="text-gray-600 font-medium">Načítavam prechody...</p>
                </div>
            </div>

            <div v-else-if="transitions.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Zo stavu
                            </th>
                            <th class="px-4 sm:px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider w-16">
                                →
                            </th>
                            <th class="px-4 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Do stavu
                            </th>
                            <th class="px-4 sm:px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">
                                Akcie
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="t in transitions" :key="t.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 sm:px-6 py-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                :class="[t.from_status.color_bg, t.from_status.color_text]"
                            >
                                {{ t.from_status.label }}
                            </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-center">
                                <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                :class="[t.to_status.color_bg, t.to_status.color_text]"
                            >
                                {{ t.to_status.label }}
                            </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <button
                                    @click="confirmDelete(t.id)"
                                    class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 transition-colors"
                                    title="Vymazať"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Žiadne prechody</h3>
                    <p class="text-gray-600 mb-6">Začnite pridaním nového prechodu medzi stavmi</p>
                    <button
                        @click="openModal = true"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 inline-flex items-center gap-2 shadow-lg transition-all duration-300"
                    >
                        <span class="text-xl">+</span>
                        <span>Pridať prvý prechod</span>
                    </button>
                </div>
            </div>

            <AddStatusTransitionModal
                :visible="openModal"
                :statuses="statuses"
                @close="openModal = false"
                @save="saveTransition"
            />

            <ConfirmModal
                v-if="showConfirmModal"
                title="Odstrániť prechod"
                message="Naozaj chcete odstrániť tento prechod? Táto akcia je nevratná."
                @cancel="showConfirmModal = false"
                @confirm="deleteConfirmed"
            />

            <NotificationContainer ref="notifier" />
        </div>
    </div>
    ```

</template>

<script>
import { inject } from 'vue';
import Navbar from '../components/Navbar.vue';
import AddStatusTransitionModal from '../components/modals/AddStatusTransitionModal.vue';
import ConfirmModal from '../components/modals/ConfirmModal.vue';
import NotificationContainer from '../components/notification/NotificationContainer.vue';
import axios from 'axios';

export default {
    name: 'Configuration',
    components: {
        Navbar,
        AddStatusTransitionModal,
        ConfirmModal,
        NotificationContainer
    },
    setup() {
        const notify = inject('notify');
        return { notify };
    },
    data() {
        return {
            transitions: [],
            statuses: [],
            openModal: false,
            showConfirmModal: false,
            transitionToDelete: null,
            loading: true,
        };
    },
    async mounted() {
        await this.fetchStatuses();
        await this.fetchTransitions();
    },
    methods: {
        async fetchStatuses() {
            try {
                const res = await axios.get('/task-statuses');
                this.statuses = res.data;
            } catch (err) {
                console.error('Chyba pri načítaní stavov:', err);
                this.$refs.notifier?.addNotification('Nepodarilo sa načítať stavy.', 'error');
            }
        },
        async fetchTransitions() {
            try {
                this.loading = true;
                const res = await axios.get('/status-transitions');
                this.transitions = res.data.map(t => ({
                    ...t,
                    from_status: this.statuses.find(s => s.id === t.from_status.id) || t.from_status,
                    to_status: this.statuses.find(s => s.id === t.to_status.id) || t.to_status,
                }));
            } catch (err) {
                console.error('Chyba pri načítaní prechodov:', err);
                this.$refs.notifier?.addNotification('Nepodarilo sa načítať prechody.', 'error');
            } finally {
                this.loading = false;
            }
        },
        async saveTransition(form) {
            try {
                await axios.post('/status-transitions', form);
                this.$refs.notifier?.addNotification('Prechod bol úspešne pridaný.', 'success');
                this.openModal = false;
                await this.fetchTransitions();
            } catch (err) {
                console.error('Chyba pri ukladaní prechodu:', err);
                if (err.response?.data?.errors) {
                    Object.values(err.response.data.errors).flat().forEach(msg =>
                        this.$refs.notifier?.addNotification(msg, 'error')
                    );
                } else {
                    this.$refs.notifier?.addNotification('Nepodarilo sa uložiť prechod.', 'error');
                }
            }
        },
        confirmDelete(id) {
            this.transitionToDelete = id;
            this.showConfirmModal = true;
        },
        async deleteConfirmed() {
            try {
                await axios.delete(`/status-transitions/${this.transitionToDelete}`);
                this.$refs.notifier?.addNotification('Prechod bol úspešne vymazaný.', 'success');
                await this.fetchTransitions();
            } catch (err) {
                console.error('Chyba pri mazaní prechodu:', err);
                this.$refs.notifier?.addNotification('Nepodarilo sa vymazať prechod.', 'error');
            } finally {
                this.showConfirmModal = false;
                this.transitionToDelete = null;
            }
        },
    },
};
</script>
