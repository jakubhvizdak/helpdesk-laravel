<template>
    <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg">
        <div class="flex items-start justify-between mb-2">
            <h4 class="text-sm font-semibold text-gray-900">{{ response.request }}</h4>
            <span class="text-xs text-gray-500 ml-2 flex-shrink-0">{{ response.time }}</span>
        </div>
        <p class="text-sm text-gray-700">{{ response.message }}</p>
    </div>
</template>

<script setup>
defineProps({
    response: {
        type: Object,
        required: true
    }
});
</script>
