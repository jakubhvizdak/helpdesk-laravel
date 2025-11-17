<template>
    <div class="space-y-4">
        <div class="grid grid-cols-3 gap-3">
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                <div class="text-2xl font-bold text-blue-700">{{ hours.today }}h</div>
                <div class="text-xs text-blue-600 mt-1">Dnes</div>
            </div>

            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                <div class="text-2xl font-bold text-purple-700">{{ hours.week }}h</div>
                <div class="text-xs text-purple-600 mt-1">Tento týždeň</div>
            </div>

            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                <div class="text-2xl font-bold text-green-700">{{ hours.month }}h</div>
                <div class="text-xs text-green-600 mt-1">Tento mesiac</div>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Týždenný cieľ</span>
                <span class="text-sm font-semibold text-gray-900">{{ hours.week }}/40h</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-500"
                    :style="{ width: progressWidth + '%' }"
                ></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    hours: {
        type: Object,
        required: true,
        default: () => ({ today: 0, week: 0, month: 0 })
    }
});

const progressWidth = computed(() => {
    return Math.min((props.hours.week / 40) * 100, 100);
});
</script>
