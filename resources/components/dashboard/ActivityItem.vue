<template>
    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors">
        <div :class="iconWrapperClasses">
            <component :is="activityIcon" class="w-4 h-4 text-white" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-900">
                {{ activity.message }}
            </p>
            <div class="flex items-center space-x-2 mt-1">
                <span class="text-xs text-gray-500">{{ activity.user }}</span>
                <span class="text-xs text-gray-400">•</span>
                <span class="text-xs text-gray-500">{{ activity.time }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { UserPlus, CheckCircle, FolderPlus, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    activity: {
        type: Object,
        required: true
    }
});

const activityTypeMap = {
    user_added: { icon: UserPlus, color: 'bg-blue-500' },
    task_completed: { icon: CheckCircle, color: 'bg-green-500' },
    project_created: { icon: FolderPlus, color: 'bg-purple-500' },
    alert: { icon: AlertCircle, color: 'bg-red-500' }
};

const activityIcon = computed(() => {
    return activityTypeMap[props.activity.type]?.icon || AlertCircle;
});

const iconWrapperClasses = computed(() => {
    const baseClasses = 'flex-shrink-0 p-2 rounded-lg';
    const colorClass = activityTypeMap[props.activity.type]?.color || 'bg-gray-500';
    return `${baseClasses} ${colorClass}`;
});
</script>
