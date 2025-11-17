<template>
    <div :class="cardClasses" class="group">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">
                    {{ title }}
                </p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900">
                    {{ value }}
                </p>
                <p v-if="trend" :class="trendClasses" class="text-xs sm:text-sm font-medium mt-2 flex items-center">
                    <component :is="trendIcon" class="w-4 h-4 mr-1" />
                    {{ trend }}
                </p>
            </div>
            <div :class="iconWrapperClasses">
                <component :is="iconComponent" class="w-6 h-6" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import {
    ClipboardList,
    CheckCircle,
    Clock,
    Folder,
    Users,
    FolderOpen,
    AlertCircle,
    MessageSquare,
    Timer,
    TrendingUp,
    TrendingDown,
    Minus
} from 'lucide-vue-next';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'blue'
    },
    trend: {
        type: String,
        default: null
    }
});

const iconMap = {
    ClipboardList,
    CheckCircle,
    Clock,
    Folder,
    Users,
    FolderOpen,
    AlertCircle,
    MessageSquare,
    Timer
};

const iconComponent = computed(() => iconMap[props.icon] || ClipboardList);

const colorClasses = {
    blue: 'from-blue-500 to-blue-600',
    green: 'from-green-500 to-green-600',
    purple: 'from-purple-500 to-purple-600',
    orange: 'from-orange-500 to-orange-600',
    red: 'from-red-500 to-red-600'
};

const cardClasses = computed(() => [
    'bg-white rounded-xl sm:rounded-2xl shadow-sm hover:shadow-xl',
    'p-5 sm:p-6 transition-all duration-300 border border-gray-100',
    'cursor-pointer'
]);

const iconWrapperClasses = computed(() => [
    'p-3 rounded-xl bg-gradient-to-br text-white',
    'group-hover:scale-110 transition-transform duration-300',
    colorClasses[props.color] || colorClasses.blue
]);

const trendIcon = computed(() => {
    if (!props.trend) return Minus;
    if (props.trend.startsWith('+')) return TrendingUp;
    if (props.trend.startsWith('-')) return TrendingDown;
    return Minus;
});

const trendClasses = computed(() => {
    if (!props.trend) return 'text-gray-500';
    if (props.trend.startsWith('+')) return 'text-green-600';
    if (props.trend.startsWith('-')) return 'text-red-600';
    return 'text-gray-500';
});
</script>
