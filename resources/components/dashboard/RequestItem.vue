<template>
    <div class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
        <div class="flex-1 min-w-0">
            <h3 class="text-sm sm:text-base font-medium text-gray-900">
                {{ request.title }}
            </h3>
            <div class="flex items-center space-x-2 mt-2">
                <span :class="statusClasses" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ statusLabel }}
                </span>
                <span class="text-xs text-gray-500">
                    {{ formatDate(request.created) }}
                </span>
            </div>
        </div>
        <ChevronRight class="w-5 h-5 text-gray-400 flex-shrink-0 ml-3" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    request: {
        type: Object,
        required: true
    }
});

const statusMap = {
    pending: { label: 'Čaká sa', classes: 'bg-yellow-100 text-yellow-700' },
    in_progress: { label: 'V riešení', classes: 'bg-blue-100 text-blue-700' },
    completed: { label: 'Dokončené', classes: 'bg-green-100 text-green-700' },
    rejected: { label: 'Zamietnuté', classes: 'bg-red-100 text-red-700' }
};

const statusLabel = computed(() => statusMap[props.request.status]?.label || 'Neznámy');
const statusClasses = computed(() => statusMap[props.request.status]?.classes || 'bg-gray-100 text-gray-700');

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('sk-SK', { day: 'numeric', month: 'long' });
};
</script>
