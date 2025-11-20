<template>
    <RouterLink
        :to="`/task/${request.id}`"
        class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors"
    >
        <div class="flex-1 min-w-0">
            <h3 class="text-sm sm:text-base font-medium text-gray-900">
                {{ request.title }}
            </h3>
            <div class="flex items-center space-x-2 mt-2">
                <span
                    :class="`${request.status?.color_bg} ${request.status?.color_text} px-2 py-1 text-xs font-medium rounded-full`"
                >
                    {{ statusLabel }}
                </span>
                <span class="text-xs text-gray-500">
                    {{ formatDate(request.created_at) }}
                </span>
            </div>
        </div>
        <ChevronRight class="w-5 h-5 text-gray-400 flex-shrink-0 ml-3" />
    </RouterLink>
</template>


<script setup>
import { computed } from 'vue';
import { ChevronRight } from 'lucide-vue-next';
import { formatDate } from '../../js/composables/date';

const props = defineProps({
    request: {
        type: Object,
        required: true
    }
});

const statusLabel = computed(() => {
    return props.request.status?.label || 'Neznámy';
});
</script>
