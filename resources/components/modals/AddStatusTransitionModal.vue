<template>
    <transition name="modal">
        <div v-if="visible" class="fixed inset-0 flex items-center justify-center z-50 p-4 bg-black/50" @click.self="$emit('close')">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col animate-fadeIn">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-white">Pridať prechod</h2>
                    </div>
                    <button
                        @click="$emit('close')"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span>Zo stavu</span>
                        </label>
                        <select
                            v-model="form.from_status_id"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                            required
                        >
                            <option value="" disabled>Vyberte stav</option>
                            <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.label }}</option>
                        </select>
                    </div>

                    <div class="flex justify-center">
                        <div class="p-2 bg-gray-100 rounded-full">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Do stavu</span>
                        </label>
                        <select
                            v-model="form.to_status_id"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm"
                            required
                        >
                            <option value="" disabled>Vyberte stav</option>
                            <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.label }}</option>
                        </select>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                            <span v-if="!loading">Uložiť prechod</span>
                            <span v-else>Ukladám...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'AddStatusTransitionModal',
    props: {
        visible: {
            type: Boolean,
            default: false
        },
        statuses: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            form: {
                from_status_id: '',
                to_status_id: ''
            },
            loading: false
        };
    },
    watch: {
        visible(newVal) {
            if (newVal) {
                this.form = {
                    from_status_id: '',
                    to_status_id: ''
                };
            }
        }
    },
    methods: {
        async submit() {
            this.loading = true;
            try {
                this.$emit('save', this.form);
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>
