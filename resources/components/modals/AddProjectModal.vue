<template>
    <div
        class="fixed inset-0 flex items-center justify-center z-50 p-4 bg-black/40"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden relative animate-fadeIn">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <FolderPlus class="w-6 h-6 text-white"/>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-white">
                        Nový projekt
                    </h2>
                </div>
                <button
                    @click="$emit('close')"
                    class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto max-h-[calc(90vh-80px)] custom-scrollbar">
                <form @submit.prevent="submit" novalidate>
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-700">Základné informácie</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    Názov projektu <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                    placeholder="Názov projektu"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">Popis</label>
                                <textarea
                                    v-model="form.description"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none shadow-sm"
                                    placeholder="Popis projektu"
                                    rows="4"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-700">Finančné nastavenia</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    Celkový počet hodín
                                </label>
                                <input
                                    v-model="form.hours_total"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                    placeholder="20"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    Hodinová sadzba (€)
                                </label>
                                <input
                                    v-model="form.hourly_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                                    placeholder="35.00"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 px-6 pb-6 pt-2">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            <span v-if="!loading">Pridať projekt</span>
                            <span v-else>Pridávam...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import api from '@/api.js';
import { inject } from 'vue';
import { FolderPlus } from 'lucide-vue-next';

export default {
    name: 'AddProjectModal',
    components: { FolderPlus },
    setup() {
        const notify = inject('notify');
        return { notify };
    },
    data() {
        return {
            loading: false,
            form: {
                name: '',
                description: '',
                hours_total: null,
                hourly_price: null,
            },
        };
    },
    methods: {
        async submit() {
            if (!this.form.name.trim()) {
                this.notify?.('Názov projektu je povinný.', 'error');
                return;
            }

            this.loading = true;

            try {
                const res = await api.post('/api/projects', this.form, { withCredentials: true });

                this.$emit('project-added', res.data.project);

                this.form = { name: '', description: '', hours_total: null, hourly_price: null };
                this.$emit('close');
            } catch (error) {
                console.error('Chyba pri pridávaní projektu:', error.response?.data || error);

                if (error.response?.data?.errors) {
                    const messages = Object.values(error.response.data.errors).flat();
                    messages.forEach(msg => this.notify?.(msg, 'error'));
                } else if (error.response?.data?.message) {
                    this.notify?.(error.response.data.message, 'error');
                } else {
                    this.notify?.('Neznáma chyba pri pridávaní projektu.', 'error');
                }
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
