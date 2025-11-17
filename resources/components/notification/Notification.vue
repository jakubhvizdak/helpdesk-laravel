<template>
    <transition name="fade">
        <div
            v-if="visible"
            :class="[
                'px-5 py-4 rounded-xl shadow-2xl text-white flex items-start gap-3 min-w-[320px] max-w-md backdrop-blur-sm pointer-events-auto',
                typeClass
            ]"
        >
            <div class="flex-shrink-0 mt-0.5">
                <svg v-if="type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <svg v-else-if="type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <svg v-else-if="type === 'warning'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>

            <span class="flex-1 text-sm font-medium leading-relaxed">{{ message }}</span>

            <button
                @click="visible = false"
                class="flex-shrink-0 text-white/60 hover:text-white transition-colors duration-200 -mt-0.5"
                aria-label="Zavrieť"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'Notification',
    props: {
        message: String,
        type: {
            type: String,
            default: 'success'
        },
        duration: {
            type: Number,
            default: 3000
        }
    },
    data() {
        return { visible: true };
    },
    computed: {
        typeClass() {
            switch (this.type) {
                case 'error':
                    return 'bg-red-500/95';
                case 'warning':
                    return 'bg-amber-500/95 text-gray-900';
                case 'info':
                    return 'bg-blue-500/95';
                default:
                    return 'bg-emerald-500/95';
            }
        }
    },
    mounted() {
        setTimeout(() => {
            this.visible = false;
        }, this.duration);
    }
};
</script>
