<template>
    <div class="bg-white rounded-xl shadow p-5 border border-gray-100">
        <div
            class="flex justify-between items-center cursor-pointer"
            @click="expanded = !expanded"
        >
            <h2 class="text-lg font-semibold text-gray-800 mb-3">{{ title }}</h2>
            <span class="text-gray-400 transform transition-transform" :class="{'rotate-90': expanded}">
        ▶
      </span>
        </div>

        <div v-show="expanded" class="text-gray-600 text-sm mt-2">
            <slot />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    title: String,
    defaultExpanded: {
        type: Boolean,
        default: true,
    },
});

const expanded = ref(props.defaultExpanded);
</script>
