<template>
    <div
        class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors group"
    >
        <div class="flex items-start space-x-3 flex-1 min-w-0">
            <div class="flex-shrink-0 mt-1">
                <div
                    class="w-5 h-5 rounded border-2 border-gray-300 group-hover:border-blue-500 transition-colors cursor-pointer"
                ></div>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm sm:text-base font-medium text-gray-900 truncate">
                    {{ task.title }}
                </h3>
                <div class="flex flex-wrap items-center gap-2 mt-2">
          <span class="text-xs text-gray-500">
            {{ task.project?.name || 'Bez projektu' }}
          </span>
                    <span class="flex items-center text-xs text-gray-500">
            <Calendar class="w-3 h-3 mr-1" />
            {{ formatDate(task.due_date) }}
          </span>
                </div>
            </div>
        </div>
        <button class="ml-3 p-2 rounded-lg hover:bg-white transition-colors">
            <ChevronRight class="w-5 h-5 text-gray-400" />
        </button>
    </div>
</template>

<script setup>
import { Calendar, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
});

const formatDate = (dateString) => {
    if (!dateString) return 'Bez termínu';
    const date = new Date(dateString);
    const today = new Date();
    const diffDays = Math.ceil((date - today) / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Dnes';
    if (diffDays === 1) return 'Zajtra';
    if (diffDays < 0) return 'Po termíne';

    return date.toLocaleDateString('sk-SK', { day: 'numeric', month: 'numeric', year: 'numeric' });
};
</script>
