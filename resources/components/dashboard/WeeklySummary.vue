<template>
    <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <CheckCircle class="w-5 h-5 text-green-600" />
                </div>
                <span class="text-sm font-medium text-gray-700">Dokončené úlohy</span>
            </div>
            <span class="text-lg font-bold text-green-700">{{ data.tasksCompleted }}</span>
        </div>

        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <Folder class="w-5 h-5 text-blue-600" />
                </div>
                <span class="text-sm font-medium text-gray-700">Aktívne projekty</span>
            </div>
            <span class="text-lg font-bold text-blue-700">{{ data.projects }}</span>
        </div>

        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <Users class="w-5 h-5 text-purple-600" />
                </div>
                <span class="text-sm font-medium text-gray-700">Stretnutia</span>
            </div>
            <span class="text-lg font-bold text-purple-700">{{ data.meetings }}</span>
        </div>
    </div>
</template>

<script setup>
import { CheckCircle, Folder, Users } from 'lucide-vue-next';

defineProps({
    data: {
        type: Object,
        required: true
    }
});
</script>
