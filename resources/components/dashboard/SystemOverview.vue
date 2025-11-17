<template>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl">
            <div class="flex items-center justify-between mb-3">
                <Users class="w-8 h-8 text-blue-600" />
                <span class="text-xs font-semibold text-blue-600 bg-blue-200 px-2 py-1 rounded-full">
                    {{ Math.round((data.users.active / data.users.total) * 100) }}% aktívnych
                </span>
            </div>
            <div class="text-3xl font-bold text-blue-900 mb-1">{{ data.users.active }}</div>
            <div class="text-sm text-blue-700">Aktívni používatelia</div>
            <div class="text-xs text-blue-600 mt-2">z {{ data.users.total }} celkovo</div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-xl">
            <div class="flex items-center justify-between mb-3">
                <FolderOpen class="w-8 h-8 text-green-600" />
                <span class="text-xs font-semibold text-green-600 bg-green-200 px-2 py-1 rounded-full">
                    {{ Math.round((data.projects.active / data.projects.total) * 100) }}% aktívnych
                </span>
            </div>
            <div class="text-3xl font-bold text-green-900 mb-1">{{ data.projects.active }}</div>
            <div class="text-sm text-green-700">Aktívne projekty</div>
            <div class="text-xs text-green-600 mt-2">z {{ data.projects.total }} celkovo</div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-xl">
            <div class="flex items-center justify-between mb-3">
                <ClipboardList class="w-8 h-8 text-purple-600" />
                <span class="text-xs font-semibold text-purple-600 bg-purple-200 px-2 py-1 rounded-full">
                    {{ Math.round((data.tasks.open / (data.tasks.open + data.tasks.completed)) * 100) }}% otvorených
                </span>
            </div>
            <div class="text-3xl font-bold text-purple-900 mb-1">{{ data.tasks.open }}</div>
            <div class="text-sm text-purple-700">Otvorené úlohy</div>
            <div class="text-xs text-purple-600 mt-2">{{ data.tasks.completed }} dokončených</div>
        </div>
    </div>
</template>

<script setup>
import { Users, FolderOpen, ClipboardList } from 'lucide-vue-next';

defineProps({
    data: {
        type: Object,
        required: true
    }
});
</script>
